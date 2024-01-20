<?php

declare(strict_types=1);

namespace App\Models\Parsers;

class Admitad extends BaseParser
{
    protected $fields = [
        'product_id' => [
            'id',
        ],

        'product_name' => [
            'name',
        ],

        'brand_name' => [
            'brand',
        ],

        'merchant' => [
            'vendor',
        ],

        'currency' => [
            'currencyId',
        ],

        'price' => [
            'price',
        ],

        'old_price' => [
            'oldprice',
        ],

        'description' => [
            'description',
        ],

        'categories' => [
            'categoryId',
            'categories',
        ],

        'url' => [
            'url',
        ],

        'image_url' => [
            'picture',
        ],

        'colors' => [
            'Color',
        ],

        'sizes' => [
            'Size',
            'dimensions',
        ],

        'materials' => [
            'material',
        ],

        'livraison' => [
            'shippingPrice',
            'local_delivery_cost',
        ],
    ];

}
