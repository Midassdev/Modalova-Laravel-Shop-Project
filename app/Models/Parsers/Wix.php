<?php

namespace App\Models\Parsers;

class Wix extends BaseParser
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
            'sale_price',
        ],

        'old_price' => [
            'price',
        ],

        'description' => [
            'description',
        ],

        'url' => [
            'link',
        ],

        'gender' => [
            'gender',
        ],

        'image_url' => [
            'image_link',
        ],

        'categories' => [
            'google_product_category',
        ],
    ];
}
