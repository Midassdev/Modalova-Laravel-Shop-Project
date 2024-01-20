<?php

declare(strict_types=1);

namespace App\Models\Parsers;

class Paidonresults extends BaseParser
{
    protected $fields = [
        'product_id' => [
            'ProductId',
        ],

        'product_name' => [
            'ProductName',
        ],

        'brand_name' => [
            'BrandName',
        ],

        'price' => [
            'ProductPrice',
        ],

        'description' => [
            'ProductDescription',
        ],

        'url' => [
            'AffiliateURL',
        ],

        'merchant' => [
            'MerchantName',
        ],

        'image_url' => [
            'OriginalImage',
        ],

        'categories' => [
            'Category',
        ],
    ];

}
