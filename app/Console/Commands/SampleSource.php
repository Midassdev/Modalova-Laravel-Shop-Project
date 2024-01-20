<?php

namespace App\Console\Commands;

use Ajgl\Csv\Rfc;
use App\Models\Fetchers\CSV;
use App\Models\Product;
use App\Models\Source;
use Illuminate\Console\Command;

class SampleSource extends Command
{
    protected $signature = 'sample:source {source} {--skip=0} {--quantity=1}';

    protected $description = 'Parse an sample product from specified Source';

    public function handle()
    {
        $opts_source = $this->argument('source');
        $opts_skip = intval($this->option('skip'));
        $opts_quantity = intval($this->option('quantity'));

        $source = Source::find($opts_source);

        echo "[+] Downloading from: $source->name\n{$source->path}\n\n";

        if (! $process = proc_open('timeout 30s '.$source->download_feed_command(), [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes)) {
            echo "[-] ERROR: Could not open handle ($process)";
            return false;
        }
        stream_set_blocking($pipes[2], false);

        $handle = $pipes[1];

        $parser = $source->getParser();
        echo '[+] Parser: '.get_class($parser)."\n";

        $fetcher = $source->getFetcher($handle);
        echo '[+] Fetcher: '.get_class($fetcher)."\n";

        if ($headers = @$fetcher->headers) {
            echo "\n\$headers = '".implode(',', $headers)."';\n\n";
        }

        try {
            $fetcher->parse(function ($row, $raw) use ($source, $parser, $opts_skip, $opts_quantity, $pipes) {
                static $i = -1;
                $i++;

                if ($i < $opts_skip) {
                    return;
                }

                static $j = -1;
                $j++;
                if ($j >= $opts_quantity) {
                    return false;
                }

                if (! $data = $parser->parse_row($row)) {
                    echo "[!] Failed to parse:\n".print_r($row, true).".\n";
                    echo "[!] Failed to parse (raw):\n".print_r($raw, true)."\n";
                    echo "[!] Reasons_for_skipping: ".print_r($source->reasons_for_skipping, true);
                    echo "[!] Command output: " . (isset($pipes[2]) ? trim(stream_get_contents($pipes[2])) : "<empty>");
                    return false;
                }

                echo "\$headers = '".implode(',', array_keys($row))."';\n\n";
                echo "\$payload = '".base64_encode(json_encode($raw))."';\n\n";

                $attrs = CSV::array_combine($source::$columns, $data);
                $product = new Product($attrs);

                $v = $product->buildDocument();
                foreach ($v as $key => $value) {
                    if (is_array($value)) {
                        unset($v[$key]);
                    }
                }

                echo '$expected_value = '.var_export($v, true).";\n\n";

                echo 'DATA : '.print_r($row, true)."\n\n";
            });
        } catch (\Exception $e) {
            print_r([
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        echo "[=] Command output: " . (isset($pipes[2]) ? trim(stream_get_contents($pipes[2])) : "<empty>");
        echo "\n";

        pclose($handle);
    }
}
