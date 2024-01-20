<?php

namespace App\Models\Parsers;

use App\Models\Source;

class Prestashop extends BaseParser
{
    protected $fields = [
        'product_id' => [
            'reference',
        ],

        'product_name' => [
            'name',
        ],

        'price' => [
            'price',
        ],

        'old_price' => [
            'regular_price',
        ],

        'description' => [
            'description',
            'description_short',
            'meta_description',
        ],
    ];

    protected function __product_name($row)
    {
        return $this->get_value_localized(parent::__product_name($row));
    }

    protected function __description($row)
    {
        return $this->get_value_localized(parent::__description($row));
    }

    protected function __url($row)
    {
        return $this->get_base_url(true)
            . @$row['id']
            . '-'
            . @$row['id_default_combination']
            . '.html';
    }

    protected function __image_url($row)
    {
        return $this->get_base_url()
            . @$row['id_default_image']
            . '-'
            . $this->source->config[Source::CONFIG_PRESTASHOP_IMAGE_TYPE]
            . '/image.jpg';
    }

    protected function __categories($row)
    {
        $values = [];

        $values[] = @$this->get_value_localized($this->resolve_category(@$row['id_category_default'])['name']);
        foreach (@$row['associations']['categories'] as $id) {
            $values[] = @$this->get_value_localized($this->resolve_category($id['id'])['name']);
        }

        return $values;
    }

    private function get_base_url($include_lang = false)
    {
        $chunks = parse_url($this->source->path);
        $base = $chunks['scheme'].'://'.$chunks['host'].'/';

        return $base.($include_lang ? $this->source->config[Source::CONFIG_PRESTASHOP_LANGUAGE].'/' : '');
    }

    private function get_value_localized($values)
    {
        $id_language = @$this->source->config[Source::CONFIG_PRESTASHOP_LANGUAGE_ID];

        foreach ($values as $value) {
            if ($id_language == $value['id']) {
                return $value['value'];
            }
        }

        return false;
    }

    private function resolve_category($id_category)
    {
        static $categories = [];

        if (empty($categories)) {
            $raw_categories = json_decode(file_get_contents(
                str_replace('/products', '/categories', $this->source->path)
            ), true)['categories'];

            foreach ($raw_categories as $category) {
                $categories[$category['id']] = $category;
            }
        }

        return $categories[$id_category];
    }
}
