<?php

namespace App\Models\Parsers;

class Shareasale extends BaseParser
{
    const IDENTIFIER_FOR_AFFILIATE_VALUE = 'YOURUSERID';

    public static $headers = ['productID', 'name', 'merchantId', 'merchant', 'link', 'thumbnail', 'bigImage', 'price', 'retailPrice', 'category', 'subCategory', 'description', 'custom1', 'custom2', 'custom3', 'custom4', 'custom5', 'lastUpdated', 'status', 'manufacturer', 'partNumber', 'merchantCategory', 'merchantSubcategory', 'shortDescription', 'isbn', 'upc', 'sku', 'crossSell', 'merchantGroup', 'merchantSubgroup', 'compatibleWith', 'compareTo', 'quantityDiscount', 'bestseller', 'addToCartUrl', 'reviewsRssUrl', 'option1', 'option2', 'option3', 'option4', 'option5', 'mobileUrl', 'mobileImage', 'mobileThumbnail', 'reservedForFutureUse1', 'reservedForFutureUse2', 'reservedForFutureUse3', 'reservedForFutureUse4', 'reservedForFutureUse5', 'reservedForFutureUse6', 'reservedForFutureUse7'];

    public $col_sep = '|';

    protected $fields = [
        'product_id' => [
            'productID',
        ],

        'product_name' => [
            'name',
        ],

        'brand_name' => [
            'manufacturer',
        ],

        'price' => [
            'price',
        ],

        'old_price' => [
            'retailPrice',
        ],

        'description' => [
            'description',
        ],

        'url' => [
            'link',
        ],

        'merchant' => [
            'merchant',
        ],

        'gender' => [
            'name',
            'subCategory',
        ],

        'image_url' => [
            'bigImage',
        ],

        'categories' => [
            'category',
            'subCategory',
            'merchantCategory',
            'merchantSubcategory',
        ],

        'models' => [
            'sku',
        ],
    ];

    protected function before_parse_row($row)
    {
        $row = parent::before_parse_row($row);

        // relate link to the particular affiliate account by id of user
        // checks env variable to skip that for tests
        if (!empty($row['link']) && $affiliateUserId = env('SHAREASALE_AFFILIATE_ID')) {
            $queryParams   = [];
            $urlComponents = parse_url($row['link']);

            if (!empty($urlComponents['query'])) {
                parse_str($urlComponents['query'], $queryParams);

                if (!empty($queryParams['userID']) && $queryParams['userID'] == self::IDENTIFIER_FOR_AFFILIATE_VALUE) {
                    $queryParams['userID'] = $affiliateUserId;

                    $row['link'] = $urlComponents['scheme'] . '://' . $urlComponents['host'] . $urlComponents['path'] . '?' . http_build_query($queryParams);
                }
            }
        }

        return $row;
    }

    protected function after_parse_row($array, $row)
    {
        $array = parent::after_parse_row($array, $row);

        if (preg_match('/^(.+) Size ([\.0-9]+)$/i', $array[0], $matches)) {
            // name
            $array[0] = $matches[1];

            // sizes
            $array[23] = trim($matches[2]);

            // generate slug with final $product_name
            $array[1] = $this->__slug_real($array[0], @$row['productID']);
        }

        if ('43363' == $row['merchantId']) {
            if (preg_match('/(?<=^|,)([XSML]{1,3}(?:,|$))+/i', $row['custom2'], $matches)) {
                // sizes
                $array[23] = $this->asMultiString($this->handle_multiple_values($matches[0]));
            }

            if (preg_match('/(?<=^|,)([^,]*sleeve[^,]*)(?=,|$)/i', $row['custom2'], $matches)) {
                // sleeves
                $array[17] = trim($matches[0]);
            }

            if (preg_match('/(?<=^|,)([^,]*neck[^,]*)(?=,|$)/i', $row['custom2'], $matches)) {
                // col/collar
                $array[15] = trim($matches[0]);
            }
        }

        if ('77093' == $row['merchantId']) {
            if (preg_match('/^(.+)-([a-zA-Z\s]+)$/i', $array[0], $matches)) {
                // name
                $array[0] = $matches[1];

                // color
                $array[12] = trim($matches[2]);

                // generate slug with final $product_name
                $array[1] = $this->__slug_real($array[0], @$row['productID']);
            }
        }

        if ('10669' == $row['merchantId']) {
            if (preg_match('/^.+ Color: ([^,]+), .+$/i', $array[2], $matches)) {
                // color
                $array[12] = trim($matches[1]);
            }

            if (preg_match('/^.+ Materials: (.+), Size: .+$/i', $array[2], $matches)) {
                // material
                $array[18] = trim($matches[1]);
            }

            if (preg_match('/^.+ Size: (.+), Care: .+$/i', $array[2], $matches)) {
                // size
                $array[23] = trim($matches[1]);
            }
        }

        if ('109911' == $row['merchantId']) {
            if (preg_match('/Material: ([a-zA-Z, ]+)  (?=[a-zA-z ]*:|$)/i', $array[2], $matches)) {
                $array[18] = trim($matches[1]);
            }

            if (preg_match('/(?:Style|Clothes style): ([a-zA-Z, ]+)(?=  [a-zA-z ]*:|$)/i', $array[2], $matches)) {
                // style
                $array[22] = trim($matches[1]);
            }
        }

        if ('120052' == $row['merchantId']) {
            if (preg_match('/^.*Occasion: (.+)\s?Color: .+$/i', $array[2], $matches)) {
                // event
                $array[21] = trim($matches[1]);
            }

            if (preg_match('/^.*Color: (.+)\s?Pattern: .+$/i', $array[2], $matches)) {
                // color
                $array[12] = trim($matches[1]);
            }

            if (preg_match('/^.*Pattern: (.+)\s?Material: .+$/i', $array[2], $matches)) {
                // motifs/patterns
                $array[20] = trim($matches[1]);
            }

            if (preg_match('/^.*Material: (.+)\s?Design Element: .+$/i', $array[2], $matches)) {
                // material
                $array[18] = trim($matches[1]);
            }

            if (preg_match('/^.*Collar: (.+)\s?Size: .+$/i', $array[2], $matches)) {
                // col/collar
                $array[15] = trim($matches[1]);
            }

            if (preg_match('/^.*Size: (.+)\s?Sleeves Length: .+$/i', $array[2], $matches)) {
                // size
                $array[23] = trim($matches[1]);
            }

            if (preg_match('/^.*Sleeves Length: (.+)\s?Fit Type: .+$/i', $array[2], $matches)) {
                // sleeves
                $array[17] = trim($matches[1]);
            }

            if (preg_match('/^.*Fit Type: (.+)\s?Season: .+$/i', $array[2], $matches)) {
                // coupe/fit type
                $array[16] = trim($matches[1]);
            }
        }

        return $array;
    }

    protected function __gender($row)
    {
        if ('56695' == $row['merchantId'] && !empty($row['custom2'])) {
            $this->fields['gender'][] = 'custom2';
        }

        return parent::__gender($row);
    }

    protected function __livraison($row)
    {
        if ('43363' == $row['merchantId']) {
            return $row['custom4'] . ' ' . $row['custom3'];
        }

        return parent::__livraison($row);
    }

    protected function __categories($row)
    {
        $values = parent::__categories($row);

        if ('43363' == $row['merchantId'] && !empty($row['custom1'])) {
            $values = [...$values, ...explode('>', $row['custom1'])];
        }

        return $values;
    }

    protected function __colors($row)
    {
        $values = parent::__colors($row);

        if ('56695' == $row['merchantId'] && !empty($row['custom1'])) {
            $values[] = $row['custom1'];
        }

        return $values;
    }

    protected function __sizes($row)
    {
        $values = parent::__sizes($row);

        if ('56695' == $row['merchantId'] && !empty($row['custom4'])) {
            $values[] = $row['custom4'];
        }

        return $values;
    }

    protected function __materials($row)
    {
        $value = parent::__materials($row);

        if ('56695' == $row['merchantId'] && !empty($row['custom3'])) {
            $value = $row['custom3'];
        }

        return $value;
    }
}
