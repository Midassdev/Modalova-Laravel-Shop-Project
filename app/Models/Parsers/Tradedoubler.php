<?php

namespace App\Models\Parsers;

class Tradedoubler extends BaseParser
{
    public $col_sep = '|';

    protected $fields = [
        'product_id' => [
            'sku',
        ],

        'product_name' => [
            'name',
        ],

        'brand_name' => [
            'brand',
        ],

        'price' => [
            '(field)Sale Price',
            'priceValue',
            'price',
        ],

        'old_price' => [
            'priceValue',
        ],

        'description' => [
            'description',
        ],

        'url' => [
            'productUrl',
        ],

        'merchant' => [
            'programName',
        ],

        'gender' => [
            '(field)gender',
            '(field)Age Group', // fix a misconfiguration from vendors
            'categories',
        ],

        'image_url' => [
            'productImage',
        ],

        'livraison' => [
            'deliveryTime',
        ],

        'categories' => [
            '(field)subcategory',
        ],

        'colors' => [
            '(field)Color',
            '(field)color',
        ],

        'sizes' => [
            'size',
        ],

        'materials' => [
            '(field)composition',
        ],
    ];

    private $__description_properties = [
        'Manche:',
        'Matériel:',
    ];

    protected function __brand_name($row)
    {
        $value = parent::__brand_name($row);

        if(false !== stripos($this->source->title, 'DC Shoes'))
            $value = @$row['size'];

        return $value;
    }

    protected function __description($row)
    {
        $value = parent::__description($row);

        $value = preg_replace_callback('/(&#[0-9]+;)/', function ($m) {
            return mb_convert_encoding($m[1], 'UTF-8', 'HTML-ENTITIES');
        }, $value);

        foreach ($this->__description_properties as $prop) {
            $value = str_replace($prop, "\n{$prop}", $value);
        }

        $min = 'a-zàâäèéêëîïôœùûüÿç';
        $max = 'A-ZÀÂÄÈÉÊËÎÏÔŒÙÛÜŸÇ';

        $value = preg_replace(
            '#(['.$min.']|[1-9]XL+|[0-9]+%)(['.$max.'])(?=[0-9'.$max.''.$min.'\-‎\'\s\(\)/]*:)#u',
            "$1\n$2",
            htmlspecialchars_decode($value)
        );

        return $value;
    }

    protected function __image_url($row)
    {
        return trim(parent::__image_url($row), ':;');
    }

    protected function __categories($row)
    {
        $values = parent::__categories($row);

        $values = [...$values, ...explode(';', @$row['categories'])];

        return $values;
    }
}
