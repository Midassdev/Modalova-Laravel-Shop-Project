<?php

namespace App\Models\Parsers;

class Shopify extends BaseParser
{
    protected $fields = [
        'product_id' => [
            'id',
        ],

        'product_name' => [
            'title',
        ],

        'brand_name' => [
            'vendor',
        ],

        'description' => [
            'body_html',
        ],

        'merchant' => [
            'vendor',
        ],

        'categories' => [
            'product_type',
            'tags',
        ],
    ];

    protected function __price($row)
    {
        return @$row['variants'][0]['price'];
    }

    protected function __old_price($row)
    {
        return @$row['variants'][0]['compare_at_price'];
    }

    protected function __url($row)
    {
        $url = parse_url($this->source->path);

        return $url['scheme'].'://'.$url['host'].'/products/'.@$row['handle'];
    }

    protected function __image_url($row)
    {
        return @$row['images'][0]['src'];
    }

    protected function __colors($row)
    {
        return $this->search_in_options($row, [
            'Color',
        ]);
    }

    protected function __sizes($row)
    {
        return $this->search_in_options($row, [
            'Size',
        ]);
    }

    protected function __materials($row)
    {
        return $this->asMultiString(
            $this->search_in_options($row, [
                'Material',
            ]
        ));
    }

    private function search_in_options($row, $matches)
    {
        foreach (@$row['options'] ?: [] as $option) {
            foreach ($matches as $match) {
                if (strtolower($match) == strtolower($option['name'])) {
                    return $option['values'];
                }
            }
        }
    }

    protected function product_is_not_available($row)
    {
        if (true == parent::product_is_not_available($row)) {
            return true;
        }

        if (! empty($row['variants'])) {
            $at_lease_one_is_available = false;

            foreach ($row['variants'] as $variant) {
                if (true === $variant['available']) {
                    $at_lease_one_is_available = true;
                }
            }

            if (false === $at_lease_one_is_available) {
                return true;
            }
        }

        return false;
    }
}
