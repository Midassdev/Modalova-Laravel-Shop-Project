<?php

namespace App\Models\Parsers;

class Woocommerce extends BaseParser
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
            'short_description',
        ],

        'url' => [
            'link',
        ],

        'merchant' => [
        ],

        'gender' => [
        ],

        'image_url' => [
            'featured_image',
            'main_image',
        ],

        'categories' => [
            'parent_category',
            'child_category',
            'category_path',
        ],
    ];

    protected function __categories($row)
    {
        $values = parent::__categories($row);

        $values = array_merge($values, explode(',', (string)@$row['tags']));

        return $values;
    }
}
