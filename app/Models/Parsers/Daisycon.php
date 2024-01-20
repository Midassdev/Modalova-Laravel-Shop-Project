<?php

namespace App\Models\Parsers;

class Daisycon extends BaseParser
{
    protected $fields = [
        'product_id' => [
            'daisycon_unique_id',
        ],

        'product_name' => [
            'title',
        ],

        'brand_name' => [
            'brand',
        ],

        'price' => [
            'price',
        ],

        'old_price' => [
            'price_old',
        ],

        'description' => [
            'description',
        ],

        'url' => [
            'link',
        ],

        'merchant' => [
            'name',
        ],

        'gender' => [
            'gender_target',
            'category_path',
        ],

        'image_url' => [
            'image_large',
            'image_medium',
            'image_small',
        ],

        'livraison' => [
            'delivery_time',
        ],

        'categories' => [
            'category_path',
            'category',
        ],

        'colors' => [
            'color_primary',
        ],

        'sizes' => [
            'size',
        ],

        'materials' => [
            'material_description',
        ],

        'models' => [
            'model',
            'sku',
        ],
    ];

    protected function before_parse_row($row)
    {
        $row = parent::before_parse_row($row);

        $row['title'] = preg_replace('/taille [0-9\.]+$/i', '', @$row['title']);
        $row['title'] = preg_replace('/\(taille: [0-9\.]+\)$/i', '', $row['title']);
        $row['title'] = preg_replace('/\(maat [0-9\.]+\)$/i', '', $row['title']);

        $row['description'] = preg_replace('/taille [0-9\.]+$/i', '', @$row['description']);
        $row['description'] = preg_replace('/\(taille: [0-9\.]+\)$/i', '', $row['description']);

        return $row;
    }

    protected function __livraison($row)
    {
        $value = parent::__livraison($row);

        if (!empty($value)) {
            $value .= ($value > 1) ? ' days' : ' day';
        }

        return $value;
    }

    protected function __categories($row)
    {
        $values = [];

        foreach (@$this->fields['categories'] as $field)
            if (empty($value))
                $values = [...$values, ...explode('|', @$row[$field] ?: '')];

        return $values;
    }
}
