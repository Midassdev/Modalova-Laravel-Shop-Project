<?php

namespace App\Models\Parsers;

class NetaffiliationV3 extends BaseParser
{
    public $col_sep = ';';

    protected $fields = [
        'product_id' => [
            'Id product',
        ],

        'product_name' => [
            'Name',
        ],

        'brand_name' => [
            'Brand',
        ],

        'price' => [
            'Price',
        ],

        'old_price' => [
            'Crossed prices',
        ],

        'description' => [
            'Description',
        ],

        'url' => [
            'Url',
        ],

        'merchant' => [
        ],

        'gender' => [
            'Category', // do not add me ::guess_categories(), values are too broad
            'Category merchant',
            'Name',
        ],

        'image_url' => [
            'Url large image',
        ],

        'livraison' => [
            'Delivery delays',
            'Availability',
        ],

        'categories' => [
            'Category merchant',
        ],

        'colors' => [
        ],

        'sizes' => [
        ],

        'materials' => [
        ],

        'models' => [
            'Model',
        ],
    ];
}
