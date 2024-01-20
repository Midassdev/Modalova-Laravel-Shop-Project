<?php

namespace App\Models\Parsers;

class Flexoffers extends BaseParser
{
    protected $fields = [
        'product_id' => [
            'pid',
        ],

        'product_name' => [
            'name',
        ],

        'brand_name' => [
            'brand',
        ],

        'price' => [
            'finalPrice',
            'salePrice',
            'price',
        ],

        'old_price' => [
            'price',
        ],

        'description' => [
            'description',
        ],

        'url' => [
            'linkUrl',
        ],

        'merchant' => [
            'manufacturer',
            'brand',
        ],

        'gender' => [
            'gender',
        ],

        'image_url' => [
            'imageUrl',
        ],

        'categories' => [
            'category',
        ],

        'colors' => [
            'color',
        ],

        'sizes' => [
            'size',
        ],

        'currency' => [
            'priceCurrency',
        ],
    ];
}
