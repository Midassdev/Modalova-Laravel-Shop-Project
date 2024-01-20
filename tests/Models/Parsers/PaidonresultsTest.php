<?php

namespace Tests\Models\Parsers;

use App\Models\Parsers\Paidonresults;

class PaidonresultsTest extends BaseParser
{
    public static $klass = Paidonresults::class;

    public function test__parse_row__from_TCA()
    {
        // given
        $headers = 'ProductName,ProductPrice,ProductDescription,SummaryDescription,BrandName,EAN,ProductID,AffiliateURL,ImageURL,ProductAddedDate,ProductUpdatedDate,Category,MerchantName,ImageURL50by50,ImageURL100by100,ImageURL120by120,ImageURL200by200,ImageURL234by234,ImageURL300by300,ImageURL400by400,OriginalImage,DynamicProductPrice,HasProductImage,DirectLinkNoTracking';
        $payload = 'WyJUQ0EgQWVyb24gU2hvcnQgMi4wIC0gQmx1ZSBJcmlzIiwiMjAuMDAiLCJDb29sIGFuZCBjb21mb3J0YWJsZS4gQSBtdWx0aS1mdW5jdGlvbmFsIHNob3J0IHRoYXQncyBsaWdodCwgc29mdCBhbmQgYnJlYXRoYWJsZSB3aXRoIG11bHRpcGxlIHN0b3JhZ2Ugb3B0aW9ucy4gLS0tIC0gQnVpbHQgZnJvbSBicmVhdGhhYmxlIEFlcm9uIGdyaWQgZmFicmljXFxuLSAyeCBzaWRlIHBvY2tldHMgcHJvdmlkZSBzaW1wbGUgc2VjdXJlIHN0b3JhZ2UgZm9yIHZhbHVhYmxlcywgZmluaXNoZWQgd2l0aCBUQ0EgYnJhbmRlZCBlbGFzdGljIHRyaW1taW5nLlxcbi0gRHJhd3N0cmluZ3MgZm9yIGN1c3RvbSBmaXRcXG4tIFRDQSBELlIuWS4gdGVjaG5vbG9neSBrZWVwcyB5b3UgZHJ5LCBsaWdodCBhbmQgY29tZm9ydGFibGVcXG4tIFJlZmxlY3RpdmUgVENBIGxvZ28gVENBIFF1YWxpdHkgR3VhcmFudGVlICsgMzY1LURheSBSZXR1cm5zIiwiQ29vbCBhbmQgY29tZm9ydGFibGUuIEEgbXVsdGktZnVuY3Rpb25hbCBzaG9ydCB0aGF0J3MgbGlnaHQsIHNvZnQgYW5kIGJyZWF0aGFibGUgd2l0aCBtdWx0aXBsZSBzdG9yYWdlIG9wdGlvbnMuIC0tLSAtIEJ1aWx0IGZyb20gYnJlYXRoYWJsZSBBZXJvbiBncmlkIGZhYnJpYyAtIDJ4IHNpZGUgcG9ja2V0cyBwcm92aWRlIHNpbXBsZSBzZWN1ci4uLiIsIlRDQSIsIjUwNTU5MDY1NjQ1NjQiLCI0NDIyMzY2IiwiaHR0cHM6XC9cL3d3dy5wYWlkb25yZXN1bHRzLm5ldFwvY1wvNTkzNjlcL0ZNMjAxM1wvMjAxM1wvMFwvcHJvZHVjdHNcL2Flcm9uLXNob3J0LWNvbG91ci1ibHVlLWlyaXMiLCJodHRwOlwvXC91ay5wcm9kdWN0LWltYWdlcy5uZXRcLzZcLzQ0MjIzNjYtMTAweDEwMC5qcGciLCIyMy0wOC0yMDIxIDIyOjA4IiwiMzAtMDYtMjAyMyAwMDo0NSIsIlNob3J0cyIsIlRDQSIsImh0dHA6XC9cL3VrLnByb2R1Y3QtaW1hZ2VzLm5ldFwvNlwvNDQyMjM2Ni01MHg1MC5qcGciLCJodHRwOlwvXC91ay5wcm9kdWN0LWltYWdlcy5uZXRcLzZcLzQ0MjIzNjYtMTAweDEwMC5qcGciLCJodHRwOlwvXC91ay5wcm9kdWN0LWltYWdlcy5uZXRcLzZcLzQ0MjIzNjYtMTIweDEyMC5qcGciLCJodHRwOlwvXC91ay5wcm9kdWN0LWltYWdlcy5uZXRcLzZcLzQ0MjIzNjYtMjAweDIwMC5qcGciLCJodHRwOlwvXC91ay5wcm9kdWN0LWltYWdlcy5uZXRcLzZcLzQ0MjIzNjYtMjM0eDIzNC5qcGciLCJodHRwOlwvXC91ay5wcm9kdWN0LWltYWdlcy5uZXRcLzZcLzQ0MjIzNjYtMzAweDMwMC5qcGciLCJodHRwOlwvXC91ay5wcm9kdWN0LWltYWdlcy5uZXRcLzZcLzQ0MjIzNjYtNDAweDQwMC5qcGciLCJodHRwOlwvXC91ay5wcm9kdWN0LWltYWdlcy5uZXRcL29yaWdpbmFsXC8yM1wvNjZcLzQ0MjIzNjYuanBnIiwiPHNjcmlwdCBzcmM9XCJodHRwOlwvXC9keW5hbWljcHJvZHVjdHByaWNlLmNvbVwvanM/VXNlcklEPTU5MzY5JlByb2R1Y3RJRD00NDIyMzY2XCI+PFwvc2NyaXB0PiIsIlllcyIsImh0dHBzOlwvXC90Y2EuZml0XC9wcm9kdWN0c1wvYWVyb24tc2hvcnQtY29sb3VyLWJsdWUtaXJpcyJd';

        // when
        $expected_value = [
            'name'              => 'TCA Aeron Short 2.0 - Blue Iris',
            'slug'              => 'tca-aeron-short-2-0-blue-iris',
            'description'       => 'Cool and comfortable. A multi-functional short that\'s light, soft and breathable with multiple storage options. --- - Built from breathable Aeron grid fabric\\n- 2x side pockets provide simple secure storage for valuables, finished with TCA branded elastic trimming.\\n- Drawstrings for custom fit\\n- TCA D.R.Y. technology keeps you dry, light and comfortable\\n- Reflective TCA logo TCA Quality Guarantee + 365-Day Returns',
            'brand_original'    => 'TCA',
            'merchant_original' => 'TCA',
            'currency_original' => 'EUR',
            'category_original' => 'shorts',
            'color_original'    => '',
            'price'             => 20.0,
            'old_price'         => 0.0,
            'reduction'         => 0,
            'url'               => 'https://www.paidonresults.net/c/59369/FM2013/2013/0/products/aeron-short-colour-blue-iris',
            'image_url'         => 'http://uk.product-images.net/original/23/66/4422366.jpg',
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

    public function test__parse_row__from_TCA_gender()
    {
        // given
        $headers = 'ProductName,ProductPrice,ProductDescription,SummaryDescription,BrandName,EAN,ProductID,AffiliateURL,ImageURL,ProductAddedDate,ProductUpdatedDate,Category,MerchantName,ImageURL50by50,ImageURL100by100,ImageURL120by120,ImageURL200by200,ImageURL234by234,ImageURL300by300,ImageURL400by400,OriginalImage,DynamicProductPrice,HasProductImage,DirectLinkNoTracking';
        $payload = 'WyJUQ0EgTWVuJ3MgVXRpbGl0eSAyLWluLTEgUnVubmluZyBTaG9ydCAtIEJsYWNrIiwiMjIuMDAiLCJTdHJlbmd0aCBhbmQgcG93ZXIuIFZlcnNhdGlsZSBtaWQtd2VpZ2h0IHNob3J0IHdpdGggbXVsdGktZnVuY3Rpb25hbCBwZXJmb3JtYW5jZSBmZWF0dXJlcy4gSW50ZWdyYXRlZCBpbm5lciBjb21wcmVzc2lvbiBzaG9ydCBoZWxwcyBzdXBwb3J0IG11c2NsZSBwZXJmb3JtYW5jZSwgZmVhdHVyaW5nIGEgY29tcGFjdCBhbmQgc2VjdXJlIHBob25lIHBvY2tldC4gSWRlYWwgZm9yIHlvdXIgZ3ltIG9yIHJ1biByb3V0aW5lIiwiU3RyZW5ndGggYW5kIHBvd2VyLiBWZXJzYXRpbGUgbWlkLXdlaWdodCBzaG9ydCB3aXRoIG11bHRpLWZ1bmN0aW9uYWwgcGVyZm9ybWFuY2UgZmVhdHVyZXMuIEludGVncmF0ZWQgaW5uZXIgY29tcHJlc3Npb24gc2hvcnQgaGVscHMgc3VwcG9ydCBtdXNjbGUgcGVyZm9ybWFuY2UsIGZlYXR1cmluZyBhIGNvbXBhY3QgYW5kIHNlY3VyZSBwaG9uZS4uLiIsIlRDQSIsIjUwNTU5MDY1NDU2ODYiLCI0MDY3MjkzIiwiaHR0cHM6XC9cL3d3dy5wYWlkb25yZXN1bHRzLm5ldFwvY1wvNTkzNjlcL0ZNMjAxM1wvMjAxM1wvMFwvcHJvZHVjdHNcL3V0aWxpdHktMi1pbi0xLXNob3J0LWNvbG91ci1hbnRocmFjaXRlIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8zXC80MDY3MjkzLTEwMHgxMDAuanBnIiwiMjgtMTEtMjAxOSAyMjozMyIsIjI2LTA3LTIwMjMgMDA6NDUiLCJTaG9ydHMiLCJUQ0EiLCJodHRwOlwvXC91ay5wcm9kdWN0LWltYWdlcy5uZXRcLzNcLzQwNjcyOTMtNTB4NTAuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8zXC80MDY3MjkzLTEwMHgxMDAuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8zXC80MDY3MjkzLTEyMHgxMjAuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8zXC80MDY3MjkzLTIwMHgyMDAuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8zXC80MDY3MjkzLTIzNHgyMzQuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8zXC80MDY3MjkzLTMwMHgzMDAuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8zXC80MDY3MjkzLTQwMHg0MDAuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC9vcmlnaW5hbFwvNzJcLzkzXC80MDY3MjkzLmpwZyIsIjxzY3JpcHQgc3JjPVwiaHR0cDpcL1wvZHluYW1pY3Byb2R1Y3RwcmljZS5jb21cL2pzP1VzZXJJRD01OTM2OSZQcm9kdWN0SUQ9NDA2NzI5M1wiPjxcL3NjcmlwdD4iLCJZZXMiLCJodHRwczpcL1wvdGNhLmZpdFwvcHJvZHVjdHNcL3V0aWxpdHktMi1pbi0xLXNob3J0LWNvbG91ci1hbnRocmFjaXRlIl0=';

        // when
        $expected_value = [
            'name'              => 'Men\'s Utility 2-in-1 Running Short - Black',
            'slug'              => 'tca-men-s-utility-2-in-1-running-short-black',
            'description'       => 'Strength and power. Versatile mid-weight short with multi-functional performance features. Integrated inner compression short helps support muscle performance, featuring a compact and secure phone pocket. Ideal for your gym or run routine',
            'brand_original'    => 'TCA',
            'merchant_original' => 'TCA',
            'currency_original' => 'EUR',
            'category_original' => 'shorts',
            'color_original'    => '',
            'price'             => 22.0,
            'old_price'         => 0.0,
            'reduction'         => 0,
            'url'               => 'https://www.paidonresults.net/c/59369/FM2013/2013/0/products/utility-2-in-1-short-colour-anthracite',
            'image_url'         => 'http://uk.product-images.net/original/72/93/4067293.jpg',
            'gender'            => 'homme',
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

    public function test__parse_row__from_WH()
    {
        // given
        $headers = 'ProductName,ProductPrice,ProductDescription,SummaryDescription,ProductID,AffiliateURL,ImageURL,ProductAddedDate,ProductUpdatedDate,Category,MerchantName,ImageURL50by50,ImageURL100by100,ImageURL120by120,ImageURL200by200,ImageURL234by234,ImageURL300by300,ImageURL400by400,OriginalImage,DynamicProductPrice,HasProductImage,DirectLinkNoTracking';
        $payload = 'WyJCbGFjayBNYXJjZWxsYSBFdmVuaW5nIEJlc3Bva2UgU2hpcnQgLSAxKyIsIjE2MC4wMCIsIk91ciBNYXJjZWxsYSBldmVuaW5nIHNoaXJ0IGlzIGRlc2lnbmVkIHRvIGNvbXBsaW1lbnQgeW91ciBldmVuaW5nd2VhciBmb3IgYmxhY2sgdGllIGFuZCBvdGhlciBmb3JtYWwgZXZlbnRzLiBDb25zdHJ1Y3RlZCBpbiBhIGNvb2wgY290dG9uIHdpdGggYSBNYXJjZWxsYSB3ZWF2ZSBiaWIgb24gdGhlIGZyb250IGFzIGlzIHRyYWRpdGlvbmFsIGluIHRoaXMgYXR0aXJlLlxcblxcblRoZSBzaGlydCBhbHNvIGNvbWVzIHdpdGggcmVtb3ZhYmxlIGJ1dHRvbnMsIHdoaWNoIGNhbiBiZSByZXBsYWNlZCB3aXRoIHlvdXIgZmF2b3JpdGUgc2V0IG9mIGRyZXNzIHN0dWRzIHRvIGNyZWF0ZSBhIHRydWx5IHVuaXF1ZSBsb29rLlxcblxcbkNob29zZSB5b3VyIHBlcnNvbmFsIHNwZWNpZmljYXRpb25zIGZyb20gdGhlIG9wdGlvbnMgbWVudXMgYWJvdmUgdG8gY3VzdG9taXNlIHRoZSBzaGlydCB0byB5b3VyIGV4YWN0IHByZWZlcmVuY2UuXFxuIiwiT3VyIE1hcmNlbGxhIGV2ZW5pbmcgc2hpcnQgaXMgZGVzaWduZWQgdG8gY29tcGxpbWVudCB5b3VyIGV2ZW5pbmd3ZWFyIGZvciBibGFjayB0aWUgYW5kIG90aGVyIGZvcm1hbCBldmVudHMuIENvbnN0cnVjdGVkIGluIGEgY29vbCBjb3R0b24gd2l0aCBhIE1hcmNlbGxhIHdlYXZlIGJpYiBvbiB0aGUgZnJvbnQgYXMgaXMgdHJhZGl0aW9uYWwgaS4uLiIsIjQ4NjU1NzkiLCJodHRwczpcL1wvd3d3LnBhaWRvbnJlc3VsdHMubmV0XC9jXC81OTM2OVwvRk0xNzQxXC8xNzQxXC8wXC9wcm9kdWN0c1wvYmVzcG9rZS1ibGFjay1tYXJjZWxsYS1ldmVuaW5nLXNoaXJ0IiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8xXC8zNDgyNjUxLTEwMHgxMDAuanBnIiwiMDUtMTItMjAyMiAyMjozNCIsIjIyLTAzLTIwMjMgMTc6NDUiLCJDdXRhd2F5IENvbGxhciBTaGlydHMiLCJXSCBUYXlsb3IgU2hpcnRtYWtlcnMiLCJodHRwOlwvXC91ay5wcm9kdWN0LWltYWdlcy5uZXRcLzFcLzM0ODI2NTEtNTB4NTAuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8xXC8zNDgyNjUxLTEwMHgxMDAuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8xXC8zNDgyNjUxLTEyMHgxMjAuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8xXC8zNDgyNjUxLTIwMHgyMDAuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8xXC8zNDgyNjUxLTIzNHgyMzQuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8xXC8zNDgyNjUxLTMwMHgzMDAuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC8xXC8zNDgyNjUxLTQwMHg0MDAuanBnIiwiaHR0cDpcL1wvdWsucHJvZHVjdC1pbWFnZXMubmV0XC9vcmlnaW5hbFwvMjZcLzUxXC8zNDgyNjUxLmpwZyIsIjxzY3JpcHQgc3JjPVwiaHR0cDpcL1wvZHluYW1pY3Byb2R1Y3RwcmljZS5jb21cL2pzP1VzZXJJRD01OTM2OSZQcm9kdWN0SUQ9NDg2NTU3OVwiPjxcL3NjcmlwdD4iLCJZZXMiLCJodHRwczpcL1wvd3d3LndodHNoaXJ0bWFrZXJzLmNvbVwvcHJvZHVjdHNcL2Jlc3Bva2UtYmxhY2stbWFyY2VsbGEtZXZlbmluZy1zaGlydCJd';

        // when
        $expected_value = [
            'name'              => 'Black Marcella Evening Bespoke Shirt - 1+',
            'slug'              => 'black-marcella-evening-bespoke-shirt-1',
            'description'       => 'Our Marcella evening shirt is designed to compliment your eveningwear for black tie and other formal events. Constructed in a cool cotton with a Marcella weave bib on the front as is traditional in this attire.\\n\\nThe shirt also comes with removable buttons, which can be replaced with your favorite set of dress studs to create a truly unique look.\\n\\nChoose your personal specifications from the options menus above to customise the shirt to your exact preference.\\n',
            'brand_original'    => 'WH Taylor Shirtmakers',
            'merchant_original' => 'WH Taylor Shirtmakers',
            'currency_original' => 'EUR',
            'category_original' => 'cutaway collar shirts',
            'color_original'    => '',
            'price'             => 160.0,
            'old_price'         => 0.0,
            'reduction'         => 0,
            'url'               => 'https://www.paidonresults.net/c/59369/FM1741/1741/0/products/bespoke-black-marcella-evening-shirt',
            'image_url'         => 'http://uk.product-images.net/original/26/51/3482651.jpg',
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
}
