<?php

namespace App\Models\Parsers;

class Partnerize extends BaseParser
{
    public $col_sep = '|';

    protected $fields = [
        'product_id' => [
            'id',
            'product_id',
            'pid',
        ],

        'product_name' => [
            'title',
            'product_name',
            'name',
        ],

        'brand_name' => [
            'brand',
            'brand_name',
        ],

        'price' => [
            'sale_price_without_currency_symbol',
            'sale_price',
            'saleprice',
        ],

        'old_price' => [
            'price_without_currency_symbol',
            'price',
            'price_old',
        ],

        'description' => [
            'description',
        ],

        'url' => [
            'link',
            'deep_link',
            'purl',
            'url',
        ],

        'merchant' => [
            'storename',
        ],

        'gender' => [
            'gender',
            'suitable_for',
        ],

        'image_url' => [
            'image_link',
            'image_url',
            'imgurl',
            'image',
        ],

        'livraison' => [
            'delivery_time',
        ],

        'categories' => [
            'category',
            'product_type',
            'google_product_category',
            'merchant_category',
            'product_type_old',
            'ptype',
            'primary_category',
        ],

        'colors' => [
            'color',
            'colour',
        ],

        'sizes' => [
            'size',
        ],

        'materials' => [
            'material',
        ],
    ];

    protected function __brand_name($row)
    {
        $value = parent::__brand_name($row);

        if (in_array($value, ['032C', '0711', '0909'])) {
            $value = $this->merchant($row);
        }

        return $value;
    }

    protected function __image_url($row)
    {
        $value = parent::__image_url($row);

        if (false !== strpos($value, 'https://cdn-images.farfetch-contents.com')) {
            $value = str_replace('_1000.jpg', '_500.jpg', $value);
        }

        return $value;
    }
}
