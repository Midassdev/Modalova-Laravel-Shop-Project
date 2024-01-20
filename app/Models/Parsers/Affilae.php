<?php

namespace App\Models\Parsers;

class Affilae extends BaseParser
{
    protected $fields = [
        'product_id' => [
            'id',
            '_id',
        ],

        'product_name' => [
            'title',
            'name',
        ],

        'brand_name' => [
            'brand',
            'attribute_marque',
        ],

        'price' => [
            'sale_price',
            'sale price',
            'price_with_discount',
        ],

        'old_price' => [
            'price',
            'price_without_discount',
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
            'image link',
        ],

        'livraison' => [
            'livraison',
            'Delai de livraison',
        ],

        'categories' => [
            'google_product_category_name',
            'product_type',
            'google product category',
            'product type',
            'categories',
        ],

        'colors' => [
            'color',
            'attribute_couleur',
        ],

        'sizes' => [
            'attribute_taille',
            // 'SIZE', // how to parse 'U IT  - (One size FR)' ?
        ],

        'materials' => [
            'material',
            'attribute_matiere',
        ],
    ];

    protected function __image_url($row)
    {
        $image_url = parent::__image_url($row);

        if(empty($image_url))
            $image_url = @$row['images'][0];

        return $image_url;
    }
}
