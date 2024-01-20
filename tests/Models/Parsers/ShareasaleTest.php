<?php

namespace Tests\Models\Parsers;

use App\Models\Parsers\Shareasale;

class ShareasaleTest extends BaseParser
{
    public static $klass = Shareasale::class;

    public static $headers = 'productID,name,merchantId,merchant,link,thumbnail,bigImage,price,retailPrice,category,subCategory,description,custom1,custom2,custom3,custom4,custom5,lastUpdated,status,manufacturer,partNumber,merchantCategory,merchantSubcategory,shortDescription,isbn,upc,sku,crossSell,merchantGroup,merchantSubgroup,compatibleWith,compareTo,quantityDiscount,bestseller,addToCartUrl,reviewsRssUrl,option1,option2,option3,option4,option5,mobileUrl,mobileImage,mobileThumbnail,reservedForFutureUse1,reservedForFutureUse2,reservedForFutureUse3,reservedForFutureUse4,reservedForFutureUse5,reservedForFutureUse6,reservedForFutureUse7';

    public function test__parse_row__from_modlily_prices()
    {
        // given
        $headers = self::$headers;
        $payload = 'WyIxMDc2MjA0NDkwIiwiU3RyaXBlIFByaW50IFNsZWV2ZWxlc3MgSGlnaCBXYWlzdCBEcmVzcyIsIjQzMzYzIiwibW9kbGlseS5jb20iLCJodHRwczpcL1wvd3d3LnNoYXJlYXNhbGUuY29tXC9tLXByLmNmbT9tZXJjaGFudElEPTQzMzYzJnVzZXJJRD1ZT1VSVVNFUklEJnByb2R1Y3RJRD0xMDc2MjA0NDkwIiwiaHR0cDpcL1wvd3d3Lm1vZGxpbHkuY29tXC9pbWFnZXNcLzIwMTkwNVwvdGh1bWJfaW1nXC8yMjEyMDBfdGh1bWJfR18xNTU3ODI3Njk2Njc3MC5qcGciLCJodHRwOlwvXC93d3cubW9kbGlseS5jb21cL2ltYWdlc1wvMjAxOTA1XC9nb29kc19pbWdcLzIyMTIwMF9HXzE1NTc4Mjc2OTY3NTIwLmpwZyIsIjMzLjk4IiwiNTIuMDAiLCJNaWxpdGFyeSIsIkdpZnRzIiwiIiwiRHJlc3NlcyIsIkdpbmdlcixCb2hlbWlhbixTdHJpcGUsUm91bmQgbmVjayxUYW5rLFNsZWV2ZWxlc3MsSGlnaCB3YWlzdCxZZWxsb3csOTUlIFBvbHllc3RlciwgNSUgU3BhbmRleCxNaWQgQ2FsZixIYW5kIFdhc2ggXC9NYWNoaW5lIFdhc2hhYmxlLDEgWCBEcmVzcywxMTguMDAsUGFydHksU3VtbWVyLFhTLFMsTSxMLFhMLFhYTCIsIlNoaXBwaW5nIHdvcmxkd2lkZSIsIk5vIE1pbmltdW0gT3JkZXIgQW1vdW50ISIsIk1vZGxpbHkuY29tLS1Qcm9mZXNzaW9uYWwgd2hvbGVzYWxlIENsb3RoaW5nIE1hcnQhIiwiMjAyMi0wNi0wOCAyMzoxNDoxMC40NDAiLCJpbnN0b2NrIiwidW5zaWduZWQiLCJub25lIiwiRHJlc3MiLCJEcmVzcyIsIlN0cmlwZSBQcmludCBTbGVldmVsZXNzIEhpZ2ggV2Fpc3QgRHJlc3MiLCIxIiwiMSIsIjE5MDMyOTA1MCIsIiIsIkZhc2hpb24gRHJlc3NlcyIsIldob2xlc2FsZSBEcmVzc2VzIiwiIiwiIiwiIiwiMCIsImh0dHBzOlwvXC93d3cuc2hhcmVhc2FsZS5jb21cL20tcHIuY2ZtP21lcmNoYW50SUQ9NDMzNjMmdXNlcklEPVlPVVJVU0VSSUQmYXRjPTEmcHJvZHVjdElEPTEwNzYyMDQ0OTAiLCIiLCIiLCIiLCIiLCIiLCIiLCJodHRwczpcL1wvd3d3LnNoYXJlYXNhbGUuY29tXC9tLXByLmNmbT9tZXJjaGFudElEPTQzMzYzJnVzZXJJRD1ZT1VSVVNFUklEJm1vYmlsZT0xJnByb2R1Y3RJRD0xMDc2MjA0NDkwIiwiIiwiIiwiIiwiIiwiIiwiIiwiIiwiIiwiIl0=';

        // when
        $expected_value = [
            'name' => 'Stripe Print Sleeveless High Waist Dress',
            'slug' => 'stripe-print-sleeveless-high-waist-dress-1076204490',
            'description' => '',
            'brand_original' => 'unsigned',
            'merchant_original' => 'modlily.com',
            'currency_original' => 'EUR',
            'category_original' => 'military|gifts|dress|dresses',
            'color_original' => '',
            'price' => 33.98,
            'old_price' => 52.0,
            'reduction' => 35.0,
            'url' => 'https://www.shareasale.com/m-pr.cfm?merchantID=43363&userID=YOURUSERID&productID=1076204490',
            'image_url' => 'http://www.modlily.com/images/201905/goods_img/221200_G_15578276967520.jpg',
            'gender' => 'mixte',
            'col' => 'Round neck',
            'coupe' => '',
            'manches' => 'Sleeveless',
            'material' => '',
            'model' => '190329050',
            'motifs' => '',
            'event' => '',
            'style' => NULL,
            'size' => 'xs|s|m|l|xl|xxl',
            'livraison' => 'No Minimum Order Amount! Shipping worldwide',
        ];

        // then
        $this->assertEquals(
            $expected_value,
            $this->parse_payload($payload, $headers)
        );
    }

    public function test__parse_row__from_YesStyle_color_and_additional_attributes()
    {
        // given
        $headers = self::$headers;
        $payload = 'WyIxMjEyMzQxNzg1IiwiUG9pbnRlbGxlIFN3ZWF0ZXIiLCIxMDY2OSIsIlllc1N0eWxlLmNvbSIsImh0dHBzOlwvXC93d3cuc2hhcmVhc2FsZS5jb21cL20tcHIuY2ZtP21lcmNoYW50SUQ9MTA2NjkmdXNlcklEPVlPVVJVU0VSSUQmcHJvZHVjdElEPTEyMTIzNDE3ODUiLCJodHRwOlwvXC9hZmYueXNpLmJ6XC9hc3NldHNcLzY0XC8zMTBcL3AwMTUwMTMxMDY0LmpwZyIsImh0dHA6XC9cL2FmZi55c2kuYnpcL2Fzc2V0c1wvNjRcLzMxMFwvbF9wMDE1MDEzMTA2NC5qcGciLCIyMC40MCIsIjIwLjQwIiwiRmFzaGlvbiIsIldvbWVucyIsIkJyYW5kIGZyb20gQ2hpbmE6IEZSLiBDb2xvcjogWWVsbG93LCBNYXRlcmlhbHM6IDEwMCUgUG9seWVzdGVyLCBTaXplOiBPbmUgU2l6ZTogU2hvdWxkZXIgV2lkdGg6IDUzY20sIEJ1c3Q6IDExNmNtLCBUb3RhbCBMZW5ndGg6IDU0Y20sIFNsZWV2ZSBMZW5ndGg6IDUwY20sIENhcmU6IEhhbmQgV2FzaCIsIjQ1MCIsImh0dHA6XC9cL3d3dy55ZXNzdHlsZS5jb21cL3AxMTAxOTEwMjMxIiwiIiwiIiwiIiwiMjAyMi0wOC0wNiAyMzowNzowOC4wNzciLCJpbnN0b2NrIiwiRlIiLCIxMTAxOTEwMjMxIiwiV29tZW4iLCJUb3AiLCJCcmFuZCBmcm9tIENoaW5hOiBGUi4gQ29sb3I6IFllbGxvdywgTWF0ZXJpYWxzOiAxMDAlIFBvbHllc3RlciwgU2l6ZTogT25lIFNpemU6IFNob3VsZGVyIFdpZHRoOiA1M2NtLCBCdXN0OiAxMTZjbSwgVG90YWwgTGVuZ3RoOiA1NGNtLCBTbGVldmUgTGVuZ3RoOiA1MGNtLCBDYXJlOiBIYW5kIFdhc2giLCIiLCIiLCIxMTAxOTEwMjMxIiwiIiwiIiwiIiwiIiwiIiwiIiwiIiwiaHR0cHM6XC9cL3d3dy5zaGFyZWFzYWxlLmNvbVwvbS1wci5jZm0/bWVyY2hhbnRJRD0xMDY2OSZ1c2VySUQ9WU9VUlVTRVJJRCZhdGM9MSZwcm9kdWN0SUQ9MTIxMjM0MTc4NSIsIiIsIiIsIiIsIiIsIiIsIiIsImh0dHBzOlwvXC93d3cuc2hhcmVhc2FsZS5jb21cL20tcHIuY2ZtP21lcmNoYW50SUQ9MTA2NjkmdXNlcklEPVlPVVJVU0VSSUQmbW9iaWxlPTEmcHJvZHVjdElEPTEyMTIzNDE3ODUiLCIiLCIiLCIiLCIiLCIiLCIiLCIiLCIiLCIiXQ==';

        // when
        $expected_value = [
            'name' => 'Pointelle Sweater',
            'slug' => 'pointelle-sweater-1212341785',
            'description' => 'Brand from China: FR. Color: Yellow, Materials: 100% Polyester, Size: One Size: Shoulder Width: 53cm, Bust: 116cm, Total Length: 54cm, Sleeve Length: 50cm, Care: Hand Wash',
            'brand_original' => 'FR',
            'merchant_original' => 'YesStyle.com',
            'currency_original' => 'EUR',
            'category_original' => 'fashion|womens|women|top',
            'color_original' => 'Yellow',
            'price' => 20.4,
            'old_price' => 0.0,
            'reduction' => 0,
            'url' => 'https://www.shareasale.com/m-pr.cfm?merchantID=10669&userID=YOURUSERID&productID=1212341785',
            'image_url' => 'http://aff.ysi.bz/assets/64/310/l_p0150131064.jpg',
            'gender' => 'femme',
            'col' => '',
            'coupe' => '',
            'manches' => '',
            'material' => '100% Polyester',
            'model' => '1101910231',
            'motifs' => '',
            'event' => '',
            'style' => NULL,
            'size' => 'Shoulder Width: 53cm, Bust: 116cm, Total Length: 54cm, Sleeve Length: 50cm',
            'livraison' => '',
        ];

        // then
        $this->assertEquals(
            $expected_value,
            $this->parse_payload($payload, $headers)
        );
    }

    public function test__parse_row__from_MustHaveSkirts_additional_attributes()
    {
        // given
        $headers = self::$headers;
        $payload = 'WyIxMjE4OTU5Mjg3IiwiVHJhbnNwYXJlbnQgRWxlZ2FudCBMYWNlIE1lc2ggU2tpcnQiLCIxMDk5MTEiLCJNdXN0SGF2ZVNraXJ0cy5jb20iLCJodHRwczpcL1wvd3d3LnNoYXJlYXNhbGUuY29tXC9tLXByLmNmbT9tZXJjaGFudElEPTEwOTkxMSZ1c2VySUQ9WU9VUlVTRVJJRCZwcm9kdWN0SUQ9MTIxODk1OTI4NyIsImh0dHBzOlwvXC9jZG4uc2hvcGlmeS5jb21cL3NcL2ZpbGVzXC8xXC8wNTUyXC8xODM3XC8yNzcyXC9wcm9kdWN0c1wvcHJvZHVjdC1pbWFnZS0xNzA3MTI3MzU2X21lZGl1bS5qcGc/dj0xNjE3MDYxNDA1IiwiaHR0cHM6XC9cL2Nkbi5zaG9waWZ5LmNvbVwvc1wvZmlsZXNcLzFcLzA1NTJcLzE4MzdcLzI3NzJcL3Byb2R1Y3RzXC9wcm9kdWN0LWltYWdlLTE3MDcxMjczNTYuanBnP3Y9MTYxNzA2MTQwNSIsIjMyLjk5IiwiMzIuOTkiLCJGYXNoaW9uIiwiV29tZW5zIiwiU3BlY2lmaWNhdGlvbnM6ICAgTWF0ZXJpYWw6IE1lc2gsTGFjZSAgU2lsaG91ZXR0ZTogQS1MaW5lICBEZWNvcmF0aW9uOiBIb2xsb3cgT3V0ICBXYWlzdGxpbmU6IEVtcGlyZSAgUGF0dGVybiBUeXBlOiBzdHJpcGVkICBTdHlsZTogQ2FzdWFsICBEcmVzc2VzIExlbmd0aDogTWlkLUNhbGYiLCIiLCIiLCIiLCIiLCIiLCIyMDIxLTA3LTA2IDExOjMwOjEyLjQ5MyIsImluc3RvY2siLCJtdXN0aGF2ZXNraXJ0cyIsIiIsIiIsIiIsIlRyYW5zcGFyZW50IEVsZWdhbnQgTGFjZSBNZXNoIFNraXJ0IiwiIiwiIiwiNDQwNDE1OTAtYmxhY2stb25lLXNpemVfMzk1MTA2ODA0MDQxMzIiLCIiLCIiLCIiLCIiLCIiLCIiLCIwIiwiaHR0cHM6XC9cL3d3dy5zaGFyZWFzYWxlLmNvbVwvbS1wci5jZm0/bWVyY2hhbnRJRD0xMDk5MTEmdXNlcklEPVlPVVJVU0VSSUQmYXRjPTEmcHJvZHVjdElEPTEyMTg5NTkyODciLCIiLCIiLCIiLCIiLCIiLCIiLCJodHRwczpcL1wvd3d3LnNoYXJlYXNhbGUuY29tXC9tLXByLmNmbT9tZXJjaGFudElEPTEwOTkxMSZ1c2VySUQ9WU9VUlVTRVJJRCZtb2JpbGU9MSZwcm9kdWN0SUQ9MTIxODk1OTI4NyIsIiIsIiIsIiIsIiIsIiIsIiIsIiIsIiIsIiJd';

        // when
        $expected_value = [
            'name' => 'Transparent Elegant Lace Mesh Skirt',
            'slug' => 'transparent-elegant-lace-mesh-skirt-1218959287',
            'description' => 'Specifications:   Material: Mesh,Lace  Silhouette: A-Line  Decoration: Hollow Out  Waistline: Empire  Pattern Type: striped  Style: Casual  Dresses Length: Mid-Calf',
            'brand_original' => 'musthaveskirts',
            'merchant_original' => 'MustHaveSkirts.com',
            'currency_original' => 'EUR',
            'category_original' => 'fashion|womens',
            'color_original' => '',
            'price' => 32.99,
            'old_price' => 0.0,
            'reduction' => 0,
            'url' => 'https://www.shareasale.com/m-pr.cfm?merchantID=109911&userID=YOURUSERID&productID=1218959287',
            'image_url' => 'https://cdn.shopify.com/s/files/1/0552/1837/2772/products/product-image-1707127356.jpg?v=1617061405',
            'gender' => 'femme',
            'col' => '',
            'coupe' => '',
            'manches' => '',
            'material' => 'Mesh,Lace',
            'model' => '44041590-black-one-size_39510680404132',
            'motifs' => '',
            'event' => '',
            'style' => 'Casual',
            'size' => '',
            'livraison' => '',
        ];

        // then
        $this->assertEquals(
            $expected_value,
            $this->parse_payload($payload, $headers)
        );
    }

    public function test__parse_row__from_Mensclo_additional_attributes()
    {
        // given
        $headers = self::$headers;
        $payload = 'WyIxMjU3MjA5ODk0IiwiTWVucyBTb2xpZCBDb2xvciBCdXR0b24gVXAgRGFpbHkgU2hvcnQgU2xlZXZlIFNoaXJ0cyIsIjEyMDA1MiIsIk1lbnNjbG8iLCJodHRwczpcL1wvd3d3LnNoYXJlYXNhbGUuY29tXC9tLXByLmNmbT9tZXJjaGFudElEPTEyMDA1MiZ1c2VySUQ9WU9VUlVTRVJJRCZwcm9kdWN0SUQ9MTI1NzIwOTg5NCIsImh0dHBzOlwvXC9jZG4uc2hvcGlmeS5jb21cL3NcL2ZpbGVzXC8xXC8wNTg3XC84MzY5XC81MDI3XC9wcm9kdWN0c1wvM2QyMDc3OWEtNTk4My00ZjRiLWJiNGMtYWZhNWI1MGIwNjFkLmpwZz92PTE2NTc3MTA2MjUiLCJodHRwczpcL1wvY2RuLnNob3BpZnkuY29tXC9zXC9maWxlc1wvMVwvMDU4N1wvODM2OVwvNTAyN1wvcHJvZHVjdHNcLzNkMjA3NzlhLTU5ODMtNGY0Yi1iYjRjLWFmYTViNTBiMDYxZC5qcGc/dj0xNjU3NzEwNjI1IiwiMTYuOTkiLCIiLCJBcnRcL01lZGlhXC9QZXJmb3JtYW5jZSIsIkFydCIsIk9jY2FzaW9uOiBEYWlseUNvbG9yOiBZZWxsb3dQYXR0ZXJuOiBTb2xpZE1hdGVyaWFsOiBQb2x5ZXN0ZXIsQ290dG9uRGVzaWduIEVsZW1lbnQ6IEJ1dHRvbkNvbGxhcjogTGFwZWwgQ29sbGFyU2l6ZTogUyxNLEwsWEwsMlhMU2xlZXZlcyBMZW5ndGg6IFNob3J0IFNsZWV2ZUZpdCBUeXBlOiBSZWd1bGFyU2Vhc29uOiBTdW1tZXJCcmFuZDogTWVuc2Nsb1RoaWNrbmVzczogTW9kZXJhdGVTaXplIENoYXJ0KENNKSAgICAgICAgIFRhZyBTaXplIFVTIFNpemUgQnVzdCBTbGVldmUgTGVuZ3RoIFNob3VsZGVyIExlbmd0aCAgIFMgUyAxMDYgMjIgNDQuNSA3MiAgIE0gTSAxMTEgMjIuNSA0NiA3NCAgIEwgTCAxMTYgMjMgNDcuOCA3NiAgIFhMIFhMIDEyMSAyMy41IDQ5LjYgNzggICAyWEwgMlhMIDEyNiAyNCA1MS40IDgwICAgIiwiIiwiIiwiIiwiIiwiIiwiMjAyMi0wNy0xNCAyMzoxMDoyOS41MjMiLCIiLCIiLCIiLCJNZW4+VG9wcz5TaGlydHMiLCIiLCJPY2Nhc2lvbjogRGFpbHlDb2xvcjogWWVsbG93UGF0dGVybjogU29saWRNYXRlcmlhbDogUG9seWVzdGVyLENvdHRvbkRlc2lnbiBFbGVtZW50OiBCdXR0b25Db2xsYXI6IExhcGVsIENvbGxhclNpemU6IFMsTSxMLFhMLDJYTFNsZWV2ZXMgTGVuZ3RoOiBTaG9ydCBTbGVldmVGaXQgVHlwZTogUmVndWxhclNlYXNvbjogU3VtbWVyQnJhbmQ6IE1lbnNjbG9UaGlja25lc3M6IE1vZGVyYXRlU2l6ZSBDaGFydChDTSkgICAgICAgICBUYWcgU2l6ZSBVUyAiLCIiLCIiLCJQT0E5Nzk2OTc5IiwiIiwiIiwiIiwiIiwiIiwiIiwiMCIsImh0dHBzOlwvXC93d3cuc2hhcmVhc2FsZS5jb21cL20tcHIuY2ZtP21lcmNoYW50SUQ9MTIwMDUyJnVzZXJJRD1ZT1VSVVNFUklEJmF0Yz0xJnByb2R1Y3RJRD0xMjU3MjA5ODk0IiwiIiwiIiwiIiwiIiwiIiwiIiwiaHR0cHM6XC9cL3d3dy5zaGFyZWFzYWxlLmNvbVwvbS1wci5jZm0/bWVyY2hhbnRJRD0xMjAwNTImdXNlcklEPVlPVVJVU0VSSUQmbW9iaWxlPTEmcHJvZHVjdElEPTEyNTcyMDk4OTQiLCIiLCIiLCIiLCIiLCIiLCIiLCIiLCIiLCIiXQ==';

        // when
        $expected_value = [
            'name' => 'Mens Solid Color Button Up Daily Short Sleeve Shirts',
            'slug' => 'mens-solid-color-button-up-daily-short-sleeve-shirts-1257209894',
            'description' => 'Occasion: DailyColor: YellowPattern: SolidMaterial: Polyester,CottonDesign Element: ButtonCollar: Lapel CollarSize: S,M,L,XL,2XLSleeves Length: Short SleeveFit Type: RegularSeason: SummerBrand: MenscloThickness: ModerateSize Chart(CM)         Tag Size US Size Bust Sleeve Length Shoulder Length   S S 106 22 44.5 72   M M 111 22.5 46 74   L L 116 23 47.8 76   XL XL 121 23.5 49.6 78   2XL 2XL 126 24 51.4 80',
            'brand_original' => 'Mensclo',
            'merchant_original' => 'Mensclo',
            'currency_original' => 'EUR',
            'category_original' => 'art/media/performance|art|men>tops>shirts',
            'color_original' => 'Yellow',
            'price' => 16.99,
            'old_price' => 0.0,
            'reduction' => 0,
            'url' => 'https://www.shareasale.com/m-pr.cfm?merchantID=120052&userID=YOURUSERID&productID=1257209894',
            'image_url' => 'https://cdn.shopify.com/s/files/1/0587/8369/5027/products/3d20779a-5983-4f4b-bb4c-afa5b50b061d.jpg?v=1657710625',
            'gender' => 'homme',
            'col' => 'Lapel Collar',
            'coupe' => 'Regular',
            'manches' => 'Short Sleeve',
            'material' => 'Polyester,Cotton',
            'model' => 'POA9796979',
            'motifs' => 'Solid',
            'event' => 'Daily',
            'style' => NULL,
            'size' => 'S,M,L,XL,2XL',
            'livraison' => '',
        ];


        // then
        $this->assertEquals(
            $expected_value,
            $this->parse_payload($payload, $headers)
        );
    }
}
