<?php

namespace App\Models\Parsers;

use App\Models\Gender;
use App\Models\Product;

class Awin extends BaseParser
{
    protected $fields = [
        'product_id' => [
            'parent_product_id',
            'aw_product_id',
        ],

        'product_name' => [
            'product_name',
        ],

        'brand_name' => [
            'brand_name',
        ],

        'price' => [
            'search_price',
            'rrp_price',
        ],

        'old_price' => [
            'product_price_old',
            'base_price',
            'rrp_price',
        ],

        'description' => [
            'description',
        ],

        'url' => [
            'aw_deep_link',
        ],

        'merchant' => [
            'merchant_name',
        ],

        'gender' => [
            'Fashion:suitable_for',
            'custom_1',
            'custom_5', // Under Armour
        ],

        'image_url' => [
            'aw_image_url',
            'large_image',
            'merchant_image_url',
        ],

        'livraison' => [
            'delivery_time',
        ],

        'categories' => [
            'merchant_product_category_path',
            'merchant_product_second_category',
            'merchant_product_third_category',
            'merchant_category',
            'category_name',
            'product_type',
        ],

        'colors' => [
            'colour',
        ],

        'sizes' => [
            'Fashion:size',
        ],

        'materials' => [
            'Fashion:material',
        ],
    ];

    public function __construct($source)
    {
        parent::__construct($source);

        if(!empty($source->path)) {
            if (1 === preg_match('/\/delimiter\/\%([A-Z0-9]{2})\//', $source->path, $matches)) {
                $source->col_sep = hex2bin($matches[1])[0];
            }
        }
    }

    protected function after_parse_row($array, $row)
    {
        $array = parent::after_parse_row($array, $row);

        if (preg_match('/^(.+) - - ([0-9]+|[XSML]{1,3}|taille unique|one size)$/i', $array[0], $matches)) {
            // name
            $array[0] = $matches[1];

            // sizes
            $array[23] .= Product::FACET_SEPARATOR.mb_strtoupper($matches[2]);
            $array[23] = trim($array[23], Product::FACET_SEPARATOR);
        }

        // generate slug with final $product_name
        $array[1] = $this->__slug_real($array[0], $this->__product_id($row));

        // we remove these values from 'description' so that duplicates can be detected
        foreach ([
            12, // colors
            23, // sizes
        ] as $key) {
            $array[2] = str_ireplace(
        array_map(function ($v) {
            return " $v ";
        }, explode(Product::FACET_SEPARATOR, $array[$key])), ' ',
        $array[2]
      );
        }

        return $array;
    }

    protected function __brand_name($row)
    {
        if ('ARMOR LUX FR' == @$row['merchant_name'])
            return '';

        $value = parent::__brand_name($row);

        if (empty($value))
            $value = preg_replace('/ FR$/', '', @$row['merchant_name']);

        return $value;
    }

    protected function __description($row)
    {
        if ('15341' == @$row['data_feed_id'] && '7375' == @$row['merchant_id']) { // Tostadora
            $this->fields['description'] = ['product_short_description'];
        }

        return parent::__description($row);
    }

    protected function __gender($row)
    {
        $value = null;

        if (in_array($row['brand_name'], [
            'Etam',
            'Envie de Fraise',
            'Nasty Gal',
        ])) {
            return Gender::GENDER_FEMALE;
        }

        if (in_array($row['merchant_name'], [
            'JW PEI FR',
        ])) {
            return Gender::GENDER_FEMALE;
        }

        $description = $this->description($row);

        if (false !== strpos($description, 'la mannequin')) {
            return Gender::GENDER_FEMALE;
        }

        $value = parent::__gender($row);

        if (empty($value)) {
            foreach ($this->fields['categories'] as $field) {
                if (empty($value)) {
                    $value = $this->asGender(@$row[$field]);
                }
            }
        }

        if ('Jacques Loup FR' == @$row['merchant_name']) {
            if (empty($value)) {
                $value = $this->asGender(@$row['product_name']);
            }
        }

        return $value;
    }

    protected function __image_url($row)
    {
        $value = parent::__image_url($row);

        if (! empty($row['large_image'])) {
            if ('15255' == @$row['data_feed_id'] && '7342' == @$row['merchant_id']) { // Boohoo
                $value = explode('$', @$row['large_image'])[0];
            } elseif ('yoox_fr' == @$row['merchant_name']) {
                $value = @$row['large_image'];
            }
        }

        if ('Jacques Loup' == $this->brand_name($row)) {
            $value = str_replace('ssl%3A', 'https%3A%2F%2F', $value);
            parse_str(parse_url($value, PHP_URL_QUERY), $result);

            if ($urls = $result['url']) {
                $urls = explode(',', $urls);
                $value = $urls[0];
            }
        }

        return empty($value) ? '' : preg_replace('/([w|h])=[0-9]+/', '$1=1000', $value);
    }

    protected function __categories($row)
    {
        $values = [];

        if ('15341' == @$row['data_feed_id'] && '7375' == @$row['merchant_id']) { // Tostadora
            $values[] = 'vêtements > t-shirt';
        }

        if ('18025' == @$row['data_feed_id'] && '9173' == @$row['merchant_id']) { // PrettyLittleThing
            $values[] = @$row['custom_4'];
        }

        if ('NastyGal FR' == @$row['merchant_name']) {
            $values[] = @$row['size_stock_amount'];
        }

        if (in_array(@$row['brand_name'], [
            'TFNC',
            'TFNC Tall',
            'TFNC Plus',
            'TFNC Maternity',
            'TFNC Petite',
            'Envie de Fraise',
        ])) {
            $values[] = 'vêtements';
        }

        $values = [...$values, ...parent::__categories($row)];

        return $values;
    }

    protected function __materials($row)
    {
        $value = parent::__materials($row);

        if ('Scotch&Soda FR' == @$row['merchant_name']) {
            if ($data = json_decode($value)) {
                return $this->asMultiString(array_keys(get_object_vars($data)));
            }
        }

        return $value;
    }
}
