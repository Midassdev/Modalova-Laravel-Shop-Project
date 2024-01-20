<?php

namespace Tests\Models\Parsers;

use App\Models\Fetchers\CSV;
use App\Models\Fetchers\XML;
use App\Models\Product;
use App\Models\Source;
use PHPUnit\Framework\TestCase;

class BaseParser extends TestCase
{
    public static $headers = '';

    protected function parse_payload($payload, $headers = [])
    {
        if (empty($headers)) {
            $headers = static::$headers;
        }

        $source = new Source;
        $source->name = 'Provider - Source Name';
        $source->title = 'Source Title';
        $source->mapping = [];

        $parser = new static::$klass($source);

        $data = (array) json_decode(base64_decode($payload), true);

        if (! empty($headers)) {
            $data = CSV::array_combine(explode(',', $headers), $data);
        }

        $data = XML::xml2array($data);

        $parsed_data = $parser->parse_row($data);

        if (! $parsed_data) {
            error_log('Could not parse data');
            var_dump($data, $source);

            return;
        }

        array_push($parsed_data, 1); // i

        $result = (new Product(array_combine(Source::$columns, $parsed_data)))->toArray();
        // $result['provider'] = $parser::$parser;

        unset($result['payload']);
        unset($result['provider']);
        unset($result['i']);
        $result = array_filter($result, function ($v) {
            return ! is_array($v);
        });

        return $result;
    }
}
