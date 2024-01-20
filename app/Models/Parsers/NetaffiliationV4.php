<?php

namespace App\Models\Parsers;

use App\Models\Gender;

class NetaffiliationV4 extends BaseParser
{
    public $col_sep = '|';

    protected $fields = [
        'product_id' => [
            'ean',
            'internal reference',
            'reference interne',
            'référence interne',
            'Internal reference',
            'R_f_rence Interne',
            'reference_interne',
            'EAN or ISBN',
            'gtin',
            'EAN',
            'id',
            'ID',
            'ID_PRODUCT',
            'identifiant',
        ],

        'product_name' => [
            'titre',
            'name of the product',
            'Nom',
            'title',
            'name',
            'nom',
            'Product Name',
            'Name',
            'NAME',
        ],

        'brand_name' => [
            'marque',
            'brand',
            'Nom de la marque',
            'Brand',
            'MARQUE',
        ],

        'price' => [
            'prix',
            'current price',
            'prix actuel',
            'sale_price',
            'sale-price',
            'Current Price',
            'Montant (TTC)',
            'price',
            'Current price',
            'Price',
            'current_price',
            'TTC_AR',
            'prix_actuel',
            'prix soldé',
        ],

        'old_price' => [
            'prix_barre',
            'crossed price',
            'regular-price',
            'original_price',
            'Crossed Price',
            'Crossed price',
            'price_old',
            'crossed_price',
            'TTC_SR',
            'old price',
            'fromPrice',
            'price',
        ],

        'description' => [
            'description',
            'descriptif',
            'Discription of the product',
            'Description',
            'DESCRIPTION',
        ],

        'url' => [
            'URL_produit',
            'URLproduit',
            'product page URL',
            'link',
            'url',
            'URL de la page produit',
            'product_url',
            'Product page URL',
            'URL de la page',
            'URL',
            'product link',
            'productURL',
            'productUrl',
            'product url',
            'lien',
        ],

        'merchant' => [
            'programName',
        ],

        'gender' => [
            'gender',
            'genre',
            'sexe',
        ],

        'image_url' => [
            'big image',
            'Big image',
            'image_big',
            'URL image grande',
            'URL of the big image',
            'URL_image_grande',
            'URL_image',
            'image_link',
            'image-url',
            'image_url',
            'URL Image',
            'image',
            'IMAGE_1',
            'image link',
            'imageURL',
            'imageUrl',
            'image url',
            'lien image',
        ],

        'livraison' => [
            'delai_de_livraison',
            'Délai de livraison',
        ],

        'categories' => [
            'categorie',
            'product category',
            'product_type',
            'google_product_category',
            'adwords_grouping',
            'category-breadcrumb',
            'catégorie',
            'categories',
            'subcategories',
            'subsubcategories',
            'Product category',
            'Cat_gorie Fil d\'ariane',
            'Category',
            'category',
            'CATEGORIE',
            'Categorie_finale',
            'catégorie de produits Google',
        ],

        'colors' => [
            'couleur',
            'Couleur',
            'Couleurs',
            'color',
            'Color',
        ],

        'sizes' => [
            'taille',
            'Taille',
            'Tailles',
            'size',
            'Size',
            'SIZE',
            'pointure',
        ],

        'materials' => [
            'matiere',
            'Matière',
            'outside-sole-material',
            'inside-sole-material',
            'lining-material',
            'material',
        ],

        'models' => [
            'model',
        ],
    ];

    protected function before_parse_row($row)
    {
        $row = parent::before_parse_row($row);

        $row['titre'] = preg_replace('/^promo : /i', '', (string)@$row['titre']);

        if($fields = @$row['fields'])
            if(is_string($fields)) {
                try {
                    $temp = array_map(function($v) {
                        return preg_split('/(?<!g):/', $v, 2);
                    }, explode(';', $fields));

                    $row['fields'] = array_combine(
                        array_map(function($v) { return $v[0]; }, $temp),
                        array_map(function($v) { return $v[1]; }, $temp)
                    );
                } catch (\Exception $e) {
                    \Log::err("[-] NetaffiliationV4: could not parse 'fields': '$fields'");
                }
            }

        return $row;
    }

    protected function __product_id($row)
    {
        $value = parent::__product_id($row);

        if (empty($value)) {
            if($fields = @$row['fields'])
                if(is_array($fields))
                    $value = @$fields['ProductoID'];
        }

        return $value;
    }

    protected function __brand_name($row)
    {
        $value = parent::__brand_name($row);

        if ('La Boutique du Haut Talon' == $this->__merchant($row)) {
            $value = str_replace([
                'Chaussures femmes ',
                'Chaussures femme ',
                'Chaussures ',
                'Mode Femme ',
                'Mode ',
            ], '', $value);
        }

        return $value;
    }

    protected function __gender($row)
    {
        $value = parent::__gender($row);

        if (empty($value)) {
            if($fields = @$row['fields'])
                if(is_array($fields))
                    $value = $this->asGender(@$fields['g_gender']);
        }

        return $value;
    }

    protected function __categories($row)
    {
        $values = parent::__categories($row);

        if($fields = @$row['fields']) {
            if(is_array($fields)) {
                $values[] = @$fields['Categoria'];
            }
        }

        return $values;
    }

    protected function __colors($row)
    {
        $values = parent::__colors($row);

        if($fields = @$row['fields'])
            if(is_array($fields)) {
                $values[] = @$fields['g_color'];
            }

        return $values;
    }

    protected function __sizes($row)
    {
        $values = parent::__sizes($row);

        if($fields = @$row['fields'])
            if(is_array($fields))
                $values[] = @$fields['g_size'];

        return $values;
    }

    protected function __materials($row)
    {
        $value = parent::__materials($row);

        if (empty($value)) {
            if($fields = @$row['fields'])
                if(is_array($fields))
                    $value = @$fields['g_material'];
        }

        return $value;
    }
}
