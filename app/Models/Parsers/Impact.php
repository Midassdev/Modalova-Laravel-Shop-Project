<?php

namespace App\Models\Parsers;

class Impact extends BaseParser
{
    protected $fields = [
        'product_id' => [
            'Unique_Merchant_SKU',
        ],

        'product_name' => [
            'Product_Name',
        ],

        'brand_name' => [
            'Manufacturer',
        ],

        'price' => [
            'Current_Price',
        ],

        'old_price' => [
            'Original_Price',
        ],

        'description' => [
            'Product_Description',
        ],

        'url' => [
            'Product_URL',
        ],

        'merchant' => [
            'Manufacturer',
        ],

        'gender' => [
            'Gender',
        ],

        'image_url' => [
            'Image_URL',
        ],

        'categories' => [
            'Category',
            'Labels',
        ],

        'colors' => [
            'Color',
        ],

        'sizes' => [
            'Size',
        ],

        'availability' => [ // TODO
            'Stock_Availability',
        ],
    ];

    protected function product_is_not_available($row)
    {
        if (true == parent::product_is_not_available($row)) {
            return true;
        }

        if ('N' == @$row['Stock_Availability']) {
            return true;
        }

        return false;
    }
}
