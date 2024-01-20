<?php

namespace App\Models\Fetchers;

use Ajgl\Csv\Rfc;

class CSV extends Fetcher
{
    private $col_sep;

    public $headers;

    const BOM_STRING = "\xef\xbb\xbf";

    private function remove_bom($string) {
        if (self::BOM_STRING == substr($string, 0, 3)) {
            $string = ltrim($string, self::BOM_STRING);
        }

        return $string;
    }

    public function __construct($handle, $opts = [])
    {
        parent::__construct($handle, $opts);

        $this->col_sep = $opts['col_sep'];
        $this->headers = array_values($opts['headers'] ?: []);

        if (empty($this->headers)) {
            if ($headers = $this->get_new_row_raw()) {
                $headers[0] = $this->remove_bom($headers[0]);

                if(count($headers) == 1) {
                    $separators = [
                        ',',
                        ';',
                        '|',
                        "\t",
                    ];

                    foreach($separators as $separator)
                        $counts[$separator] = substr_count($headers[0], $separator);

                    $max = max($counts);
                    $this->col_sep = array_search($max, $counts);

                    \Log::info("Had to figure out col_sep for this source", [
                        'headers' => $headers,
                        'counts' => $counts,
                        'max' => $max,
                        'col_sep' => $this->col_sep,
                        'opts' => $opts,
                    ]);

                    $headers = explode($this->col_sep, $headers[0]);
                }

                $this->headers = array_map(function ($v) {
                    return trim($v, " \n\r\t\v\0".'"');
                }, $headers);
            }
        }
    }

    public function parse($callback)
    {
        while (list($row, $raw) = $this->get_new_row())
            if(false === $callback($row, $raw))
                break;
    }

    private function get_new_row_raw()
    {
        return Rfc\fgetcsv($this->handle, 0, $this->col_sep);
    }

    public function get_new_row()
    {
        if ($value = $this->get_new_row_raw()) {
            return [self::array_combine($this->headers, $value), $value];
        }

        return false;
    }

    public static function array_combine($keys, $values)
    {
        $count_1 = count($keys);
        $count_2 = count($values);

        $diff = $count_2 - $count_1;

        if (0 != $diff) {
            $max = max($count_1, $count_2);

            if ($diff > 0) {
                $keys = array_pad($keys, $max, 'ignore_me');
            } else {
                $values = array_pad($values, $max, 'ignore_me');
            }
        }

        return array_combine($keys, $values);
    }
}
