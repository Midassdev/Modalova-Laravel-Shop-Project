<?php

namespace App\Models\Parsers;

use App\Models\Gender;
use App\Models\Source;
use App\Models\Product;
use App\Models\GoogleProductCategory;

abstract class BaseParser
{
    protected $source;
    public static $headers = [];

    public function __construct($source) {
        $this->source = $source;

        if(isset($this->col_sep))
            $this->source->col_sep = $this->col_sep;

        if(!empty($this->source->mapping))
            foreach($this->source->mapping as $key => $value)
                if(!empty($value))
                    $this->fields[$key] = array_map('trim', explode(',', $value));
    }

    public function getFields($name) {
        return @$this->fields[$name] ?: [];
    }

    protected function get_value_from_row($row, $fields = []) {
        if(empty($fields))
            return null;

        $value = null;

        foreach($fields as $field)
            if(empty($value))
                $value = @$row[ $field ];

        return $value;
    }

    protected function get_multiples_values_from_row($row, $fields = []) {
        if(empty($fields))
            return null;

        $values = [];

        foreach($fields as $field)
            $values = array_merge($values, $this->handle_multiple_values(@$row[ $field ]));

        return $values;
    }

    private function get($name, $row) {
        $fn = "__{$name}";

        $value = null;

        if(empty($value))
            $value = $this->$fn($row);

        return $value;
    }

    private function product_id($row)
    {
        return $this->get('product_id', $row);
    }
    protected function __product_id($row)
    {
        return $this->get_value_from_row($row, @$this->fields['product_id']);
    }

    private function slug($row)
    {
        return $this->__slug(
            $row,
            $this->product_id($row)
        );
    }
    protected function __slug($row, $id)
    {
        return $this->__slug_real(
            $this->product_name($row),
            $id
        );
    }
    protected function __slug_real($product_name, $product_id)
    {
        return slugify($product_name.'-'.$product_id);
    }

    private function product_name($row)
    {
        return $this->asString(
            $this->get('product_name', $row)
        );
    }
    protected function __product_name($row)
    {
        return $this->get_value_from_row($row, @$this->fields['product_name']);
    }

    protected function brand_name($row)
    {
        $value = $this->asString(
            $this->get('brand_name', $row)
        );

        if ($map_config_str_replace_brand = @$this->source->config[Source::CONFIG_STR_REPLACE_BRAND])
            $value = $this->handle_str_replace($value, $map_config_str_replace_brand);

        return $value;
    }
    protected function __brand_name($row)
    {
        $value = null;

        foreach(@$this->fields['brand_name'] ?: [] as $field)
            if(empty($value))
                $value = $this->getFieldValue($row, $field);

        return $value;
    }

    private function price($row)
    {
        return $this->asFloat(
            $this->get('price', $row)
        );
    }
    protected function __price($row)
    {
        return $this->get_value_from_row($row, @$this->fields['price']);
    }

    private function old_price($row)
    {
        return $this->asFloat(
            $this->get('old_price', $row)
        );
    }
    protected function __old_price($row)
    {
        return $this->get_value_from_row($row, @$this->fields['old_price']);
    }

    protected function description($row)
    {
        return $this->asString(
            $this->get('description', $row)
        );
    }
    protected function __description($row)
    {
        return $this->get_value_from_row($row, @$this->fields['description']);
    }

    private function url($row)
    {
        return $this->asString(
            $this->get('url', $row)
        );
    }
    protected function __url($row)
    {
        return $this->get_value_from_row($row, @$this->fields['url']);
    }

    protected function merchant($row)
    {
        $value = $this->get('merchant', $row);

        if(empty($value))
            $value = $this->source->title;

        return $this->asString($value);
    }
    protected function __merchant($row) {
        $value = null;

        foreach(@$this->fields['merchant'] ?: [] as $field)
            if(empty($value))
                $value = $this->getFieldValue($row, $field);

        return $value;
    }

    private function gender($row)
    {
        $value = $this->get('gender', $row);

        if (empty($value)) {
            $value = $this->asGender($this->categories($row));
        }
        if (empty($value)) {
            $value = $this->asGender($this->product_name($row));
        }

        return $this->asString($value) ?: Gender::GENDER_BOTH;
    }
    protected function __gender($row) {
        $value = null;

        foreach(@$this->fields['gender'] ?: [] as $field)
            if(empty($value))
                $value = $this->asGender($this->getFieldValue($row, $field));

        return $value;
    }

    private function getFieldValue($row, $field_name) {
        if(preg_match('/^([a-z0-9_]+)\[([a-z0-9_]+)\]$/', $field_name, $matches)) {
            return @$row[ $matches[1] ][ $matches[2] ];
        }

        return @$row[ $field_name ];
    }

    private function image_url($row)
    {
        return $this->asString(
            $this->get('image_url', $row)
        );
    }
    protected function __image_url($row)
    {
        return $this->get_value_from_row($row, @$this->fields['image_url']);
    }

    private function livraison($row)
    {
        return $this->asString(
            $this->get('livraison', $row)
        );
    }
    protected function __livraison($row)
    {
        return $this->get_value_from_row($row, @$this->fields['livraison']);
    }

    private function categories($row)
    {
        return $this->asCategories(
            $this->__categories($row)
        );
    }
    protected function __categories($row)
    {
        $values = [];

        foreach(@$this->fields['categories'] as $field)
            if(empty($value)) {
                $data = @$row[ $field ];

                if(is_array($data))
                    $values = [...$values, ...$data];
                else
                    $values[] = $data;
            }

        return $values;
    }

    private function colors($row)
    {
        return $this->asMultiString(
            $this->__colors($row)
        );
    }
    protected function __colors($row)
    {
        return $this->get_multiples_values_from_row($row, @$this->fields['colors']);
    }

    private function sizes($row)
    {
        return $this->asMultiString(
            $this->__sizes($row)
        );
    }
    protected function __sizes($row)
    {
        return $this->get_multiples_values_from_row($row, @$this->fields['sizes']);
    }

    private function materials($row)
    {
        return $this->asMaterials(
            $this->__materials($row)
        );
    }
    protected function __materials($row)
    {
        return $this->get_value_from_row($row, @$this->fields['materials']);
    }

    private function models($row)
    {
        return $this->asString(
            $this->__models($row)
        );
    }
    protected function __models($row)
    {
        return $this->get_value_from_row($row, @$this->fields['models']);
    }

    private function styles($row)
    {
        return null;
    }

    private function currency($row)
    {
        // TODO: read this from feed and allow conversion from USD to EUR
        $value = $this->get('currency', $row);

        if(empty($value) || ctype_digit($value))
            $value = config('app.currency');

        return $this->asString(
            $value
        );
    }
    protected function __currency($row)
    {
        return $this->get_value_from_row($row, @$this->fields['currency']);
    }

    protected function age_group($row)
    {
        $brand_name = $this->brand_name($row);

        foreach ([
            'kids',
        ] as $needle) {
            if (false !== stripos($brand_name, $needle) || false !== stripos($brand_name, _i($needle))) {
                return 'kids';
            }
        }

        return mb_strtolower((string)@$row['age_group']);
    }

    private function get_string_used_for_lookup($row)
    {
        return $value = implode(' ', [
            $this->product_name($row),
            $this->description($row),
            $this->categories($row),
        ]);
    }

    protected function cols($row)
    {
        return $this->__cols(
            $this->get_string_used_for_lookup($row)
        );
    }
    protected function __cols($string)
    {
        $values = [];

        // TODO: store these values in DB and allow admin to change them
        $values = array_merge($values, $this->lookup($string, [
            'col à fronces' => null,
            'col à revers' => null,
            'col à nouer' => null,
            'col amovible' => null,
            'col asymétrique' => null,
            'col bardot' => null,
            'col bateau' => null,
            'col bénitier' => null,
            'col blouse' => null,
            'col camionneur' => null,
            'col châle' => null,
            'col cheminée' => null,
            'col classique' => null,
            'col claudine' => null,
            'col double' => null,
            'col droit' => null,
            'col escargot' => null,
            'col fauve' => null,
            'col fourré' => null,
            'col français' => null,
            'col italien' => null,
            'col lacé' => null,
            'col mao' => null,
            'col mandarin' => null,
            'col médicis' => null,
            'col montant' => null,
            'col ouvert' => null,
            'col pointu' => null,
            'col tunisien' => null,
            'col romain' => null,
            'col roulé' => null,

            'col écharpe' => 'col écharpe',
            'col echarpe' => 'col écharpe',

            'col boutonné' => 'col boutonné',

            'col v' => 'col v',
            'col en v' => 'col v',

            'col rond' => 'col rond',
            'encolure rond' => 'col rond',

            'col lavallière' => 'col lavallière',
            'col à lavallière' => 'col lavallière',
        ]));

        return $this->asMultiString($values);
    }

    protected function coupes($row)
    {
        return $this->__coupes(
            $this->get_string_used_for_lookup($row)
        );
    }

    protected function manches($row)
    {
        return $this->__manches(
            $this->get_string_used_for_lookup($row)
        );
    }

    protected function motifs($row)
    {
        return $this->__motifs(
            $this->get_string_used_for_lookup($row)
        );
    }

    protected function events($row)
    {
        return $this->__events(
            $this->get_string_used_for_lookup($row)
        );
    }

    protected function before_parse_row($row)
    {
        return $row;
    }

    protected function after_parse_row($array, $row)
    {
        // merchant_original
        $array[7] = str_ireplace([
            'affiliation',
            'standard',
        ], '', $array[7]);
        $array[7] = trim(preg_replace('/(?: -)?(?: \(?[A-Z]{2,3}\)?)+$/', '', $array[7]));

        // color_original
        $array[12] = implode(
            Product::FACET_SEPARATOR,
            array_filter(
                explode(Product::FACET_SEPARATOR, $array[12]),
                function ($str) {
                    return ! ctype_digit($str);
                }
            )
        );

        // image_url
        $array[13] = str_replace([
            'https:/image',
            'http://img.ltwebstatic.com/',
            '_09_74x102.jpg?', // Sarenza
        ], [
            'https://image',
            'https://img.ltwebstatic.com/',
            '_09_504x690.jpg?',
        ], $array[13]);

        if (($map_config_str_replace_image = @$this->source->config[Source::CONFIG_STR_REPLACE_IMAGE])
            && (1 != @$row['marketplace']) // Skip this for product from Spartoo marketplace (images are not HD)
        ) $array[13] = $this->handle_str_replace($array[13], $map_config_str_replace_image);

        // sizes
        $array[23] = mb_strtoupper($array[23]);

        return $array;
    }

    /*
        One Rule per line
        string_1=string_2
    */
    private function handle_str_replace($value, $rules_raw) {
        $rules = explode("\n", $rules_raw);

        foreach ($rules as $rule) {
            list($op1, $op2) = explode('=', $rule);
            $op1 = trim($op1);
            $op2 = trim($op2);

            $value = str_replace($op1, $op2, $value);
        }

        return trim($value);
    }

    private function clean_string($string)
    {
        $string = str_replace('\\\'', "'", $string);
        $string = str_replace('\\"', '"', $string);
        $string = strip_tags($string);
        $string = trim($string);

        return $string;
    }

    public function parse_row($row)
    {
        $row = $this->before_parse_row($row);

        $product_name = $this->product_name($row);

        if (! $brand_name = @$this->source->config[Source::CONFIG_FORCE_BRAND_NAME]) {
            $brand_name = $this->brand_name($row);
        }

        $price = $this->price($row);
        $old_price = $this->old_price($row);

        if ($price == $old_price) {
            $old_price = 0;
        }

        if (0 == $price) {
            $price = $old_price;
            $old_price = 0;
        }

        if ($old_price && $price > $old_price) {
            $temp = $price;
            $price = $old_price;
            $old_price = $temp;
        }

        if ($convert_currency_from = @$this->source->config[Source::CONFIG_CONVERT_CURRENCY_FROM]) {
            $rate = 0.8397188752; // USD

            $price *= $rate;
            $old_price *= $rate;
        }

        $price = round($price, 2);
        $old_price = round($old_price, 2);

        $reduction = ($old_price > 0) ? round((1 - ($price / $old_price)) * 100) : 0;

        $description = $this->description($row);
        $description = substr($description, 0, 10000);

        $url = $this->url($row);
        if ($url_transformer = @$this->source->config[Source::CONFIG_TRANSFORM_URL]) {
            $url = str_replace([
                '{url}',
                '{url_encoded}',
            ], [
                $url,
                urlencode($url),
            ], $url_transformer);
        }

        $merchant = $this->merchant($row);
        $currency = $this->currency($row);
        $image_url = $this->image_url($row);
        $categories = $this->categories($row);

        if (! $gender = @$this->source->config[Source::CONFIG_FORCE_GENDER]) {
            $gender = $this->gender($row);
        }

        $colors = $this->colors($row);
        $sizes = $this->sizes($row);
        $manches = $this->manches($row);
        $cols = $this->cols($row);
        $coupes = $this->coupes($row);
        $motifs = $this->motifs($row);
        $events = $this->events($row);
        $materials = $this->materials($row);
        $livraison = $this->livraison($row);
        $models = $this->models($row);
        $styles = $this->styles($row);

        if (0 == $price) {
            return $this->add_reason_for_skipping('no price');
        }

        if (empty($url)) {
            return $this->add_reason_for_skipping('no url');
        }

        if (empty($product_name)) {
            return $this->add_reason_for_skipping('no product_name');
        }

        if ($this->should_I_skip_this_row($row)) {
            return false;
        }

        if (strlen($product_name) > 35) {
            if (! empty($colors)) {
                foreach (explode('|', $colors) as $color) {
                    $color = str_replace('/', '\/', preg_quote($color));
                    $product_name = preg_replace('/(?<!\w)'.$color.'e?s?(?!\w)/i', '', $product_name);
                }
            }

            if (! empty($brand_name)) {
                $product_name = str_ireplace($brand_name, '', $product_name);
            }

            if (! empty($gender)) {
                $product_name = str_ireplace(' pour '.$gender, '', $product_name);
                $product_name = str_ireplace($gender, '', $product_name);
            }
        }

        // \p{L} matches every unicode character from every alphabet, so accents too
        $product_name = preg_replace('/^[^\p{L}]+/i', '', $product_name);  // remove whitespaces at the beginning
        // Capitalize only first letter, leave other uppercase letters alone
        $product_name = mb_ucfirst($product_name);
        $product_name = preg_replace('/\s+-\s+$/i', '', $product_name);  // remove trailing dashes (with potential surrounding whitespaces)
        $product_name = preg_replace('/ {2,}/', ' ', $product_name);  // remove multiple whitespaces
        $product_name = trim($product_name);
        $product_name = trim($product_name, ',.-');

        $product_name = substr($product_name, 0, 150);

        $product_name = $this->clean_string($product_name);
        $description = $this->clean_string($description);
        $categories = $this->clean_string($categories);

        $product_name = html_entity_decode($product_name, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401);
        $description = html_entity_decode($description, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401);
        $brand_name = html_entity_decode($brand_name, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401);

        $slug = $this->slug($row);
        if (strlen($slug) > 250) {
            $slug = substr($slug, 0, 250);
        }

        if (empty($merchant)) {
            $merchant = $this->source->title;
        }
        if (empty($brand_name)) {
            $brand_name = $merchant;
        }

        if(@$this->source->config[Source::CONFIG_DEBUG_SHOW_BRANDS_ADDED]) {
            if (! isset($this->source->brands_added[$brand_name])) {
                $this->source->brands_added[$brand_name] = 0;
            }

            $this->source->brands_added[$brand_name]++;
        }

        if(@$this->source->config[Source::CONFIG_DEBUG_SHOW_CATEGORIES_ADDED]) {
            $this->source->categories_added = array_merge(
                $this->source->categories_added,
                explode(Product::FACET_SEPARATOR, $categories)
            );
        }

        $payload = '';
        if(@$this->source->config[Source::CONFIG_DEBUG_STORE_PAYLOAD] || config('app.debug')) {
            $payload = base64_encode(json_encode(array_merge($row, [
                'import__extra' => [
                    'source__id' => $this->source->id,
                    'source__name' => $this->source->name,
                    'created_at' => date('Y-m-d H:i:s'),
                ],
            ])));
        }

        return $this->after_parse_row([
            $product_name,          //  0 - name
            $slug,                  //  1 - slug
            $description,           //  2 - description
            $price,                 //  3 - price
            $old_price,             //  4 - old_price
            $reduction,             //  5 - reduction
            $url,                   //  6 - url
            $merchant,              //  7 - merchant_original
            $brand_name,            //  8 - brand_original
            $categories,            //  9 - category_original
            $gender,                // 10 - gender
            $currency,              // 11 - currency_original
            $colors,                // 12 - color_original
            $image_url,             // 13 - image_url
            $this->source->name,    // 14 - provider
            $cols,                  // 15 - cols
            $coupes,                // 16 - coupes
            $manches,               // 17 - manches
            $materials,             // 18 - materials
            $models,                // 19 - models
            $motifs,                // 20 - motifs
            $events,                // 21 - events
            $styles,                // 22 - styles
            $sizes,                 // 23 - sizes
            $livraison,             // 24 - livraison
            $payload,               // 25 - payload
        ], $row);
    }

    protected function asCategories($values)
    {
        if(empty($values))
            return '';

        $values = array_map(function ($value) {
            if (ctype_digit((string)$value)) {
                // TODO: only do the ::find() on last call ; or store value to prevent multiples calls
                if ($category_from_google = GoogleProductCategory::find($value)) {
                    return $category_from_google->name;
                }

                return null;
            }

            return $value;
        }, $values);

        if ($category_to_append = @$this->source->config[Source::CONFIG_APPEND_CATEGORY]) {
            $values[] = $category_to_append;
        }

        $value = $this->asMultiString($values);

        return $value;
    }

    private function asString($value)
    {
        if('\N' == $value)
            return '';

        $value = trim((string)$value);

        return empty($value) ? '' : $value;
    }

    protected function asMultiString($values)
    {
        return empty($values) ? '' : mb_strtolower(implode(Product::FACET_SEPARATOR, array_filter(array_unique($values))));
    }

    private function asFloat($value)
    {
        return empty($value) ? 0 : floatval($value);
    }

    protected function asGender($string)
    {
        if (empty($string)) {
            return false;
        }

        $string = mb_strtolower($string);

        $malePattern = '/\b(men|man)s?\b/i';

        $value = false;

        if (
            'f' == $string
          || false !== strpos($string, 'femme')
          || false !== strpos($string, 'woman')
          || false !== strpos($string, 'women')
          || false !== strpos($string, 'female')
          || false !== strpos($string, 'femelle')
          || false !== strpos($string, '|women|')
          || false !== strpos($string, _i('femme'))
        ) {
            $value = Gender::GENDER_FEMALE;
        } elseif (
            'h' == $string
          || preg_match($malePattern, $string)
          || false !== strpos($string, 'homme')
          || false !== strpos($string, 'male')
          || false !== strpos($string, 'man >')
          || false !== strpos($string, 'men >')
          || false !== strpos($string, '|mens|')
          || false !== strpos($string, '|men|')
          || false !== strpos($string, _i('homme'))
        ) {
            $value = Gender::GENDER_MALE;
        } elseif (
            false !== strpos($string, 'fille')
          || false !== strpos($string, 'girl')
          || false !== strpos($string, _i('fille'))
        ) {
            $value = Gender::GENDER_GIRL;
        } elseif (
            false !== strpos($string, 'garçon')
          || false !== strpos($string, 'garcon')
          || false !== strpos($string, _i('garçon'))
        ) {
            $value = Gender::GENDER_BOY;
        } elseif (
            false !== strpos($string, 'enfant')
          || false !== strpos($string, _i('enfant'))
          || false !== strpos($string, 'kid')
          || false !== strpos($string, 'kids')
        ) {
            $value = Gender::GENDER_CHILD;
        } elseif (
            false !== strpos($string, 'unisex')
          || false !== strpos($string, _i('unisex'))
          || false !== strpos($string, 'mixte')
          || false !== strpos($string, _i('mixte'))
        ) {
            $value = Gender::GENDER_BOTH;
        }

        return $value;
    }

    protected function __coupes($string)
    {
        $values = [];

        $values = array_merge($values, $this->lookup($string, [
            'coupe oversize' => null,
            'coupe croisée' => null,
            'coupe standard' => null,
            'coupe ample' => null,
            'coupe longue' => null,
            'coupe ajustée' => null,

            'coupe slim' => 'coupe cintrée/slim',
            'coupe cintrée' => 'coupe cintrée/slim',

            'coupe droite' => 'coupe classique/droite',
            'coupe classique' => 'coupe classique/droite',
        ]));

        return $this->asMultiString($values);
    }

    protected function __manches($string)
    {
        $values = [];

        $values = array_merge($values, $this->lookup($string, [
            'sans manche' => null,

            'manches longue' => 'manches longues',
            'manche longue' => 'manches longues',

            'manches courte' => 'manches courtes',
            'manche courte' => 'manches courtes',

            'manches volant' => 'manche volant',
            'manche volant' => 'manche volant',
        ]));

        return $this->asMultiString($values);
    }

    protected function __motifs($string)
    {
        $values = [];

        $values = array_merge($values, $this->lookup($string, [
            'motif monogrammé' => null,
            'motif à pois' => null,
            'motif zig-zag' => null,
            'motif moucheté' => null,
            'motif cachemire' => null,
            'motif navajo' => null,
            'motif intarsia' => null,
            'motif losange' => null,
            'motif chevron' => null,

            'pied de poule' => 'motif pied de poule',
            'pied-de-poule' => 'motif pied de poule',

            'motif à rayures' => 'motif à rayures',
            'motif de rayures' => 'motif à rayures',

            'motif géométrique' => 'motif géométrique',
            'motif à formes géométriques' => 'motif géométrique',

            'motif à carreaux' => 'motif à carreaux',
            'imprimé à carreaux' => 'motif à carreaux',

            'floral' => 'motif floral',
            'de fleurs' => 'motif floral',
            'à fleurs' => 'motif floral',
        ]));

        return $this->asMultiString($values);
    }

    protected function __events($string)
    {
        $values = [];

        $values = array_merge($values, $this->lookup($string, [
            'week-end' => 'week-end',
            'fin de semaine' => 'week-end',

            'soirée chic' => 'soirée chic',

            'plage' => 'plage',

            'de mariage' => 'mariage',
            'un mariage' => 'mariage',
            'demoiselle d\'honneur' => 'mariage',
        ]));

        return $this->asMultiString($values);
    }

    protected function asMaterials($string)
    {
        $values = [];

        $string = remove_accents($string);
        $string = str_replace('&amp;', '', (string)$string);

        if (preg_match_all('/% ([\w\s]+)/', $string, $matches) > 0) {
            $values = array_merge($values, $matches[1]);
        } else {
            $values = array_merge($values, $this->handle_multiple_values($string));
        }

        $values = array_map(function ($v) {
            $chunks = explode(':', $v);

            if (false !== stripos($chunks[0], 'MDL') || false !== stripos($chunks[0], 'FDA')) {
                return null;
            }

            $c = preg_replace('/[0-9]{0,3}%/', '', end($chunks));

            if (false !== stripos($c, 'alcohol denat')) {
                return null;
            }

            return trim($c);
        }, $values);

        return $this->asMultiString($values);
    }

    protected function lookup($string, $rules)
    {
        $values = [];

        $string = mb_strtolower($string);

        foreach ($rules as $search => $value) {
            if (null === $value) {
                $value = $search;
            }

            if (false !== strpos($string, $search)) {
                $values[] = $value;
            }
        }

        return $values;
    }

    protected function handle_multiple_values($value, $pattern = '/[,;\/\-&]/')
    {
        if (empty($value)) {
            return [];
        }

        if (! is_array($value)) {
            $value = preg_split($pattern, $value, -1, PREG_SPLIT_NO_EMPTY);
        }

        return array_map('trim', $value);
    }

    private function add_reason_for_skipping($reason)
    {
        if (! isset($this->source->reasons_for_skipping[$reason])) {
            $this->source->reasons_for_skipping[$reason] = 0;
        }

        $this->source->reasons_for_skipping[$reason]++;

        return false;
    }

    protected function should_I_skip_this_row($row)
    {
        if (in_array($this->age_group($row), ['kids', 'enfants'])) {
            $this->add_reason_for_skipping('for kids');

            return true;
        }

        if ($this->category_is_not_fashion($row)) {
            $this->add_reason_for_skipping('not fashion ('.$this->categories($row).')');

            return true;
        }

        if ($this->product_is_not_available($row)) {
            $this->add_reason_for_skipping('not available');

            return true;
        }

        if (empty($this->image_url($row))) {
            $this->add_reason_for_skipping('no image');

            return true;
        }

        if (false !== strpos($this->image_url($row), 'noimage')) {
            $this->add_reason_for_skipping('bad image');

            return true;
        }

        if (! in_array($this->gender($row), Gender::genders())) {
            $this->add_reason_for_skipping('bad gender ('.$this->gender($row).' / '.$this->categories($row).')');

            return true;
        }

        return false;
    }

    protected function product_is_not_available($row)
    {
        foreach ([
            'availability',
            'disponibilite',
        ] as $key) {
            if (isset($row[$key])) {
                if (in_array($row[$key], [
                    'out of stock',
                ])) {
                    return true;
                }
            }
        }

        if($active = @$row['active'])
            if('0' == $active)
                return true;

        if($stock_quantity = @$row['stock_quantity']) {
            $stock_status = @$row['stock_status'];

            if('available' != $stock_status)
                if('0' == $stock_quantity)
                    return true;
        }

        return false;
    }

    protected function category_is_not_fashion($row)
    {
        $categories = $this->categories($row);

        // TODO: store these values in DB and allow admin to change them
        $values = [
            'modalova__do-not-import-me__modalova',

            'érotisme',

            'maison',
            'meuble',
            'homeware',
            'drinkware',
            'garden',
            'intelligence',
            'security',
            'lifestyle',
            'jardinage',
            'garage',
            'extérieur',
            'aménagement',
            'rangements',
            'cuisine',
            'kitchen',
            'literie',
            'oreiller',

            'petit garçon',
            'kid garçon',
            'petite fille',
            'kid fille',
            'enfant',
            'bébé',
            'fille',
            'adolescent',
            'naissance',
            'baby',
            'toddler',

            'visage + corps',
            'soin du corps',
            'soins',
            'skincare',
            'beauté',
            'beaute',
            'beauty',
            'parfum',
            'fragrance',
            'maquillage',
            'cosmetics',
            'démaquillant',

            'vin rouge',

            'automóvil',
            'motocicleta',
            'reptiles',
            'activités sportives',
            'equipments',
            'high tech',
            'vélo',
            'pièces détachées',
            'loisirs',

            'décoration',
            'papeterie',
            'fourniture',

            'électronique',
            'électroménager',
            'écouteurs',

            'mamelon',
            'intimate toy',
            'jouet',
            'jeux',
            'figurine',
            'puériculture',
            'gastronomie',

            'gift card',
            'carte cadeau',
        ];

        if('de_DE' != config('app.locale'))
            $values = array_merge($values, [
                'Book',
                'livre',
                'littérature',
            ]);

        foreach ($values as $needle) {
            if (false !== stripos($categories, $needle) || false !== stripos($categories, _i($needle))) {
                return true;
            }
        }

        return false;
    }
}
