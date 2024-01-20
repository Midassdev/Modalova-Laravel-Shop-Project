<?php

namespace App\Models\Parsers;

class Kelkoo extends BaseParser
{
    protected $fields = [
        'product_id' => [
            'offerId',
        ],

        'product_name' => [
            'title',
        ],

        'brand_name' => [
            "brand[name]",
        ],

        'merchant' => [
            "merchant[name]",
        ],

        'price' => [
            'price',
        ],

        'old_price' => [
            'priceWithoutRebate',
        ],

        'description' => [
            'description',
        ],

        'url' => [
            'goUrl',
        ],

        'livraison' => [
            'timeToDeliver',
        ],

        'currency' => [
            'currency',
        ],
    ];

    protected function __gender($row)
    {
        if (empty($row['features']['gender']['values'])) {
            return null;
        }

        return $this->asGender($row['features']['gender']['values'][0]['value']);
    }

    protected function __image_url($row)
    {
        return @$row['images'][0]['zoomUrl'] ?? @$row['images'][0]['url'];
    }

    protected function __colors($row)
    {
        if (empty($row['features']['color']['values'])) {
            return null;
        }

        $colorCollection = collect($row['features']['color']['values']);

        return $colorCollection->pluck('label')->all();
    }

    protected function __materials($row)
    {
        if (empty($row['features']['material']['values'])) {
            return null;
        }

        $colorCollection = collect($row['features']['material']['values']);

        return $this->asMultiString($colorCollection->pluck('label')->all());
    }

    protected function __sizes($row)
    {
        if (empty($row['features']['size']['values'])) {
            return null;
        }

        $colorCollection = collect($row['features']['size']['values']);

        return $colorCollection->pluck('label')->all();
    }

    protected function __motifs($row)
    {
        if (empty($row['features']['print']['values'])) {
            return null;
        }

        $colorCollection = collect($row['features']['print']['values']);

        return $colorCollection->pluck('label')->all();
    }

    protected function __models($row)
    {
        if (empty($row['features']['type']['values'])) {
            return null;
        }

        $colorCollection = collect($row['features']['type']['values']);

        return $colorCollection->pluck('label')->implode('|');
    }

    protected function __categories($row)
    {
        $categories = [@$row['category']['name'], ...preg_split('/[~~|>]/', @$row['merchantProvidedCategory'])];

        return array_map('trim', $categories);
    }

}
