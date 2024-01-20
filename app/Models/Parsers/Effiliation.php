<?php

namespace App\Models\Parsers;

class Effiliation extends BaseParser
{
    protected $fields = [
        'product_id' => [
            'id',
        ],

        'product_name' => [
            'title',
        ],

        'brand_name' => [
            'brand',
        ],

        'price' => [
            'price',
        ],

        'old_price' => [
            'price_norebate',
        ],

        'description' => [
            'description',
        ],

        'url' => [
            'link',
        ],

        'merchant' => [
        ],

        'gender' => [
            'gender',
        ],

        'image_url' => [
            'image_link',
        ],

        'livraison' => [
            'shipping_time',
        ],

        'categories' => [
            'category',
            'category_level2',
            'category_level3',
            'category_level4',
        ],

        'colors' => [
            'color',
        ],

        'sizes' => [
            'size',
        ],

        'materials' => [
            'material',
            'style',
        ],

        'models' => [
            'id',
        ],
    ];

    protected function __livraison($row)
    {
        $value = parent::__livraison($row);

        if (! empty($value) && is_numeric($value)) {
            $value .= ($value > 1) ? ' days' : ' day';
        }

        return $value;
    }
}
