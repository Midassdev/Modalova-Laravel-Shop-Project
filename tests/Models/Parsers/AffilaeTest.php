<?php

namespace Tests\Models\Parsers;

use App\Models\Parsers\Affilae;

class AffilaeTest extends BaseParser
{
	public static $klass = Affilae::class;

	public function test__parse_row__from_Faguo()
	{
		$payload = "eyJpZCI6IjE5NjciLCJ0aXRsZSI6IlQtc2hpcnQgZW4gY290b24gZ3JpcyAnSidhaW1lIG1vbiB2XHUwMGU5bG8nIiwiZGVzY3JpcHRpb24iOiJDZSB0LXNoaXJ0IGVuIGNvdG9uIFx1MDBlMCBtZXNzYWdlIGltcHJpbVx1MDBlOSBlc3QgbGUgbW9kXHUwMGU4bGUgaWNvbmlxdWUgZGUgRkFHVU8uIEludGVtcG9yZWwsIGlsIGFib3JkZSBsYSBtb2RlIGF2ZWMgbFx1MDBlOWdcdTAwZThyZXRcdTAwZTkgZXQgaHVtb3VyLiBJbCBhc3N1cmUgdW4gbG9vayB0ZW5kYW5jZSBldCBkXHUwMGU5Y29udHJhY3RcdTAwZTkuIFNhIGNvdXBlIGRyb2l0ZSBldCBzb24gZW5jb2x1cmUgcm9uZGUgcydhZGFwdGVudCBcdTAwZTAgdG91dGVzIGxlcyBzaWxob3VldHRlcy4gUG9pbnRzIGNsXHUwMGU5cyA6IENvbCByb25kIEZpbml0aW9ucyBib3JkLWNcdTAwZjR0ZSBCYXMgZGUgbWFuY2hlcyBldCBiYXMgZGUgY29ycHMgYXZlYyBmaW5pdGlvbiBvdXJsZXQgMiBhaWd1aWxsZXMgU1x1MDBlOXJpZ3JhcGhpZSBlbmNyZSBcdTAwZTAgbCdlYXUgQm91dG9uIGNvY28gYmFzIGRlIGNvcnBzIGdhdWNoZSBHcm9zIGdyYWluIGJsZXVcL2JsYW5jIGRhbnMgbCdlbmNvbHVyZSBkb3MgSW1wcmltXHUwMGU5IEZBR1VPIFx1MDBlMCBsJ2ludFx1MDBlOXJpZXVyIGRlIGwnZW5jb2x1cmUgZG9zIGVuIHByaW50IFx1MDBlMCBsJ2VhdSBDb25mZWN0aW9ublx1MDBlOSBlbiBDaGluZSBMYXZhZ2UgbWFjaGluZSAzMFx1MDBiMCBzdXIgbCdlbnZlcnMgUkVGIDogUzE1VFMwMTAxIEdSWTA0IiwiZ29vZ2xlIHByb2R1Y3QgY2F0ZWdvcnkiOiJBY2N1ZWlsID4gVlx1MDBlYXRlbWVudHMiLCJwcm9kdWN0IHR5cGUiOiJULXNoaXJ0IiwibGluayI6Imh0dHBzOlwvXC93d3cuZmFndW8tc3RvcmUuY29tXC9mclwvdmV0ZW1lbnRzXC8xOTY3LXRzaGlydC1lbi1jb3Rvbi1hcmN5LWdyaXMtai1haW1lLW1vbi12ZWxvLmh0bWwiLCJpbWFnZSBsaW5rIjoiaHR0cHM6XC9cL3d3dy5mYWd1by1zdG9yZS5jb21cLzIwNTk0LWxhcmdlX3NjZW5lXC90c2hpcnQtZW4tY290b24tYXJjeS1ncmlzLWotYWltZS1tb24tdmVsby5qcGciLCJhZGRpdGlvbmFsIGltYWdlIGxpbmsiOiJodHRwczpcL1wvd3d3LmZhZ3VvLXN0b3JlLmNvbVwvMjA1OTUtbGFyZ2Vfc2NlbmVcL3RzaGlydC1lbi1jb3Rvbi1hcmN5LWdyaXMtai1haW1lLW1vbi12ZWxvLmpwZyIsImNvbmRpdGlvbiI6Im5ldyIsImF2YWlsYWJpbGl0eSI6IiIsInByaWNlIjoiMTcuNSIsInNhbGUgcHJpY2UiOiIxNy41Iiwic2FsZSBwcmljZSBlZmZlY3RpdmUgZGF0ZSI6IiIsImJyYW5kIjoiRkFHVU8iLCJndGluIjoiIiwibXBuIjoiIiwiZ2VuZGVyIjoiSE9NTUUiLCJhZ2UgZ3JvdXAiOiIiLCJjb2xvciI6IkdyaXMiLCJzaXplIjoiIiwibWF0ZXJpYWwiOiIiLCJwYXR0ZXJuIjoiIiwiaXRlbSBncm91cCBpZCI6IiIsInRheCI6IiIsInNoaXBwaW5nIjoiNS45MCIsInNoaXBwaW5nIHdlaWdodCI6IiIsIm9ubGluZSBvbmx5IjoiIiwiZXhjbHVkZWQgZGVzdGluYXRpb24iOiIiLCJleHBpcmF0aW9uIGRhdGUiOiIiLCJsb3lhbHR5IHBvaW50cyI6IiIsImFkd29yZHNfZ3JvdXBpbmciOiIiLCJhZHdvcmRzX2xhYmVscyI6IiIsImFkd29yZHNfcHVibGlzaCI6IiIsImFkd29yZHNfcmVkaXJlY3QiOiIiLCJpZGVudGlmaWVyX2V4aXN0cyI6IiIsImN1c3RvbV9sYWJlbF8wIjoiIiwiY3VzdG9tX2xhYmVsXzEiOiIiLCJjdXN0b21fbGFiZWxfMiI6IiIsImN1c3RvbV9sYWJlbF8zIjoiIiwiY3VzdG9tX2xhYmVsXzQiOiIiLCJMYW5kaW5nX3BhZ2VfdXJsIjoiIiwiRGVsYWkgZGUgbGl2cmFpc29uIjoiIn0=";

		$expected_value = [
			'name' => 'T-shirt en coton \'J\'aime mon vélo\'',
			'slug' => 't-shirt-en-coton-gris-j-aime-mon-v-lo-1967',
			'description' => 'Ce t-shirt en coton à message imprimé est le modèle iconique de FAGUO. Intemporel, il aborde la mode avec légèreté et humour. Il assure un look tendance et décontracté. Sa coupe droite et son encolure ronde s\'adaptent à toutes les silhouettes. Points clés : Col rond Finitions bord-côte Bas de manches et bas de corps avec finition ourlet 2 aiguilles Sérigraphie encre à l\'eau Bouton coco bas de corps gauche Gros grain bleu/blanc dans l\'encolure dos Imprimé FAGUO à l\'intérieur de l\'encolure dos en print à l\'eau Confectionné en Chine Lavage machine 30° sur l\'envers REF : S15TS0101 GRY04',
			'price' => 17.5,
			'old_price' => 0.0,
			'reduction' => 0,
			'url' => 'https://www.faguo-store.com/fr/vetements/1967-tshirt-en-coton-arcy-gris-j-aime-mon-velo.html',
			'merchant_original' => 'Source Title',
			'brand_original' => 'FAGUO',
			'category_original' => 'accueil > vêtements|t-shirt',
			'gender' => 'homme',
			'currency_original' => 'EUR',
			'color_original' => 'gris',
			'image_url' => 'https://www.faguo-store.com/20594-large_scene/tshirt-en-coton-arcy-gris-j-aime-mon-velo.jpg',
			'col' => 'col rond',
			'coupe' => 'coupe classique/droite',
			'manches' => '',
			'material' => '',
			'model' => NULL,
			'motifs' => '',
			'event' => '',
			'style' => NULL,
			'size' => '',
			'livraison' => '',
		];

		$this->assertEquals($expected_value, $this->parse_payload($payload));
	}

	public function test__parse_row__from_Shaman()
	{
		self::$headers = '';
		$payload = 'eyJpZCI6IkZSNzMxIiwidGl0bGUiOiJUZWUtc2hpcnQgXCJHcmFpbmUgZGUgdm95b3VcIiIsImRlc2NyaXB0aW9uIjoiVGVlLXNoaXJ0IFwiR3JhaW5lIGRlIHZveW91XCIuIiwibGluayI6Imh0dHBzOlwvXC93d3cuc2hhbWFuLXNob3AuZnJcL3RlZS1zaGlydFwvNzMxLWdyYWluZS12b3lvdS5odG1sIiwiaW1hZ2VfbGluayI6Imh0dHBzOlwvXC9tZWRpYS5zaGFtYW4tc2hvcC5mclwvNjYyMzktdGhpY2tib3hfZGVmYXVsdFwvZ3JhaW5lLXZveW91LmpwZyIsImNvbmRpdGlvbiI6Im5ldyIsImFkZGl0aW9uYWxfaW1hZ2VfbGluayI6WyJodHRwczpcL1wvbWVkaWEuc2hhbWFuLXNob3AuZnJcLzY2MjQxLXRoaWNrYm94X2RlZmF1bHRcL2dyYWluZS12b3lvdS5qcGciLCJodHRwczpcL1wvbWVkaWEuc2hhbWFuLXNob3AuZnJcLzg3MzI3LXRoaWNrYm94X2RlZmF1bHRcL2dyYWluZS12b3lvdS5qcGciLCJodHRwczpcL1wvbWVkaWEuc2hhbWFuLXNob3AuZnJcLzg3MzIyLXRoaWNrYm94X2RlZmF1bHRcL2dyYWluZS12b3lvdS5qcGciLCJodHRwczpcL1wvbWVkaWEuc2hhbWFuLXNob3AuZnJcLzg3MzI2LXRoaWNrYm94X2RlZmF1bHRcL2dyYWluZS12b3lvdS5qcGciLCJodHRwczpcL1wvbWVkaWEuc2hhbWFuLXNob3AuZnJcLzg3MzI0LXRoaWNrYm94X2RlZmF1bHRcL2dyYWluZS12b3lvdS5qcGciXSwicHJvZHVjdF90eXBlIjoiSG9tbWUgPiBUZWUtc2hpcnQiLCJnb29nbGVfcHJvZHVjdF9jYXRlZ29yeSI6IlZcdTAwZWF0ZW1lbnRzIGV0IGFjY2Vzc29pcmVzID4gVlx1MDBlYXRlbWVudHMgPiBIYXV0cyIsImF2YWlsYWJpbGl0eSI6ImluIHN0b2NrIiwicHJpY2UiOiIyOS4wMCBFVVIiLCJtcG4iOiJUUyBIIElNUEVSSUFMIEdyYWluZSBkZSB2b3lvdSIsImlkZW50aWZpZXJfZXhpc3RzIjoiRkFMU0UiLCJnZW5kZXIiOiJtYWxlIiwic2hpcHBpbmdfd2VpZ2h0IjoiMC4xNSBrZyIsInNoaXBwaW5nIjp7ImNvdW50cnkiOiJGUiIsInByaWNlIjoiMC4wMCBFVVIifX0=';
		$expected_value = array(
			'name' => 'Tee-shirt "Graine de voyou"',
			'slug' => 'tee-shirt-graine-de-voyou-fr731',
			'description' => 'Tee-shirt "Graine de voyou".',
			'price' => 29.0,
			'old_price' => 0.0,
			'reduction' => 0,
			'url' => 'https://www.shaman-shop.fr/tee-shirt/731-graine-voyou.html',
			'merchant_original' => 'Source Title',
			'brand_original' => 'Source Title',
			'category_original' => 'homme > tee-shirt',
			'gender' => 'homme',
			'currency_original' => 'EUR',
			'color_original' => '',
			'image_url' => 'https://media.shaman-shop.fr/66239-thickbox_default/graine-voyou.jpg',
			'col' => '',
			'coupe' => '',
			'manches' => '',
			'material' => '',
			'model' => NULL,
			'motifs' => '',
			'event' => '',
			'style' => NULL,
			'size' => '',
			'livraison' => '',
		);
		$this->assertEquals($expected_value, $this->parse_payload($payload));
	}
}
