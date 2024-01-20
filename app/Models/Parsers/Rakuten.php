<?php

namespace App\Models\Parsers;

class Rakuten extends BaseParser
{
    public static $headers = [
        1 => 'Product ID',
        2 => 'Product Name',
        3 => 'SKU Number',
        4 => 'Primary Category',
        5 => 'Secondary Category(ies)',
        6 => 'Product URL',
        7 => 'Product Image URL',
        8 => 'Buy URL',
        9 => 'Short description of product.',
        10 => 'Long Product',
        11 => 'Description',
        12 => 'Discount Type',
        13 => 'Sale Price',
        14 => 'Retail Price',
        15 => 'Begin Date',
        16 => 'End Date',
        17 => 'Brand',
        18 => 'Shipping',
        19 => 'Keyword(s)',
        20 => 'Manufacturer Part #',
        21 => 'Manufacturer Name',
        22 => 'Shipping Information',
        23 => 'Availability',
        24 => 'Universal Product Code',
        25 => 'Class ID',
        26 => 'Currency',
        27 => 'M1',
        28 => 'Pixel',
        29 => 'Miscellaneous',
        30 => 'Product Type',
        31 => 'Size',
        32 => 'Material',
        33 => 'Color',
        34 => 'Gender',
        35 => 'Style',
        36 => 'Age',
        37 => 'Attribute 9',
        38 => 'Attribute 10',
        39 => 'Attribute 11',
        40 => 'Attribute 12',
        41 => 'Attribute 13',
        42 => 'Attribute 14',
        43 => 'Attribute 15',
        44 => 'Attribute 16',
        45 => 'Attribute 17',
        46 => 'Attribute 18',
        47 => 'Attribute 19',
        48 => 'Attribute 20',
        49 => 'Attribute 21',
        51 => 'Modification',
    ];

    public $col_sep = '|';

    protected $fields = [
        'product_id' => [
            'Product ID',
        ],

        'product_name' => [
            'Product Name',
        ],

        'brand_name' => [
            'Brand',
        ],

        'price' => [
            'Sale Price',
        ],

        'old_price' => [
            'Retail Price',
            'Sale Price',
        ],

        'description' => [
            'Description',
            'Long Product',
        ],

        'url' => [
            'Product URL',
        ],

        'merchant' => [
        ],

        'gender' => [
            'Gender',
            'Size', // Rakuten
        ],

        'image_url' => [
            'Product Image URL',
        ],

        'livraison' => [
        ],

        'categories' => [
            'Primary Category',
            'Secondary Category(ies)',
            'Product Type',
        ],

        'colors' => [
            'Color',
        ],

        'sizes' => [
            'Size',
        ],

        'materials' => [
            'Material',
        ],

        'currency' => [
            'Currency',
        ],

        'age_group' => [
            'Age',
        ],
    ];
}
