<?php

namespace Tests\Models\Parsers;

use App\Models\Parsers\Kelkoo;

class KelkooTest extends BaseParser
{
    public static $klass = Kelkoo::class;

    public static $headers = 'offerId,offerType,title,lastUpdateDate,description,country,price,priceWithoutRebate,monthPrice,rebatePercentage,rebateEndDate,deliveryCost,priceDiscountText,totalPrice,unitPrice,currency,availabilityStatus,timeToDeliver,condition,warranty,greenLabel,flag,code,images,features,offerUrl,goUrl,estimatedCpc,estimatedMobileCpc,brand,merchant,merchantProvidedCategory,category,googleProductCategory,ecotax,madeIn,efficiencyClass,performanceScore,product';


    public function test__parse_row__from_Timberland_material_color_size()
    {
        // given
        $payload = 'eyJvZmZlcklkIjoiMDFjMWFjY2U4OWMyZWQzM2I1OWViYzkzZjg5ZjRiNmYiLCJvZmZlclR5cGUiOm51bGwsInRpdGxlIjoiVGltYmVybGFuZCBCb3R0aW5lIENodWtrYSBCcmFkc3RyZWV0IEVuIEN1aXIgUG91ciBIb21tZSBFbiBNYXJyb24gTWFycm9uLCBUYWlsbGUgNDUiLCJsYXN0VXBkYXRlRGF0ZSI6IjIwMjMtMTEtMTBUMDk6MjU6MTVaIiwiZGVzY3JpcHRpb24iOiJBdGhsdGlxdWUgZXQgbGdhbnRlLCBjZXR0ZSBib3R0aW5lIGNodWtrYSBkJ2luc3BpcmF0aW9uIHNwb3J0aXZlIGVzdCBkb3RlIGQndW5lIGFzc2lzZSBwbGFudGFpcmUgT3J0aG9MaXRlIGxncmUsIGRlIGxhIHRlY2hub2xvZ2llIFNlbnNvckZsZXggcG91ciBwbHVzIGRlIGNvbmZvcnQgZXQgZGUgZmxleGliaWxpdCBldCBkJ3VuZSBkb3VibHVyZSBSZUJPVEwuIFRpbWJlcmxhbmQgc291dGllbnQgbGEgZmFicmljYXRpb24gcmVzcG9uc2FibGUgZHUgY3VpciBwYXIgbCdpbnRlcm1kaWFpcmUgZHUgTGVhdGhlciBXb3JraW5nIEdyb3VwLiBUYWlsbGUgNDUiLCJjb3VudHJ5IjoiZnIiLCJwcmljZSI6MTUwLCJwcmljZVdpdGhvdXRSZWJhdGUiOjE1MCwibW9udGhQcmljZSI6bnVsbCwicmViYXRlUGVyY2VudGFnZSI6MCwicmViYXRlRW5kRGF0ZSI6bnVsbCwiZGVsaXZlcnlDb3N0IjowLCJwcmljZURpc2NvdW50VGV4dCI6bnVsbCwidG90YWxQcmljZSI6MTUwLCJ1bml0UHJpY2UiOm51bGwsImN1cnJlbmN5IjoiRVVSIiwiYXZhaWxhYmlsaXR5U3RhdHVzIjoiaW5fc3RvY2siLCJ0aW1lVG9EZWxpdmVyIjoic291cyAzIFx1MDBlMCA1IGpvdXJzIG91dnJhYmxlcyIsImNvbmRpdGlvbiI6Im5ldyIsIndhcnJhbnR5IjpudWxsLCJncmVlbkxhYmVsIjoiTFdHIC0gTGVhdGhlciBXb3JraW5nIEdyb3VwIiwiZmxhZyI6eyJvZmZlbnNpdmVDb250ZW50IjpmYWxzZSwiZ3JlZW5Qcm9kdWN0Ijp0cnVlLCJzYWxlRXZlbnQiOmZhbHNlfSwiY29kZSI6eyJlYW4iOiIwMTk1NDQwNDQ4NzYyIiwic2t1IjpudWxsLCJtcG4iOm51bGwsImd0aW4iOiIwMDE5NTQ0MDQ0ODc2MiJ9LCJpbWFnZXMiOlt7InVybCI6Imh0dHBzOlwvXC9yLmtlbGtvby5jb21cL3Jlc2l6ZS5waHA/Y291bnRyeT1mciZtZXJjaGFudElkPTEwMDUxMzI3MyZjYXRlZ29yeUlkPTEwOTMwMSZ0cmFja2luZ0lkPTk2OTgwMTQxJndpZHRoPTMwMCZoZWlnaHQ9MzAwJmltYWdlPWh0dHBzJTNBJTJGJTJGaW1hZ2VzLnRpbWJlcmxhbmQuY29tJTJGaXMlMkZpbWFnZSUyRlRpbWJlcmxhbmRFVSUyRkEyQkJLQTIwLWhlcm8lM0YlMjQ5MjB4OTIwJTI0JnNpZ249eF9WWU1fSDgxRmxqT3EwNHpHZ0U2a0hPSzFuWE1OZElKdVpHQ0dHV2FaQS0iLCJ6b29tVXJsIjoiaHR0cHM6XC9cL3Iua2Vsa29vLmNvbVwvcmVzaXplLnBocD9jb3VudHJ5PWZyJm1lcmNoYW50SWQ9MTAwNTEzMjczJmNhdGVnb3J5SWQ9MTA5MzAxJnRyYWNraW5nSWQ9OTY5ODAxNDEmd2lkdGg9YXV0byZoZWlnaHQ9YXV0byZpbWFnZT1odHRwcyUzQSUyRiUyRmltYWdlcy50aW1iZXJsYW5kLmNvbSUyRmlzJTJGaW1hZ2UlMkZUaW1iZXJsYW5kRVUlMkZBMkJCS0EyMC1oZXJvJTNGJTI0OTIweDkyMCUyNCZzaWduPXhfVllNX0g4MUZsak9xMDR6R2dFNmtIT0sxblhNTmRJSnVaR0NHR1dhWkEtIn1dLCJmZWF0dXJlcyI6eyJjb2xvciI6eyJsYWJlbCI6IkNvdWxldXIiLCJ2YWx1ZXMiOlt7ImxhYmVsIjoiTWFycm9uIiwidmFsdWUiOiJtYXJyb24ifV19LCJnZW5kZXIiOnsibGFiZWwiOiJHZW5yZSIsInZhbHVlcyI6W3sibGFiZWwiOiJIb21tZSIsInZhbHVlIjoiaG9tbWUifV19LCJtYXRlcmlhbCI6eyJsYWJlbCI6Ik1hdGlcdTAwZThyZSIsInZhbHVlcyI6W3sibGFiZWwiOiJDdWlyIiwidmFsdWUiOiJjdWlyIn1dfSwic2l6ZSI6eyJsYWJlbCI6IlRhaWxsZSIsInZhbHVlcyI6W3sibGFiZWwiOiI0NSIsInZhbHVlIjoiNDUifV19LCJ0eXBlIjp7ImxhYmVsIjoiVHlwZSIsInZhbHVlcyI6W3sibGFiZWwiOiJCb3R0ZXMiLCJ2YWx1ZSI6ImJvdHRlcyJ9LHsibGFiZWwiOiJCb3R0aW5lcyIsInZhbHVlIjoiYm90dGluZXMifV19fSwib2ZmZXJVcmwiOnsibGFuZGluZ1VybCI6Imh0dHBzOlwvXC93d3cudGltYmVybGFuZC5mclwvc2hvcFwvZnJcL3RibC1mclwvY2h1a2thLW1pLWhhdXRlLWJyYWRzdHJlZXQtdWx0cmEtcG91ci1ob21tZS1lbi1tYXJyb24tZm9uY2UtYTJiYmthMjA/Y21fbW1jPUdQRi1fLWdvb2dsZS1fLW1lcmNoYW50Y2VudGVyLV8tQTJCQktBMjAiLCJtb2JpbGVMYW5kaW5nVXJsIjoiaHR0cHM6XC9cL3d3dy50aW1iZXJsYW5kLmZyXC9zaG9wXC9mclwvdGJsLWZyXC9jaHVra2EtbWktaGF1dGUtYnJhZHN0cmVldC11bHRyYS1wb3VyLWhvbW1lLWVuLW1hcnJvbi1mb25jZS1hMmJia2EyMD9jbV9tbWM9R1BGLV8tZ29vZ2xlLV8tbWVyY2hhbnRjZW50ZXItXy1BMkJCS0EyMCIsInRyYWNrZWRVcmwiOiJodHRwczpcL1wvY2xpY2subGlua3N5bmVyZ3kuY29tXC9saW5rP2lkPUJtVVViNlAycldnJm9mZmVyaWQ9MTM0MDk2MC40Mjc0NTIxODIwMDQ3MDE0ODg4NTAwMTQmdHlwZT0xNSZtdXJsPWh0dHBzJTNBJTJGJTJGd3d3LnRpbWJlcmxhbmQuZnIlMkZzaG9wJTJGZnIlMkZ0YmwtZnIlMkZjaHVra2EtbWktaGF1dGUtYnJhZHN0cmVldC11bHRyYS1wb3VyLWhvbW1lLWVuLW1hcnJvbi1mb25jZS1hMmJia2EyMCUzRmNtX21tYyUzREdQRi1fLWdvb2dsZS1fLW1lcmNoYW50Y2VudGVyLV8tQTJCQktBMjAiLCJtb2JpbGVUcmFja2VkVXJsIjpudWxsfSwiZ29VcmwiOiJodHRwczpcL1wvZnItZ28ua2Vsa29vZ3JvdXAubmV0XC9vZmZlcnNlYXJjaEdvPy50cz0xNjk5ODkwNTE3MzMzJi5zaWc9b0JPeUFMNE41VzB2aHBJNWJtbkdDZmJiRHhFLSZhZmZpbGlhdGlvbklkPTk2OTgwMTQxJmNvbUlkPTEwMDUxMzI3MyZjb3VudHJ5PWZyJm9mZmVySWQ9MDFjMWFjY2U4OWMyZWQzM2I1OWViYzkzZjg5ZjRiNmYmc2VydmljZT0zNyZ0b2tlbklkPTFkOTU2YjM4LWQ0NTItNDI4Ny04ZDBiLTk2N2IyNzljY2U3MiZ3YWl0PXRydWUiLCJlc3RpbWF0ZWRDcGMiOjAuMDAyNzMsImVzdGltYXRlZE1vYmlsZUNwYyI6MC4wMDIxODQsImJyYW5kIjp7ImlkIjozMjMxLCJuYW1lIjoiVGltYmVybGFuZCJ9LCJtZXJjaGFudCI6eyJpZCI6MTAwNTEzMjczLCJuYW1lIjoiVGltYmVybGFuZCIsImxvZ29VcmwiOm51bGx9LCJtZXJjaGFudFByb3ZpZGVkQ2F0ZWdvcnkiOiJIb21tZSA+IENoYXVzc3VyZXN+fkJvdHRlcyBDaHVra2F+fkxvb2sgQ2FzdWFsID4gbWFsZSIsImNhdGVnb3J5Ijp7ImlkIjoxMDkzMDEsIm5hbWUiOiJDaGF1c3N1cmVzIHBvdXIgaG9tbWVzIn0sImdvb2dsZVByb2R1Y3RDYXRlZ29yeSI6eyJpZCI6MTg3LCJuYW1lIjpudWxsfSwiZWNvdGF4IjpudWxsLCJtYWRlSW4iOm51bGwsImVmZmljaWVuY3lDbGFzcyI6bnVsbCwicGVyZm9ybWFuY2VTY29yZSI6MCwicHJvZHVjdCI6eyJpZCI6IjAwMTk1NDQwNDQ4MzU5IiwicG9wdWxhcml0eSI6bnVsbH19';


        // when
        $expected_value = [
            'name' => 'Bottine Chukka Bradstreet En Cuir En , Taille 45',
            'slug' => 'timberland-bottine-chukka-bradstreet-en-cuir-pour-homme-en-marron-marron-taille-45-01c1acce89c2ed33b59ebc93f89f4b6f',
            'description' => 'Athltique et lgante, cette bottine chukka d\'inspiration sportive est dote d\'une assise plantaire OrthoLite lgre, de la technologie SensorFlex pour plus de confort et de flexibilit et d\'une doublure ReBOTL. Timberland soutient la fabrication responsable du cuir par l\'intermdiaire du Leather Working Group. Taille 45',
            'brand_original' => 'Timberland',
            'merchant_original' => 'Timberland',
            'currency_original' => 'EUR',
            'category_original' => 'chaussures pour hommes|homme|chaussures|bottes chukka|look casual|male',
            'color_original' => 'marron',
            'price' => 150.0,
            'old_price' => 0.0,
            'reduction' => 0,
            'url' => 'https://fr-go.kelkoogroup.net/offersearchGo?.ts=1699890517333&.sig=oBOyAL4N5W0vhpI5bmnGCfbbDxE-&affiliationId=96980141&comId=100513273&country=fr&offerId=01c1acce89c2ed33b59ebc93f89f4b6f&service=37&tokenId=1d956b38-d452-4287-8d0b-967b279cce72&wait=true',
            'image_url' => 'https://r.kelkoo.com/resize.php?country=fr&merchantId=100513273&categoryId=109301&trackingId=96980141&width=auto&height=auto&image=https%3A%2F%2Fimages.timberland.com%2Fis%2Fimage%2FTimberlandEU%2FA2BBKA20-hero%3F%24920x920%24&sign=x_VYM_H81FljOq04zGgE6kHOK1nXMNdIJuZGCGGWaZA-',
            'gender' => 'homme',
            'col' => '',
            'coupe' => '',
            'manches' => '',
            'material' => 'cuir',
            'model' => 'Bottes|Bottines',
            'motifs' => NULL,
            'event' => '',
            'style' => NULL,
            'size' => '45',
            'livraison' => 'sous 3 à 5 jours ouvrables',
        ];

        // then
        $this->assertEquals(
            $expected_value,
            $this->parse_payload($payload)
        );
    }

    public function test__parse_row__from_VintageMotors_livraison()
    {
        // given
        $payload = 'eyJvZmZlcklkIjoiMDYwNWFhYjdmOGY0ZmI1NGZjZGE2ZGRhMmIzOGM1YWUiLCJvZmZlclR5cGUiOm51bGwsInRpdGxlIjoiRE1EIENhc3F1ZSBKZXQgVmludGFnZSBHb29kd29vZCAtIERtZCIsImxhc3RVcGRhdGVEYXRlIjoiMjAyMy0xMS0xMFQwOTozMjozNloiLCJkZXNjcmlwdGlvbiI6IkxcdTAwZTlnZXIsIHNcdTAwZmJyLCBjb25mb3J0YWJsZSBldCBcdTAwZTlsXHUwMGU5Z2FudCA6IHF1ZSBkZW1hbmRlciBkZSBwbHVzPyBcdTAwYTAgSW5mb3JtYXRpb25zIHN1ciBsZSBwcm9kdWl0OiBcdTAwYTAgQ2FzcXVlIGpldCByXHUwMGU5YWxpc1x1MDBlOSBlbiBmaWJyZSBkZSB2ZXJyZSAzIGNhbG90dGVzIHBvdXIgbGUgbWF4aW11bSBkdSBjb25mb3J0IE1vdXNzZXMgaW50ZXJuZXMgY29tcGxcdTAwZTh0ZW1lbnQgZFx1MDBlOW1vbnRhYmxlcyBldCBsYXZhYmxlcyBCb3V0b25zLXByZXNzaW9uIGludFx1MDBlOWdyXHUwMGU5cyBcdTAwZTAgbGEgY2Fsb3R0ZSBwb3VyIHZpc2lcdTAwZThyZXMgRmVybWV0dXJlIGJvdWNsZSBEb3VibGUtRCBIb21vbG9nYXRpb24gQ0VFIFx1MDBhMCBFbnRyZXRpZW4gZHUgcHJvZHVpdDogXHUwMGEwIExlcyBjYXNxdWVzIHNvbnQgZmFpdHMgZGUgbWF0XHUwMGU5cmlhdXggcXVpIHBldXZlbnQgXHUwMGVhdHJlIGVuZG9tbWFnXHUwMGU5cyBwYXIgZGUgbm9tYnJldXggbmV0dG95YW50cyBjb3VyYW1tZW50IGRpc3BvbmlibGVzLiBOZXR0b3lleiBsZSBjYXNxdWUgXHUwMGUwIGwnYWlkZSBkJ3VuIGNoaWZmb24gZG91eCBvdSBkJ3VuZSBcdTAwZTlwb25nZSwgZCdlYXUgdGlcdTAwZThkZSBldCBkZSBzYXZvbiBkb3V4LiBMb3JzIGR1IHNcdTAwZTljaGFnZSwgdmVpbGxleiBcdTAwZTAgY2UgcXVlIGxlIGNhc3F1ZSBuZSBjaGF1ZmZlIHBhcyB0cm9wIGNhciBjZWxhIHBvdXJyYWl0IGVuZG9tbWFnZXIgbGEgZG91Ymx1cmUgRVBTIGludFx1MDBlOXJpZXVyZS4gTGFpc3NleiBzXHUwMGU5Y2hlciBcdTAwZTAgdGVtcFx1MDBlOXJhdHVyZSBhbWJpYW50ZSwgbmUgZmVybWV6IGphbWFpcyBsZXMgYXBwYXJlaWxzIGRlIGNoYXVmZmFnZSBjYXIgdW5lIHRlbXBcdTAwZTlyYXR1cmUgdHJvcCBcdTAwZTlsZXZcdTAwZTllIGVuZG9tbWFnZXJhIGxlIG1hdFx1MDBlOXJpYXUgZGUgbGEgZG91Ymx1cmUgaW50XHUwMGU5cmlldXJlIGV0IGxhIGNvcXVlIGludFx1MDBlOXJpZXVyZS4gTmV0dG95ZXogbGEgZG91Ymx1cmUgYW1vdmlibGUgXHUwMGUwIGxhIG1haW4gXHUwMGUwIGwnYWlkZSBkJ3VuZSBzZXJ2aWV0dGUsIHB1aXMgbGFpc3NleiBzXHUwMGU5Y2hlciBcdTAwZTAgbCdhaXIgbGlicmUgY2hhcXVlIGZvaXMgcXVlIGNlbGEgZXN0IHBvc3NpYmxlIHBvdXIgYXR0XHUwMGU5bnVlciBsZXMgZG9tbWFnZXMgY2F1c1x1MDBlOXMgcGFyIGxhIGNoYWxldXIuIiwiY291bnRyeSI6ImZyIiwicHJpY2UiOjE5OSwicHJpY2VXaXRob3V0UmViYXRlIjoxOTksIm1vbnRoUHJpY2UiOm51bGwsInJlYmF0ZVBlcmNlbnRhZ2UiOjAsInJlYmF0ZUVuZERhdGUiOm51bGwsImRlbGl2ZXJ5Q29zdCI6MCwicHJpY2VEaXNjb3VudFRleHQiOm51bGwsInRvdGFsUHJpY2UiOjE5OSwidW5pdFByaWNlIjpudWxsLCJjdXJyZW5jeSI6IkVVUiIsImF2YWlsYWJpbGl0eVN0YXR1cyI6ImluX3N0b2NrIiwidGltZVRvRGVsaXZlciI6ImxpdnJhaXNvbiBlbiByZWxhaXMgZW4gNzIgaGV1cmVzIiwiY29uZGl0aW9uIjoibmV3Iiwid2FycmFudHkiOm51bGwsImdyZWVuTGFiZWwiOm51bGwsImZsYWciOnsib2ZmZW5zaXZlQ29udGVudCI6ZmFsc2UsImdyZWVuUHJvZHVjdCI6ZmFsc2UsInNhbGVFdmVudCI6ZmFsc2V9LCJjb2RlIjp7ImVhbiI6IjgwNTQxNDEwOTI4MzkiLCJza3UiOm51bGwsIm1wbiI6bnVsbCwiZ3RpbiI6IjA4MDU0MTQxMDkyODM5In0sImltYWdlcyI6W3sidXJsIjoiaHR0cHM6XC9cL3Iua2Vsa29vLmNvbVwvcmVzaXplLnBocD9jb3VudHJ5PWZyJm1lcmNoYW50SWQ9MTAwNTEwMDgzJmNhdGVnb3J5SWQ9MTAwMDI5MjEzJnRyYWNraW5nSWQ9OTY5ODAxNDEmd2lkdGg9MzAwJmhlaWdodD0zMDAmaW1hZ2U9aHR0cHMlM0ElMkYlMkZ2aW50YWdlLW1vdG9ycy5uZXQlMkY0NTM1NS1sYXJnZV9kZWZhdWx0JTJGY2FzcXVlLWpldC12aW50YWdlLWdvb2R3b29kLWRtZC5qcGcmc2lnbj12NHlza0FSOGRHQWpvTjUzR29WMUxUNEhvWDI5WGprc1NNRW5TS2MwVnE0LSIsInpvb21VcmwiOiJodHRwczpcL1wvci5rZWxrb28uY29tXC9yZXNpemUucGhwP2NvdW50cnk9ZnImbWVyY2hhbnRJZD0xMDA1MTAwODMmY2F0ZWdvcnlJZD0xMDAwMjkyMTMmdHJhY2tpbmdJZD05Njk4MDE0MSZ3aWR0aD1hdXRvJmhlaWdodD1hdXRvJmltYWdlPWh0dHBzJTNBJTJGJTJGdmludGFnZS1tb3RvcnMubmV0JTJGNDUzNTUtbGFyZ2VfZGVmYXVsdCUyRmNhc3F1ZS1qZXQtdmludGFnZS1nb29kd29vZC1kbWQuanBnJnNpZ249djR5c2tBUjhkR0Fqb041M0dvVjFMVDRIb1gyOVhqa3NTTUVuU0tjMFZxNC0ifV0sImZlYXR1cmVzIjpudWxsLCJvZmZlclVybCI6eyJsYW5kaW5nVXJsIjoiaHR0cHM6XC9cL3ZpbnRhZ2UtbW90b3JzLm5ldFwvY2FzcXVlLWpldC12aW50YWdlLWdvb2R3b29kLWRtZC1wLTE0NTI0Lmh0bWwiLCJtb2JpbGVMYW5kaW5nVXJsIjoiaHR0cHM6XC9cL3ZpbnRhZ2UtbW90b3JzLm5ldFwvY2FzcXVlLWpldC12aW50YWdlLWdvb2R3b29kLWRtZC1wLTE0NTI0Lmh0bWwiLCJ0cmFja2VkVXJsIjoiaHR0cHM6XC9cL3RhZy5zaG9wcGluZy1mZWVkLmNvbVwvdjNcL3JlZGlyZWN0XC9wcm9kdWN0XC8wMmIwOTA4ZjEyNmI4Y2U0NWIwZWQ2YzE1NjlhOTFkYzJjYzg2ZTA3IiwibW9iaWxlVHJhY2tlZFVybCI6bnVsbH0sImdvVXJsIjoiaHR0cHM6XC9cL2ZyLWdvLmtlbGtvb2dyb3VwLm5ldFwvb2ZmZXJzZWFyY2hHbz8udHM9MTY5OTg5MDMyNTAyOCYuc2lnPTR2bTFhZnQ0dXNRYXdOQkIxMmhweUdtTEdjSS0mYWZmaWxpYXRpb25JZD05Njk4MDE0MSZjb21JZD0xMDA1MTAwODMmY291bnRyeT1mciZvZmZlcklkPTA2MDVhYWI3ZjhmNGZiNTRmY2RhNmRkYTJiMzhjNWFlJnNlcnZpY2U9MzcmdG9rZW5JZD0xZDk1NmIzOC1kNDUyLTQyODctOGQwYi05NjdiMjc5Y2NlNzImd2FpdD10cnVlIiwiZXN0aW1hdGVkQ3BjIjowLjEyNjEyNiwiZXN0aW1hdGVkTW9iaWxlQ3BjIjowLjA3NTY3NTYsImJyYW5kIjp7ImlkIjpudWxsLCJuYW1lIjoiRE1EIn0sIm1lcmNoYW50Ijp7ImlkIjoxMDA1MTAwODMsIm5hbWUiOiJWaW50YWdlIE1vdG9ycyIsImxvZ29VcmwiOiJodHRwczpcL1wvcjYua2Vsa29vLmNvbVwvZGF0YVwvbWVyY2hhbnRsb2dvc1wvMTAwNTEwMDgzXC9sb2dvLmpwZyJ9LCJtZXJjaGFudFByb3ZpZGVkQ2F0ZWdvcnkiOiJDYXNxdWUgamV0IHZpbnRhZ2UiLCJjYXRlZ29yeSI6eyJpZCI6MTAwMDI5MjEzLCJuYW1lIjoiVlx1MDBlYXRlbWVudHMgZGUgcHJvdGVjdGlvbiBwb3VyIGRldXgtcm91ZXMgZXQgcXVhZHMifSwiZ29vZ2xlUHJvZHVjdENhdGVnb3J5Ijp7ImlkIjpudWxsLCJuYW1lIjpudWxsfSwiZWNvdGF4IjpudWxsLCJtYWRlSW4iOm51bGwsImVmZmljaWVuY3lDbGFzcyI6bnVsbCwicGVyZm9ybWFuY2VTY29yZSI6MCwicHJvZHVjdCI6eyJpZCI6bnVsbCwicG9wdWxhcml0eSI6bnVsbH19';


        // when
        $expected_value = [
            'name' => 'Casque Jet Vintage Goodwood',
            'slug' => 'dmd-casque-jet-vintage-goodwood-dmd-0605aab7f8f4fb54fcda6dda2b38c5ae',
            'description' => 'Léger, sûr, confortable et élégant : que demander de plus?   Informations sur le produit:   Casque jet réalisé en fibre de verre 3 calottes pour le maximum du confort Mousses internes complètement démontables et lavables Boutons-pression intégrés à la calotte pour visières Fermeture boucle Double-D Homologation CEE   Entretien du produit:   Les casques sont faits de matériaux qui peuvent être endommagés par de nombreux nettoyants couramment disponibles. Nettoyez le casque à l\'aide d\'un chiffon doux ou d\'une éponge, d\'eau tiède et de savon doux. Lors du séchage, veillez à ce que le casque ne chauffe pas trop car cela pourrait endommager la doublure EPS intérieure. Laissez sécher à température ambiante, ne fermez jamais les appareils de chauffage car une température trop élevée endommagera le matériau de la doublure intérieure et la coque intérieure. Nettoyez la doublure amovible à la main à l\'aide d\'une serviette, puis laissez sécher à l\'air libre chaque fois que cela est possible pour atténuer les dommages causés par la chaleur.',
            'brand_original' => 'DMD',
            'merchant_original' => 'Vintage Motors',
            'currency_original' => 'EUR',
            'category_original' => 'vêtements de protection pour deux-roues et quads|casque jet vintage',
            'color_original' => '',
            'price' => 199.0,
            'old_price' => 0.0,
            'reduction' => 0,
            'url' => 'https://fr-go.kelkoogroup.net/offersearchGo?.ts=1699890325028&.sig=4vm1aft4usQawNBB12hpyGmLGcI-&affiliationId=96980141&comId=100510083&country=fr&offerId=0605aab7f8f4fb54fcda6dda2b38c5ae&service=37&tokenId=1d956b38-d452-4287-8d0b-967b279cce72&wait=true',
            'image_url' => 'https://r.kelkoo.com/resize.php?country=fr&merchantId=100510083&categoryId=100029213&trackingId=96980141&width=auto&height=auto&image=https%3A%2F%2Fvintage-motors.net%2F45355-large_default%2Fcasque-jet-vintage-goodwood-dmd.jpg&sign=v4yskAR8dGAjoN53GoV1LT4HoX29XjksSMEnSKc0Vq4-',
            'gender' => 'mixte',
            'col' => '',
            'coupe' => '',
            'manches' => '',
            'material' => '',
            'model' => '',
            'motifs' => NULL,
            'event' => '',
            'style' => NULL,
            'size' => '',
            'livraison' => 'livraison en relais en 72 heures',
        ];

        // then
        $this->assertEquals(
            $expected_value,
            $this->parse_payload($payload)
        );
    }

    public function test__parse_row__from_Promod_material_size_color_model()
    {
        // given
        $payload = 'eyJvZmZlcklkIjoiMDI3MTYyNWVmNjQ2NTRjZDJmZGMyNWI4MmJhOWVjZWMiLCJvZmZlclR5cGUiOm51bGwsInRpdGxlIjoiUHJvbW9kIFBhbnRhbG9uIGNhcmdvIGVuIHRvaWxlIEZlbW1lIEJlaWdlIDM4IiwibGFzdFVwZGF0ZURhdGUiOiIyMDIzLTExLTEwVDA3OjI1OjMyWiIsImRlc2NyaXB0aW9uIjoiUG9ydFx1MDBlOSB0YWlsbGUgYmFzc2Ugb3UgdGFpbGxlIGhhdXRlIGNlaW50dXJcdTAwZTllLCBsZSBwYW50YWxvbiBjYXJnbyBwcmVuZCBsZSBsYXJnZSBldCBzJ2Vudm9sZSBkYW5zIGxlcyBzb25kYWdlcyAhIENvdXBlIGxhcmdlLiBQYXNzYW50cy4gT3V2ZXJ0dXJlIHBhciBibyIsImNvdW50cnkiOiJmciIsInByaWNlIjoyMi45OSwicHJpY2VXaXRob3V0UmViYXRlIjo0NS45OSwibW9udGhQcmljZSI6bnVsbCwicmViYXRlUGVyY2VudGFnZSI6NTAsInJlYmF0ZUVuZERhdGUiOm51bGwsImRlbGl2ZXJ5Q29zdCI6NS40LCJwcmljZURpc2NvdW50VGV4dCI6bnVsbCwidG90YWxQcmljZSI6MjguMzksInVuaXRQcmljZSI6bnVsbCwiY3VycmVuY3kiOiJFVVIiLCJhdmFpbGFiaWxpdHlTdGF0dXMiOiJjaGVja19zaXRlIiwidGltZVRvRGVsaXZlciI6bnVsbCwiY29uZGl0aW9uIjoibmV3Iiwid2FycmFudHkiOm51bGwsImdyZWVuTGFiZWwiOm51bGwsImZsYWciOnsib2ZmZW5zaXZlQ29udGVudCI6ZmFsc2UsImdyZWVuUHJvZHVjdCI6ZmFsc2UsInNhbGVFdmVudCI6ZmFsc2V9LCJjb2RlIjp7ImVhbiI6IjM2MDYyMzUxODUyNzYiLCJza3UiOm51bGwsIm1wbiI6bnVsbCwiZ3RpbiI6IjAzNjA2MjM1MTg1Mjc2In0sImltYWdlcyI6W3sidXJsIjoiaHR0cHM6XC9cL3Iua2Vsa29vLmNvbVwvcmVzaXplLnBocD9jb3VudHJ5PWZyJm1lcmNoYW50SWQ9MTAwNDU0MDI0JmNhdGVnb3J5SWQ9MTA4MzAxJnRyYWNraW5nSWQ9OTY5ODAxNDEmd2lkdGg9MzAwJmhlaWdodD0zMDAmaW1hZ2U9aHR0cHMlM0ElMkYlMkZhc3NldC5wcm9tb2QuY29tJTJGcHJvZHVjdCUyRjE2MTc3MC1nei0xNjkyNzE2OTg2LmpwZyZzaWduPTZvSGJYMFdYQjIySzhVczVMRDdpVHNXOGhGQ1RnRml6eE01WFVHLkQ2bjQtIiwiem9vbVVybCI6Imh0dHBzOlwvXC9yLmtlbGtvby5jb21cL3Jlc2l6ZS5waHA/Y291bnRyeT1mciZtZXJjaGFudElkPTEwMDQ1NDAyNCZjYXRlZ29yeUlkPTEwODMwMSZ0cmFja2luZ0lkPTk2OTgwMTQxJndpZHRoPWF1dG8maGVpZ2h0PWF1dG8maW1hZ2U9aHR0cHMlM0ElMkYlMkZhc3NldC5wcm9tb2QuY29tJTJGcHJvZHVjdCUyRjE2MTc3MC1nei0xNjkyNzE2OTg2LmpwZyZzaWduPTZvSGJYMFdYQjIySzhVczVMRDdpVHNXOGhGQ1RnRml6eE01WFVHLkQ2bjQtIn1dLCJmZWF0dXJlcyI6eyJjb2xvciI6eyJsYWJlbCI6IkNvdWxldXIiLCJ2YWx1ZXMiOlt7ImxhYmVsIjoiQmVpZ2UiLCJ2YWx1ZSI6ImJlaWdlIn1dfSwiY3V0Ijp7ImxhYmVsIjoiVHlwZSBkZSBNYW5jaGVzIiwidmFsdWVzIjpbXX0sImdlbmRlciI6eyJsYWJlbCI6IkdlbnJlIiwidmFsdWVzIjpbeyJsYWJlbCI6IkZlbW1lIiwidmFsdWUiOiJmZW1tZSJ9XX0sIm1hdGVyaWFsIjp7ImxhYmVsIjoiTWF0aVx1MDBlOHJlIiwidmFsdWVzIjpbeyJsYWJlbCI6IkVuIFRpc3N1IiwidmFsdWUiOiJlbi10aXNzdSJ9LHsibGFiZWwiOiJUb2lsZSIsInZhbHVlIjoidG9pbGUifV19LCJwcmludCI6eyJsYWJlbCI6Ik1vdGlmcyIsInZhbHVlcyI6W119LCJzaXplIjp7ImxhYmVsIjoiVGFpbGxlIiwidmFsdWVzIjpbeyJsYWJlbCI6IlRhaWxsZSBMIiwidmFsdWUiOiJ0YWlsbGUtbCJ9XX0sInN0eWxlIjp7ImxhYmVsIjoiU3R5bGUiLCJ2YWx1ZXMiOltdfSwidHlwZSI6eyJsYWJlbCI6IlR5cGUiLCJ2YWx1ZXMiOlt7ImxhYmVsIjoiUGFudGFsb24gQ2FyZ28iLCJ2YWx1ZSI6InBhbnRhbG9uLWNhcmdvIn0seyJsYWJlbCI6IlBhbnRhbG9uIGV0IEplYW5zIiwidmFsdWUiOiJwYW50YWxvbi1ldC1qZWFucyJ9XX19LCJvZmZlclVybCI6eyJsYW5kaW5nVXJsIjoiaHR0cHM6XC9cL3d3dy5wcm9tb2QuZnJcL2ZyLWZyXC9wYW50YWxvbi1jYXJnby1lbi10b2lsZS1mZW1tZS1iZWlnZS0tcDE2MTc3MFwvP3V0bV9zb3VyY2U9bGVndWlkZSZ1dG1fbWVkaXVtPWNvbXBhcmF0ZXVycyZ1dG1fY2FtcGFpZ249Y2F0YWxvZ3VldXRtX2NvbnRlbnQ9MTMyMzEyMTkwMjQiLCJtb2JpbGVMYW5kaW5nVXJsIjoiaHR0cHM6XC9cL3d3dy5wcm9tb2QuZnJcL2ZyLWZyXC9wYW50YWxvbi1jYXJnby1lbi10b2lsZS1mZW1tZS1iZWlnZS0tcDE2MTc3MFwvP3V0bV9zb3VyY2U9bGVndWlkZSZ1dG1fbWVkaXVtPWNvbXBhcmF0ZXVycyZ1dG1fY2FtcGFpZ249Y2F0YWxvZ3VldXRtX2NvbnRlbnQ9MTMyMzEyMTkwMjQiLCJ0cmFja2VkVXJsIjoiaHR0cHM6XC9cL3Byb21vZGZyLmNvbW1hbmRlcjEuY29tXC9jM1wvP3Rjcz0zOTYmY2huPXNob3Bib3Qmc3JjPWxlZ3VpZGUmdXJsPWh0dHBzOlwvXC93d3cucHJvbW9kLmZyXC9mci1mclwvcGFudGFsb24tY2FyZ28tZW4tdG9pbGUtZmVtbWUtYmVpZ2UtLXAxNjE3NzBcLz91dG1fc291cmNlPWxlZ3VpZGUmdXRtX21lZGl1bT1jb21wYXJhdGV1cnMmdXRtX2NhbXBhaWduPWNhdGFsb2d1ZXV0bV9jb250ZW50PTEzMjMxMjE5MDI0IiwibW9iaWxlVHJhY2tlZFVybCI6bnVsbH0sImdvVXJsIjoiaHR0cHM6XC9cL2ZyLWdvLmtlbGtvb2dyb3VwLm5ldFwvb2ZmZXJzZWFyY2hHbz8udHM9MTY5OTg5MDE3NzYzNCYuc2lnPTgzR3VmWWtiQnZmbzlFYU9LbjRXVUtwMUNCYy0mYWZmaWxpYXRpb25JZD05Njk4MDE0MSZjb21JZD0xMDA0NTQwMjQmY291bnRyeT1mciZvZmZlcklkPTAyNzE2MjVlZjY0NjU0Y2QyZmRjMjViODJiYTllY2VjJnNlcnZpY2U9MzcmdG9rZW5JZD0xZDk1NmIzOC1kNDUyLTQyODctOGQwYi05NjdiMjc5Y2NlNzImd2FpdD10cnVlIiwiZXN0aW1hdGVkQ3BjIjowLjEwOTIsImVzdGltYXRlZE1vYmlsZUNwYyI6MC4wMzY0LCJicmFuZCI6eyJpZCI6bnVsbCwibmFtZSI6bnVsbH0sIm1lcmNoYW50Ijp7ImlkIjoxMDA0NTQwMjQsIm5hbWUiOiJQcm9tb2QiLCJsb2dvVXJsIjoiaHR0cHM6XC9cL3I2LmtlbGtvby5jb21cL2RhdGFcL21lcmNoYW50bG9nb3NcLzEwMDQ1NDAyNFwvbG9nby5qcGcifSwibWVyY2hhbnRQcm92aWRlZENhdGVnb3J5IjoiUGFudGFsb25zIGxhcmdlcyIsImNhdGVnb3J5Ijp7ImlkIjoxMDgzMDEsIm5hbWUiOiJWXHUwMGVhdGVtZW50cyBwb3VyIGZlbW1lcyJ9LCJnb29nbGVQcm9kdWN0Q2F0ZWdvcnkiOnsiaWQiOjE2MDQsIm5hbWUiOm51bGx9LCJlY290YXgiOm51bGwsIm1hZGVJbiI6bnVsbCwiZWZmaWNpZW5jeUNsYXNzIjpudWxsLCJwZXJmb3JtYW5jZVNjb3JlIjowLCJwcm9kdWN0Ijp7ImlkIjpudWxsLCJwb3B1bGFyaXR5IjpudWxsfX0=';


        // when
        $expected_value = [
            'name' => 'Promod Pantalon cargo en toile 38',
            'slug' => 'promod-pantalon-cargo-en-toile-femme-beige-38-0271625ef64654cd2fdc25b82ba9ecec',
            'description' => 'Porté taille basse ou taille haute ceinturée, le pantalon cargo prend le large et s\'envole dans les sondages ! Coupe large. Passants. Ouverture par bo',
            'brand_original' => 'Promod',
            'merchant_original' => 'Promod',
            'currency_original' => 'EUR',
            'category_original' => 'vêtements pour femmes|pantalons larges',
            'color_original' => 'beige',
            'price' => 22.99,
            'old_price' => 45.99,
            'reduction' => 50.0,
            'url' => 'https://fr-go.kelkoogroup.net/offersearchGo?.ts=1699890177634&.sig=83GufYkbBvfo9EaOKn4WUKp1CBc-&affiliationId=96980141&comId=100454024&country=fr&offerId=0271625ef64654cd2fdc25b82ba9ecec&service=37&tokenId=1d956b38-d452-4287-8d0b-967b279cce72&wait=true',
            'image_url' => 'https://r.kelkoo.com/resize.php?country=fr&merchantId=100454024&categoryId=108301&trackingId=96980141&width=auto&height=auto&image=https%3A%2F%2Fasset.promod.com%2Fproduct%2F161770-gz-1692716986.jpg&sign=6oHbX0WXB22K8Us5LD7iTsW8hFCTgFizxM5XUG.D6n4-',
            'gender' => 'femme',
            'col' => '',
            'coupe' => '',
            'manches' => '',
            'material' => 'en tissu|toile',
            'model' => 'Pantalon Cargo|Pantalon et Jeans',
            'motifs' => NULL,
            'event' => '',
            'style' => NULL,
            'size' => 'TAILLE L',
            'livraison' => '',
        ];

        // then
        $this->assertEquals(
            $expected_value,
            $this->parse_payload($payload)
        );
    }

}
