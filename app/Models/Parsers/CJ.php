<?php

namespace App\Models\Parsers;

class CJ extends BaseParser
{
    protected $fields = [
        'product_id' => [
            'ID',
        ],

        'product_name' => [
            'TITLE',
        ],

        'brand_name' => [
            'BRAND',
        ],

        'price' => [
            'SALE_PRICE',
        ],

        'old_price' => [
            'PRICE',
        ],

        'description' => [
            'DESCRIPTION',
        ],

        'url' => [
            'LINK',
        ],

        'merchant' => [
            'PROGRAM_NAME',
        ],

        'gender' => [
            'GENDER',
        ],

        'image_url' => [
            'IMAGE_LINK',
        ],

        'categories' => [
            'GOOGLE_PRODUCT_CATEGORY_NAME',
            'PRODUCT_TYPE',
        ],

        'colors' => [
            'COLOR',
        ],

        'sizes' => [
            'SIZE',
        ],

        'materials' => [
            'MATERIAL',
        ],
    ];

    protected function __brand_name($row)
    {
        if (in_array($merchant = $this->merchant($row), ['The Kooples'])) {
            return $merchant;
        }

        return parent::__brand_name($row);
    }

    protected function __image_url($row)
    {
        $value = parent::__image_url($row);

        $merchant = $this->merchant($row);

        if ('SHEIN' == $merchant) {
            if (false !== $pos = strpos($value, '_thumbnail_')) {
                $path_parts = pathinfo($value);
                $value = substr($value, 0, $pos).'.'.$path_parts['extension'];
            }
        } elseif (false !== stripos($merchant, 'Mytheresa')) {
            $value = str_replace('/1000/1000/95/jpeg/', '/500/500/80/jpeg/', $value);
        }

        return $value;
    }

    protected function __sizes($row)
    {
        $values = [];
        // $values = array_merge($values, $this->handle_multiple_values(@$row['SIZE']));
        // how to parse 'U IT  - (One size FR)' ?
        return $values;
    }
}
