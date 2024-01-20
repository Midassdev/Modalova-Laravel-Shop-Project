<?php

namespace App\Models\Parsers;

class Tradetracker extends BaseParser
{
    public $col_sep = ';';

    protected $fields = [
        'product_id' => [
            'product ID',
        ],

        'product_name' => [
            'name',
        ],

        'brand_name' => [
            'brand',
            'manufacturer',
        ],

        'price' => [
            'price',
            'sale_price',
        ],

        'old_price' => [
            'fromPrice',
            'sale_price',
        ],

        'description' => [
            'description',
        ],

        'url' => [
            'productURL',
        ],

        'merchant' => [
        ],

        'gender' => [
            'gender',
            'extra_Zielgruppe',
        ],

        'image_url' => [
            'imageURL',
            'image_url',
        ],

        'livraison' => [
            'deliveryTime',
        ],

        'categories' => [
            'category',
            'categories',
            'subcategories',
            'subsubcategories',
            'extra_KategoriepfadName',
        ],

        'colors' => [
            'color',
            'extra_MainColor',
            'extra_Farbe',
        ],

        'sizes' => [
            'size',
            'extra_Size',
        ],

        'materials' => [
            'material',
            'fabric',
            'extra_Material',
        ],

        'currency' => [
            'currency',
        ],
    ];

    protected function __product_name($row)
    {
        $value = parent::__product_name($row);

        if (strpos($value, '|') !== false) {
            $nameParts = explode('|', $value);
            $value = reset($nameParts);

            if (preg_match('#\(.*?taille.*?\)#', $value)) {
                $value = preg_replace('#(\(.*?taille.*?\))#', '', $value);
            }
        }

        return $value;
    }

    protected function __old_price($row)
    {
        $value = parent::__old_price($row);

        if (empty($value)) {
            $value = str_replace(',', '.', (string)@$row['extra_Streichpreis']);
        }

        return $value;
    }

    protected function __categories($row)
    {
        $values = [];

        if (! empty($row['extra_KategoriepfadName']))
            $row['extra_KategoriepfadName'] = str_replace('/', ' > ', $row['extra_KategoriepfadName']);

        foreach (@$this->fields['categories'] ?: [] as $col) {
            if (! empty($row[$col])) {
                $categoryParts = explode(' > ', $row[$col]);
                $values = array_merge($values, $categoryParts);
            }
        }

        return $values;
    }
}
