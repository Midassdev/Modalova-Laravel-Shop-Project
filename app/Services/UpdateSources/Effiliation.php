<?php

namespace App\Services\UpdateSources;

use App\Models\Source;

class Effiliation extends BaseSource
{
    protected $requiredEnvParams = [
        'EFFILIATION_API_KEY',
    ];

    private $feedsUrl = 'https://apiv2.effiliation.com/apiv2/productfeeds.xml';

    public function update()
    {
        $client = new \GuzzleHttp\Client();

        try {
            $response = $client->request(
                'GET',
                $this->feedsUrl,
                [
                    'query' => [
                        'key' => env('EFFILIATION_API_KEY'),
                        'filter' => 'mines',
                        'rand' => rand(), // to avoid 429 errors
                    ],
                ]
            );
            $content = $response->getBody()->getContents();
        } catch (\Exception $e) {
            throw new \Exception('[-] Cannot update Sources from Effiliation: cannot get feeds ('.$e->getResponse()->getBody()->getContents().')');
        }

        if (! empty($content)) {
            $xml = simplexml_load_string($content);

            foreach ($xml->feed as $feed) {
                $this->update_source(
                    $feed->nomprogramme,
                    $feed->nomprogramme,
                    $feed->code,
                    $feed,
                    ['nb_of_products' => -1],
                );
            }
        }
    }
}
