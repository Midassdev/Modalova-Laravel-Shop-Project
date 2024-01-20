<?php

namespace App\Models;

use Ajgl\Csv\Rfc;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

use App\Notifications\BigDeltaDuringImport;

function local_debug() {
    global $process_download, $process_sql, $pipes_sql, $pipes_download;

    return [
        'processes' => [
            'process_sql' => is_resource($process_sql) ? proc_get_status($process_sql) : "<not a resource>",
            'process_download' => is_resource($process_download) ? proc_get_status($process_download) : "<not a resource>",
        ],
        'outputs' => [
            'pipes_sql[1]' => (isset($pipes_sql[1]) && is_resource($pipes_sql[1])) ? trim(stream_get_contents($pipes_sql[1])) : "<empty>",
            'pipes_sql[2]' => (isset($pipes_sql[2]) && is_resource($pipes_sql[2])) ? trim(stream_get_contents($pipes_sql[2])) : "<empty>",
            'pipes_download[2]' => (isset($pipes_download[2]) && is_resource($pipes_download[2])) ? trim(stream_get_contents($pipes_download[2])) : "<empty>",
        ],
    ];
}

if ('testing' != env('APP_ENV') && app()->hasDebugModeEnabled() && app()->runningInConsole()) {
    $process_download = null;
    $process_sql = null;
    $pipes_sql = [];
    $pipes_download = [];

    declare(ticks = 1);

    $signal_handler = function (int $signal, mixed $siginfo) {
        echo "Caught $signal\n";

        var_dump(local_debug());

        exit(1);
    };

    pcntl_signal(SIGTERM, $signal_handler);
    pcntl_signal(SIGINT, $signal_handler);
}

class Source extends BaseModel implements Sortable
{
    use SortableTrait;

    const CONFIG_TIMEOUT = 'timeout';
    const CONFIG_FETCHER = 'fetcher';
    const CONFIG_USE_WGET = 'use wget';
    const CONFIG_USE_TIMEOUT_COMMAND = 'use timeout command';
    const CONFIG_COL_SEPARATOR = 'col_separator';
    const CONFIG_CSV_HEADERS = 'csv - headers';
    const CONFIG_XML_UNIQUENODE = 'xml - uniqueNode';
    const CONFIG_XML_NAMESPACES = 'xml - nameSpaces';
    const CONFIG_FORCE_BRAND_NAME = 'force - brand name';
    const CONFIG_FORCE_GENDER = 'force - gender';
    const CONFIG_FIX_UTF8 = 'fix - utf8';
    const CONFIG_APPEND_CATEGORY = 'append - category';
    const CONFIG_TRANSFORM_URL = 'transform - url';
    const CONFIG_STR_REPLACE_IMAGE = 'str_replace - image_url';
    const CONFIG_STR_REPLACE_BRAND = 'str_replace - brand_original';
    const CONFIG_CONVERT_CURRENCY_FROM = 'convert - currency - from';
    const CONFIG_PRESTASHOP_LANGUAGE = 'prestashop - language';
    const CONFIG_PRESTASHOP_LANGUAGE_ID = 'prestashop - language id';
    const CONFIG_PRESTASHOP_IMAGE_TYPE = 'prestashop - image type';
    const CONFIG_DEBUG_SHOW_BRANDS_ADDED = 'debug - show brands added';
    const CONFIG_DEBUG_SHOW_CATEGORIES_ADDED = 'debug - show categories added';
    const CONFIG_DEBUG_STORE_PAYLOAD = 'debug - store payload';

    public $guarded = [];

    public $col_sep = ',';
    // TODO: rename this column_seperator

    protected $attributes = [
        'enabled' => false,
        'nb_of_products' => 0,
    ];

    public $sortable = [
        'order_column_name' => 'priority',
        'sort_when_creating' => true,
    ];

    public static $headers = null;

    public static $columns = [
        'name',
        'slug',
        'description',
        'price',
        'old_price',
        'reduction',
        'url',
        'merchant_original',
        'brand_original',
        'category_original',
        'gender',
        'currency_original',
        'color_original',
        'image_url',
        'provider',
        'col',
        'coupe',
        'manches',
        'material',
        'model',
        'motifs',
        'event',
        'style',
        'size',
        'livraison',
        'payload',
        'i',
    ];

    protected $casts = [
        'config' => 'json',
        'mapping' => 'json',
    ];

    const PARSERS = [
        Parsers\Awin::class,
        Parsers\CJ::class,
        Parsers\Partnerize::class,
        Parsers\NetaffiliationV3::class,
        Parsers\NetaffiliationV4::class,
        Parsers\Tradedoubler::class,
        Parsers\Tradetracker::class,
        Parsers\Rakuten::class,
        Parsers\Daisycon::class,
        Parsers\Effiliation::class,
        Parsers\Shopify::class,
        Parsers\Woocommerce::class,
        Parsers\Flexoffers::class,
        Parsers\Wix::class,
        Parsers\Prestashop::class,
        Parsers\Impact::class,
        Parsers\Affilae::class,
        Parsers\Shareasale::class,
        Parsers\Paidonresults::class,
        Parsers\Admitad::class,
        Parsers\Kelkoo::class,
    ];

    private static $i = 1;

    public $reasons_for_skipping = [];
    public $categories_added = [];
    public $brands_added = [];

    public static function getColumnsAsString()
    {
        return implode(',', static::$columns);
    }

    public function getTable()
    {
        if (! isset($this->table)) {
            return str_replace('\\', '', Str::snake(Str::plural(class_basename(self::class))));
        }

        return $this->table;
    }

    public function save(array $options = [])
    {
        if (empty($this->title)) {
            $this->title = $this->name;
        }

        if (empty($this->parser)) {
            $this->parser = trim(explode(' ', $this->name)[0]);
        }

        $this->config = array_filter($this->config ?: []);
        $this->mapping = array_filter($this->mapping ?: []);

        return parent::save($options);
    }

    public function psql_command($new_table)
    {
        return 'psql "'.config('database.database_url').'" -c '.
            '"COPY '.$new_table.'('.self::getColumnsAsString().") FROM STDIN DELIMITER ',' CSV".'"';
    }

    public function download_feed_command()
    {
        $timeout = ($this->config[self::CONFIG_TIMEOUT] ?? 15);
        $timeout_in_sec = $timeout * 60;

        $command = "curl --compressed --globoff --header 'user-agent: modalova' --insecure --cookie 'allow-download=1' --location ";
        $command .= " --max-time $timeout_in_sec ";

        if ('rakuten' == $this->parser) {
            $command .= ' --ignore-content-length ';
        }

        if ('kelkoo' == $this->parser) {
            $command .= " -H 'Authorization: Bearer " . env('KELKOO_API_TOKEN') . "'";
        }

        if (boolval(@$this->config[self::CONFIG_USE_WGET])) {
            $command = "wget -O - --no-check-certificate --header 'Cookie: allow-download=1' --user-agent modalova --compression auto";
            $command .= " --timeout $timeout_in_sec ";

            if ('rakuten' == $this->parser) {
                $command .= ' --ignore-length ';
            }
        }

        $command .= ' '.escapeshellarg($this->path);

        if (false !== strpos($this->path, 'compression/gzip') || false !== strpos($this->path, '.gz')) {
            $command .= ' | gunzip';
        }
        if (false !== strpos($this->path, '.zip')) {
            $command .= ' | funzip';
        }

        if ($fix_utf8 = boolval(@$this->config[self::CONFIG_FIX_UTF8])) {
            $command .= ' | iconv -c -f UTF-8 -t ISO-8859-1 ';
        }

        if (boolval(@$this->config[self::CONFIG_USE_TIMEOUT_COMMAND])) {
            $command = 'timeout '.$timeout.' '. $command;
        }

        return $command;
    }

    public static function array_combine($keys, $values)
    {
        error_log('Source::array_combine has been deprecated');

        return Fetchers\CSV::array_combine($keys, $values);
    }

    public function getHeaders()
    {
        $headers = @$this->getParser()::$headers;

        if ($_headers = @$this->config[self::CONFIG_CSV_HEADERS]) {
            $headers = mb_split('[,;|]', $_headers);
        }

        return $headers;
    }

    public function getFetcher($handle)
    {
        if (false !== strpos($this->path, '.tsv'))
            $this->col_sep = "\t";
        elseif (false !== strpos($this->path, '.csv'))
            $this->col_sep = ",";

        if ($col_sep = @$this->config[self::CONFIG_COL_SEPARATOR]) {
            $this->col_sep = $col_sep;
        }

        $fetcher = Fetchers\CSV::class;

        if (false !== strpos($this->path, '.json')
            || false !== stripos($this->path, '=json'))
            $fetcher = Fetchers\JSON::class;
        elseif (false !== stripos($this->name, 'xml')
            || false !== stripos($this->path, '.xml')
            || false !== stripos($this->path, '=xml'))
            $fetcher = Fetchers\XML::class;

        if ($_fetcher = @$this->config[self::CONFIG_FETCHER])
            $fetcher = $_fetcher;

        switch ($fetcher) {
            case Fetchers\Json::class:
                return new $fetcher($handle);

            case Fetchers\XML::class:
                return new $fetcher($handle, [
                    'uniqueNode' => @$this->config[self::CONFIG_XML_UNIQUENODE],
                    'nameSpaces' => @$this->config[self::CONFIG_XML_NAMESPACES],
                ]);

            default:
                return new $fetcher($handle, [
                    'col_sep' => $this->col_sep,
                    'headers' => $this->getHeaders(),
                ]);
        }
    }

    public function getParser() {
        foreach (self::PARSERS as $parser_class)
            if(strtolower((new \ReflectionClass($parser_class))->getShortName()) == $this->parser)
                return new $parser_class($this);

        throw new \Exception("[-] ERROR: Could not parser for this [$this->parser]");
    }

    public function import($new_table)
    {
        global $process_download, $process_sql, $pipes_sql, $pipes_download;

        $progress_good = 0;
        $progress_bad = 0;
        $last_row = null;
        $last_row_parsed = [];

        $eta = -hrtime(true);
        \Log::info("[+] Importing ($this->id) '$this->name' into '$new_table'");

        $start = date(DATE_RSS);

        if (! $process_download = proc_open($this->download_feed_command(), [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes_download)) {
            \Log::err("[-] ERROR: Could not open handle ($process_download)");
            return $process_download;
        }
        stream_set_blocking($pipes_download[0], false);
        stream_set_blocking($pipes_download[2], false);

        \Log::info("[+] Downloading: $this->path");

        if (! $process_sql = proc_open($this->psql_command($new_table), [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ], $pipes_sql)) {
            \Log::err("[-] ERROR: Could not open SQL pipe ($this->id) '$this->name'", ['process' => $process_sql]);
            return $process_sql;
        }
        stream_set_blocking($pipes_sql[1], false);
        stream_set_blocking($pipes_sql[2], false);

        // Need to get Parser before Fecher in case we need to update col_sep
        if (! $parser = $this->getParser()) {
            \Log::err("[-] ERROR: Cannot get a Parser");
            return;
        }

        if (! $fetcher = $this->getFetcher($pipes_download[1])) {
            \Log::err("[-] ERROR: Cannot get a Fetcher");
            return;
        }

        \Log::debug('[+] Parser is: '.get_class($parser).' | Fetcher is: '.get_class($fetcher));

        try {
            $fetcher->parse(function ($row) use ($parser, &$pipes_sql, &$progress_good, &$progress_bad, &$last_row, &$last_row_parsed, $process_sql, $process_download, $eta) {
                $last_row = $row;

                static $progress = ['▁', '▃', '▄', '▅', '▆', '▇', '█', '▇', '▆', '▅', '▄', '▃'];
                static $size_total = 0;

                $size = null;

                try {
                    if (! $data = $parser->parse_row($row)) {
                        $progress_bad++;
                    } else {
                        $last_row_parsed = $data;

                        array_push($data, self::$i++);

                        $size = Rfc\fputcsv($pipes_sql[0], $data);
                        $size_total += $size;
                        $progress_good++;
                    }

                    if (app()->hasDebugModeEnabled()) {
                        $eta += hrtime(true);

                        echo (app()->environment('local') ? "\r" : "\n")
                            . current($progress)
                            . "    id:$this->id"
                            . "    i:".self::$i
                            . "    good:$progress_good"
                            . "    bad:$progress_bad"
                            . "    time:".($eta / 1e+9)
                            . "    size_total:".convert_filesize($size_total)
                            . "    size:".$size
                            . str_repeat(' ', 20)
                            ;

                        if (0 == self::$i % 100 && false === next($progress)) {
                            reset($progress);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::debug([
                        'ID' => 1,
                        'message' => $e->getMessage(),
                        'debug' => local_debug(),
                        'trace' => $e->getTraceAsString(),
                    ]);

                    app('sentry')->configureScope(function($scope) use ($process_sql, $process_download) {
                        $scope->setExtras([
                            'source' => $this,
                            'debug' => local_debug(),
                        ]);
                    });
                    app('sentry')->captureException($e);

                    return false;
                }
            });
        } catch (\Exception $e) {
            \Log::debug([
                'ID' => 2,
                'message' => $e->getMessage(),
                'debug' => local_debug(),
                'trace' => $e->getTraceAsString(),
            ]);

            app('sentry')->configureScope(function($scope) use ($process_sql, $process_download) {
                $scope->setExtras([
                    'source' => $this,
                    'debug' => local_debug(),
                ]);
            });
            app('sentry')->captureException($e);
        }

        if (app()->hasDebugModeEnabled()) {
            echo "\nWrapping up...\n";
        }

        $output_1 = trim(stream_get_contents($pipes_sql[1]));
        $output_2 = trim(stream_get_contents($pipes_sql[2]));
        $log_2 = trim(stream_get_contents($pipes_download[2]));

        fclose($pipes_sql[0]);
        fclose($pipes_sql[1]);
        fclose($pipes_sql[2]);

        fclose($pipes_download[0]);
        fclose($pipes_download[1]);
        fclose($pipes_download[2]);

        $termination_status_1 = proc_close($process_download);
        $termination_status_2 = proc_close($process_sql);

        $old_progress = $this->nb_of_products;
        $this->nb_of_products = $progress_good;

        if(BigDeltaDuringImport::shouldNotify($old_progress, $progress_good))
            \Notification::route('slack', env('SLACK_WEBHOOK'))
                ->notify(new BigDeltaDuringImport($this, $old_progress, $progress_good));

        $this->extra = explode('--', $this->extra, 2)[0];
        $this->extra .= '--';
        $this->extra .= "\nSTART: ".$start;
        $this->extra .= "\nEND: ".date(DATE_RSS);
        $this->extra .= "\nTERMINATION_STATUS (DL):  ".$termination_status_1;
        $this->extra .= "\nTERMINATION_STATUS (SQL): ".$termination_status_2;
        $this->extra .= "\ngood:$progress_good bad:$progress_bad old:$old_progress";
        $this->extra .= "\nreasons_for_skipping: ".print_r($this->reasons_for_skipping, true);
        $this->extra .= "\nheaders: ".print_r($fetcher->headers ?? $this->getHeaders(), true);
        $this->extra .= "\nlast_row: ".substr(print_r($last_row, true), 0, 5000);
        $this->extra .= "\nlast_row_parsed: ".print_r(Fetchers\CSV::array_combine(self::$columns, $last_row_parsed), true);
        if(@$this->config[Source::CONFIG_DEBUG_SHOW_BRANDS_ADDED])
            $this->extra .= "\nbrands_added: ".print_r(array_unique($this->brands_added), true);
        if(@$this->config[Source::CONFIG_DEBUG_SHOW_CATEGORIES_ADDED])
            $this->extra .= "\ncategories_added: ".print_r(array_values(array_filter(array_unique($this->categories_added))), true);
        $this->extra .= "\nLOG_1 (sql: STDOUT): ".$output_1;
        $this->extra .= "\nLOG_2 (sql: STDERR): ".$output_2;
        $this->extra .= "\nLOG_3 (download: STDERR): \n".$log_2;

        try {
            $this->save();
        } catch (\Exception $e) {
            app('sentry')->withScope(function($scope) use ($process_sql, $process_download) {
                $scope->setContext('data', [
                    'source' => $this,
                    'debug' => local_debug(),
                ]);
            });
            app('sentry')->captureException($e);
        }

        if (app()->hasDebugModeEnabled())
            echo $this->extra . "\n";

        \Log::info("[+] Imported ($this->id) '$this->name' in [".($eta / 1e+9)."] i:".self::$i." good:$progress_good bad:$progress_bad old:$old_progress");

        if (false === preg_match('/^COPY /', $output_1) && empty($output_2)) {
            \Log::debug('[-] DB ERROR: '.$output_1.' / '.$output_2);
            app('sentry')->captureMessage("1: $output_1\n2: $output_2");
        }

        return $termination_status_2;
    }
}
