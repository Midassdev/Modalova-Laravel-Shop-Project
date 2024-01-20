<?php

namespace App\Services\UpdateSources;

use App\Models\Source;

class Rakuten extends BaseSource
{
    protected $requiredEnvParams = [
        'RAKUTEN_FTP_USER',
        'RAKUTEN_FTP_PASS',
        'RAKUTEN_FTP_HOST',
        'RAKUTEN_SID_NUMBER',
        'RAKUTEN_CONSUMER_KEY',
        'RAKUTEN_CONSUMER_SECRET',
    ];

    private $client;
    private $access_token;

    public function __construct() {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => 'https://api.linksynergy.com/',
        ]);
    }

    public function update()
    {
        if (! $this->get_access_token()) {
            return error_log('[-] Cannot get access token for Rakuten');
        }

        if (! $advertisersList = $this->get_advertisers_ids_list()) {
            return error_log('[-] Cannot update Advertisers from Rakuten');
        }

        foreach ($advertisersList as $advertiser) {
            if (empty($advertiser->merchantname)) {
                continue;
            }

            foreach(['xml', 'txt'] as $format) {
                $name = $advertiser->merchantname;;

                $this->update_source(
                    "{$name} [{$format}]",
                    $name,
                    $this->get_url($advertiser->mid, $format),
                    $advertiser,
                    ['nb_of_products' => -1],
                );

            }
        }
    }

    private function get_advertisers_ids_list()
    {
        $res = $this->client->request(
            'GET',
            'advertisersearch/1.0',
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->access_token,
                ],
            ]
        );

        $advertisers = [];
        if (200 == $res->getStatusCode()) {
            $xml = new \SimpleXMLElement($res->getBody());

            foreach ($xml->midlist->merchant as $item) {
                $advertisers[] = $item;
            }
        }

        return $advertisers;
    }

    private function get_url($id, $format = 'txt')
    {
        return 'ftp://'
            . env('RAKUTEN_FTP_USER')
            . ':' . env('RAKUTEN_FTP_PASS')
            . '@' . env('RAKUTEN_FTP_HOST')
            . '/' . $id . '_'
            . env('RAKUTEN_SID_NUMBER')
            . '_mp.' . $format . '.gz';
    }

    private function get_access_token()
    {
        $basicAuthToken = base64_encode(env('RAKUTEN_CONSUMER_KEY').':'.env('RAKUTEN_CONSUMER_SECRET'));

        $response = $this->client->request(
            'POST',
            'token',
            [
                'headers' => [
                    'Authorization' => 'Basic ' . $basicAuthToken,
                ],

                'form_params' => [
                    'scope' => env('RAKUTEN_SID_NUMBER')
                ]
            ]
        );

        if (200 == $response->getStatusCode()) {
            $response = json_decode($response->getBody());

            return $this->access_token = $response->access_token;
        }
    }
}
