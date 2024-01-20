<?php

namespace Tests\Models\Parsers;

use App\Models\Parsers\Admitad;

class AdmitadTest extends BaseParser
{
    public static $klass = Admitad::class;

    public function test__parse_row__from_Cotosen()
    {
        // given
        $headers = 'available,categoryId,currencyId,description,id,modified_time,name,picture,price,shippingPrice,type,url,vendor';
        $payload = 'WyJ0cnVlIiwiU3dlYXRzIFx1MDBlMCBjYXB1Y2hlIGV0IHN3ZWF0LXNoaXJ0cyIsIkVVUiIsInN3ZWF0cyBcdTAwZTAgY2FwdWNoZSBldCBzd2VhdC1zaGlydHMsc3dlYXQtc2hpcnQgXHUwMGUwIGNvbCBtb250YW50IGF2ZWMgZmVybWV0dXJlIFx1MDBlOWNsYWlyIHZpbnRhZ2Ugd2VzdGVybiB5ZWxsb3dzdG9uZSBwb3VyIGhvbW1lcyIsIlNQMjIxMjAzMllHMCIsIjE2OTMzMDQ4MzAiLCJTd2VhdC1zaGlydCBcdTAwZTAgQ29sIE1vbnRhbnQgQXZlYyBGZXJtZXR1cmUgXHUwMGU5Y2xhaXIgVmludGFnZSBXZXN0ZXJuIFllbGxvd3N0b25lIFBvdXIgSG9tbWVzIiwiaHR0cHM6XC9cL3Vwcy5hb3BjZG4uY29tXC9zMzk2NTVcL2dvb2RzXC8xOTMzMVwvLTJ1YmIwYmIxYjNlNjUxNDI0NGJiMzdkN2Y2MWI4ZjhkMjUuanBnQCFoOTAwLXc5MDAiLCIyNy45OSIsIjEyLjk5IiwiIiwiaHR0cHM6XC9cL2FkLmFkbWl0YWQuY29tXC9nXC83ZXoyY25keThrNzVjNTk4NThlNmZlOWNmNzYyYTVcLz9mX2lkPTIyMzE5JnVscD1odHRwcyUzQSUyRiUyRnd3dy5jb3Rvc2VuLmNvbSUyRnByb2R1Y3RzJTJGbWVuLXMtYXp0ZWMtaG9vZGllLXZpbnRhZ2Utd2VzdGVybi15ZWxsb3dzdG9uZS1jb2xvcmJsb2NrLXppcHBlci1zdGFuZC1jb2xsYXItc3dlYXRzaGlydC03NDEwMjgxLmh0bWwlM0Zsb2NhbGUlM0RmciUyNmNjeSUzREVVUiUyNm1vbml0b3IlM0RGUiIsIkNPVE9TRU4iXQ==';


        // when
        $expected_value = [
            'name'              => 'Sweat-shirt à Col Montant Avec Fermeture éclair Vintage Western Yellowstones',
            'slug'              => 'sweat-shirt-col-montant-avec-fermeture-clair-vintage-western-yellowstone-pour-hommes-sp2212032yg0',
            'description'       => 'sweats à capuche et sweat-shirts,sweat-shirt à col montant avec fermeture éclair vintage western yellowstone pour hommes',
            'brand_original'    => 'COTOSEN',
            'merchant_original' => 'COTOSEN',
            'currency_original' => 'EUR',
            'category_original' => 'sweats à capuche et sweat-shirts',
            'color_original'    => '',
            'price'             => 27.99,
            'old_price'         => 0.0,
            'reduction'         => 0,
            'url'               => 'https://ad.admitad.com/g/7ez2cndy8k75c59858e6fe9cf762a5/?f_id=22319&ulp=https%3A%2F%2Fwww.cotosen.com%2Fproducts%2Fmen-s-aztec-hoodie-vintage-western-yellowstone-colorblock-zipper-stand-collar-sweatshirt-7410281.html%3Flocale%3Dfr%26ccy%3DEUR%26monitor%3DFR',
            'image_url'         => 'https://ups.aopcdn.com/s39655/goods/19331/-2ubb0bb1b3e6514244bb37d7f61b8f8d25.jpg@!h900-w900',
            'gender'            => 'homme',
            'col'               => 'col montant',
            'coupe'             => '',
            'manches'           => '',
            'material'          => '',
            'model'             => null,
            'motifs'            => '',
            'event'             => '',
            'style'             => null,
            'size'              => '',
            'livraison'         => '12.99',
        ];

        // then
        $this->assertEquals(
            $expected_value,
            $this->parse_payload($payload, $headers)
        );
    }

    public function test__parse_row__from_Bellelily()
    {
        // given
        $headers = 'available,categoryId,currencyId,description,id,model,modified_time,name,oldprice,param,picture,price,type,url,vendor';
        $payload = 'WyJ0cnVlIiwiVGFua3MiLCJFVVIiLCJJdGVtICM6IFNDTTAxNzk3Ml8xIFBhY2thZ2UgOiAxIHBpZWNlIENsb3RoaW5nIExlbmd0aCA6IFJlZ3VsYXIgQ29sbGFyIDogU2Nvb3AgTmVja2xpbmUgR2VuZGVyIDogV29tZW4gTWF0ZXJpYWwgOiBQb2x5ZXN0ZXIgUGF0dGVybiBUeXBlIDogTGV0dGVyIFNlYXNvbiA6IFN1bW1lciBTbGVldmUgTGVuZ3RoIDogU2xlZXZlbGVzcyBTbGVldmUgU3R5bGUgOiBUYW5rIFN0eWxlIDogU3dlZXQgVG9wcyBUeXBlIDogVGFuayBUb3BzIE9jY2FzaW9uIDogSG9saWRheSBPY2Nhc2lvbiA6IE91dGRvb3IgT2NjYXNpb24gOiBWYWNhdGlvbiBJdGVtIFdlaWdodCA6IDE0MC4wMCBncmFtIiwiU0NNMDE3OTcyIiwiU0NNMDE3OTcyIiwiMTY5MzIzNTMxMyIsIkNvbWUgT24gTGV0J3MgR28gUGFydHkgVGFuayAtIFBpbmsiLCIxMy43NyIsImNvbG9yOlBpbmt8c2l6ZToyWEwsM1hMLE0sUyxYTCIsImh0dHBzOlwvXC9pbWFnZXMuYmVsbGVsaWx5LmNvbVwvMjAyM1wvMDZcLzE1XC9TQ00wMTc5NzJfMV82MTUwODAzNzg1OTYwMl8xMjIuanBnIiwiMTAuNTkiLCIiLCJodHRwczpcL1wvYWQuYWRtaXRhZC5jb21cL2dcL3F4a2Q5aTgxdGE3NWM1OTg1OGU2MGFiMDExYTMzZlwvP2ZfaWQ9MjQ1MTAmdWxwPWh0dHBzJTNBJTJGJTJGd3d3LmJlbGxlbGlseS5jb20lMkZDb21lLU9uLUxldHMtR28tUGFydHktVGFuay0tLVBpbmstZy0xMDUwMTUtNTg3ODg5JTNGY3VycmVuY3klM0RFVVIiLCJCZWxsZWxpbHkiXQ==';


        // when
        $expected_value = [
            'name'              => 'Come On Let\'s Go Party Tank - Pink',
            'slug'              => 'come-on-let-s-go-party-tank-pink-scm017972',
            'description'       => 'Item #: SCM017972_1 Package : 1 piece Clothing Length : Regular Collar : Scoop Neckline Gender : Women Material : Polyester Pattern Type : Letter Season : Summer Sleeve Length : Sleeveless Sleeve Style : Tank Style : Sweet Tops Type : Tank Tops Occasion : Holiday Occasion : Outdoor Occasion : Vacation Item Weight : 140.00 gram',
            'brand_original'    => 'Bellelily',
            'merchant_original' => 'Bellelily',
            'currency_original' => 'EUR',
            'category_original' => 'tanks',
            'color_original'    => '',
            'price'             => 10.59,
            'old_price'         => 13.77,
            'reduction'         => 23.0,
            'url'               => 'https://ad.admitad.com/g/qxkd9i81ta75c59858e60ab011a33f/?f_id=24510&ulp=https%3A%2F%2Fwww.bellelily.com%2FCome-On-Lets-Go-Party-Tank---Pink-g-105015-587889%3Fcurrency%3DEUR',
            'image_url'         => 'https://images.bellelily.com/2023/06/15/SCM017972_1_61508037859602_122.jpg',
            'gender'            => 'mixte',
            'col'               => '',
            'coupe'             => '',
            'manches'           => '',
            'material'          => '',
            'model'             => null,
            'motifs'            => '',
            'event'             => '',
            'style'             => null,
            'size'              => '',
            'livraison'         => '',
        ];

        // then
        $this->assertEquals(
            $expected_value,
            $this->parse_payload($payload, $headers)
        );
    }

    public function test__parse_row__from_LuxuryCloset_material_size()
    {
        // given
        $headers = 'android_app_name,android_package,android_url,available,bracelet_material,categories,categoryId,condition_detail,currencyId,custom_label_3,custom_label_4,description,dimensions,gemstones,icon_media_url,id,includes,ios_app_name,ios_app_store_id,ios_url,material,mobile_ios_app_link,mobile_ios_app_store_id,modified_time,movement_type,name,oldprice,orp,param,picture,price,promotion_id,shopping_ads_excluded_country,style,type,url,vendor';
        $payload = 'WyJMdXh1cnkgQ2xvc2V0IiwiY29tLnRoZWx1eHVyeWNsb3NldC50Y2xhcHBsaWNhdGlvbiIsImluYXBwbHV4dXJ5Y2xvc2V0OlwvXC90eXBlPXNwcCZ2YWx1ZT04MjE4MzIiLCJ0cnVlIiwiIiwiV29tZW4sIFdvbWVuJ3MgSGFuZGJhZ3MsIFNob3VsZGVyIEJhZ3MiLCJTaG91bGRlciBCYWdzIiwiR29vZCAtIEluIGdvb2QgY29uZGl0aW9uIHdpdGggbWlub3Igc2N1ZmZzLCBjcmVhc2UgJmFtcDsgbWFya3MgaW4gc29tZSBhcmVhcywgc3RhaW5zIG9uIGxpbmluZywgd2hpdGUgbWFya3Mgb24gaGFuZGxlXC9zdHJhcC4iLCJVU0QiLCJJUyIsIkdvb2QiLCJUaGlzIENobG9lIGFjY2Vzc29yeSBpcyBhbiBleGFtcGxlIG9mIHRoZSBicmFuZHMgZmluZSBkZXNpZ25zIHRoYXQgYXJlIHNraWxsZnVsbHkgY3JhZnRlZCB0byBwcm9qZWN0IGEgY2xhc3NpYyBjaGFybS4gSXQgaXMgYSBmdW5jdGlvbmFsIGNyZWF0aW9uIHdpdGggYW4gZWxldmF0aW5nIGFwcGVhbC4iLCJIZWlnaHQ6IDE3LjUgY20sIFdpZHRoOiAxMCBjbSwgTGVuZ3RoOiAxNSBjbSAoYm90dG9tKSIsIiIsImh0dHBzOlwvXC9jZG4udGhlbHV4dXJ5Y2xvc2V0LmNvbVwvdXBsb2Fkc1wvYXNzZXRzXC9hcHBzXC90bGMucG5nIiwiODIxODMyIiwiVGhlIEx1eHVyeSBDbG9zZXQgUGFja2FnaW5nLCBEZXRhY2hhYmxlIFN0cmFwIiwiVGhlIEx1eHVyeSBDbG9zZXQgLSBCdXkgJiBTZWxsIiwiMTA4NTQ3MDk5MSIsImluYXBwbHV4dXJ5Y2xvc2V0OlwvXC90eXBlPXNwcCZ2YWx1ZT04MjE4MzIiLCJMZWF0aGVyIiwiaW5hcHBsdXh1cnljbG9zZXQ6XC9cL3R5cGU9c3BwJnZhbHVlPTgyMTgzMiIsIjEwODU0NzA5OTEiLCIxNjkzMjM2ODk1IiwiIiwiQ2hsb2UgVHJpIENvbG9yIExlYXRoZXIgTWluaSBSb3kgQnVja2V0IEJhZyIsIiIsIjIwMDAgVVNEIiwiZ2VuZGVyOmZlbWFsZXxjb2xvcjpNdWx0aWNvbG9yIiwiaHR0cDpcL1wvY2RuLnRoZWx1eHVyeWNsb3NldC5jb21cL3VwbG9hZHNcL3Byb2R1Y3RzXC9mdWxsXC9sdXh1cnktd29tZW4tY2hsb2UtdXNlZC1oYW5kYmFncy1wODIxODMyLTAxMi5qcGciLCI0OTciLCIiLCIiLCJSb3kiLCIiLCJodHRwczpcL1wvYWQuYWRtaXRhZC5jb21cL2dcL3F1aDI2eHZ2bjg3NWM1OTg1OGU2NDQxM2U4ZmMxYlwvP2ZfaWQ9MjQ1MTEmdWxwPWh0dHBzJTNBJTJGJTJGdGhlbHV4dXJ5Y2xvc2V0LmNvbSUyRnVzLWVuJTJGd29tZW4lMkZjaGxvZS10cmktY29sb3ItbGVhdGhlci1taW5pLXJveS1idWNrZXQtYmFnLXA4MjE4MzIiLCJDaGxvZSJd';


        // when
        $expected_value = [
            'name'              => 'Chloe Tri Color Leather Mini Roy Bucket Bag',
            'slug'              => 'chloe-tri-color-leather-mini-roy-bucket-bag-821832',
            'description'       => 'This Chloe accessory is an example of the brands fine designs that are skillfully crafted to project a classic charm. It is a functional creation with an elevating appeal.',
            'brand_original'    => 'Chloe',
            'merchant_original' => 'Chloe',
            'currency_original' => 'USD',
            'category_original' => 'shoulder bags|women, women\'s handbags, shoulder bags',
            'color_original'    => '',
            'price'             => 497.0,
            'old_price'         => 0.0,
            'reduction'         => 0,
            'url'               => 'https://ad.admitad.com/g/quh26xvvn875c59858e64413e8fc1b/?f_id=24511&ulp=https%3A%2F%2Ftheluxurycloset.com%2Fus-en%2Fwomen%2Fchloe-tri-color-leather-mini-roy-bucket-bag-p821832',
            'image_url'         => 'http://cdn.theluxurycloset.com/uploads/products/full/luxury-women-chloe-used-handbags-p821832-012.jpg',
            'gender'            => 'femme',
            'col'               => '',
            'coupe'             => '',
            'manches'           => '',
            'material'          => 'leather',
            'model'             => null,
            'motifs'            => '',
            'event'             => '',
            'style'             => null,
            'size'              => 'HEIGHT: 17.5 CM|WIDTH: 10 CM|LENGTH: 15 CM (BOTTOM)',
            'livraison'         => '',
        ];

        // then
        $this->assertEquals(
            $expected_value,
            $this->parse_payload($payload, $headers)
        );
    }

//    public function test__parse_row__from_Newchic_color_size()
//    {
//        // given
//        $headers = 'Color,Size,available,categoryId,category_name,currencyId,description,id,local_delivery_cost,modified_time,name,offer_id,oldprice,picture,price,topseller,type,url,vendor';
//        $payload = 'WyJCbGFjayxOYXZ5IiwiUyxNLEwsWEwsMlhMLDNYTCw0WEwsNVhMIiwiYXZhaWxhYmxlIiwiMzY4OSIsIjM5ZWE0MGUxNjQiLCJFVVIiLCJDaGVtaXNpZXIgZmVtbWUgYm9oXHUwMGU4bWUgXHUwMGUwIGltcHJpbVx1MDBlOSBmbGV1cmkgZXQgbWFuY2hlcyBcdTAwZTl2YXNcdTAwZTllcyIsIlNLVUkxNjY3OCIsIjcuNTIgRVVSIiwiMTY5MzMwNjU4NyIsIkNoZW1pc2llciBmZW1tZSBib2hcdTAwZThtZSBcdTAwZTAgaW1wcmltXHUwMGU5IGZsZXVyaSBldCBtYW5jaGVzIFx1MDBlOXZhc1x1MDBlOWVzIiwiIiwiIiwiaHR0cHM6XC9cL2ltZ2F6MS5jaGljY2RuLmNvbVwvdGh1bWJcL2xhcmdlXC9vYXVwbG9hZFwvbmV3Y2hpY1wvaW1hZ2VzXC82MlwvMThcLzViYTUzYzUxLTYwMzgtNDRjNy1iNjFhLTA5YmI3Y2IzMGRmYS5qcGc/cz03MDJ4OTM2IiwiMTYuOTMiLCJUdXJlIiwiIiwiaHR0cHM6XC9cL2FkLmFkbWl0YWQuY29tXC9nXC8waTRqc3p2d2lwNzVjNTk4NThlNjM0NDI4NTBmMDRcLz9mX2lkPTE4MzE5JnVscD1odHRwcyUzQSUyRiUyRmZyLm5ld2NoaWMuY29tJTJGemFuemVhLWJsb3VzZXMtYW5kLXNoaXJ0cy0zNjg5JTJGcC0xODg5OTg2Lmh0bWwlM0Zjb3VudHJ5JTNENzMiLCJuZXdjaGljIl0=';
//
//
//        // when
//        $expected_value = [
//            'name'              => 'Chemisier bohème à imprimé fleuri et manches évasées',
//            'slug'              => 'chemisier-femme-boh-me-imprim-fleuri-et-manches-vas-es-skui16678',
//            'description'       => 'Chemisier femme bohème à imprimé fleuri et manches évasées',
//            'brand_original'    => 'newchic',
//            'merchant_original' => 'newchic',
//            'currency_original' => 'EUR',
//            'category_original' => '',
//            'color_original'    => 'black|navy',
//            'price'             => 16.93,
//            'old_price'         => 0.0,
//            'reduction'         => 0,
//            'url'               => 'https://ad.admitad.com/g/0i4jszvwip75c59858e63442850f04/?f_id=18319&ulp=https%3A%2F%2Ffr.newchic.com%2Fzanzea-blouses-and-shirts-3689%2Fp-1889986.html%3Fcountry%3D73',
//            'image_url'         => 'https://imgaz1.chiccdn.com/thumb/large/oaupload/newchic/images/62/18/5ba53c51-6038-44c7-b61a-09bb7cb30dfa.jpg?s=702x936',
//            'gender'            => 'femme',
//            'col'               => '',
//            'coupe'             => '',
//            'manches'           => '',
//            'material'          => '',
//            'model'             => null,
//            'motifs'            => '',
//            'event'             => '',
//            'style'             => null,
//            'size'              => 'S|M|L|XL|2XL|3XL|4XL|5XL',
//            'livraison'         => '7.52 EUR',
//        ];
//
//        // then
//        $this->assertEquals(
//            $expected_value,
//            $this->parse_payload($payload, $headers)
//        );
//    }

}
