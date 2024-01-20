<?php

namespace App\Services\UpdateSources;

class Impact extends BaseSource
{
    const API_BASE_URL = 'https://api.impact.com';

    protected $requiredEnvParams = [
        'IMPACT_SID',
        'IMPACT_TOKEN',
    ];

    private $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client([
            'base_uri' => self::API_BASE_URL.'/Mediapartners/'.env('IMPACT_SID').'/',
            'auth' => [env('IMPACT_SID'), env('IMPACT_TOKEN')],
            'headers' => [
                'Accept' => 'application/json',
                'IR-Version' => 12,
            ],
        ]);
    }

    public function update()
    {
        $catalogs = $this->getCatalogs();

        foreach ($catalogs as $catalog) {
            foreach ($catalog->FTPLocations as $ftp_location) {
                $name = $catalog->CampaignName;
                $this->update_source(
                    $name.' ('.basename($ftp_location).')',
                    $name,
                    $ftp_location,
                    $catalog,
                    [
                        'language' => $catalog->Language,
                        'nb_of_products' => $catalog->NumberOfItems,
                    ],
                );
            }
        }
    }

    private function getCatalogs()
    {
        $catalogs = [];
        $page = 1;

        do {
            $response = $this->client->get("Catalogs?page={$page}");
            $data = json_decode($response->getBody()->getContents());
            $catalogs = array_merge($catalogs, $data->Catalogs);

            $max_page = $data->{'@numpages'};
        } while ($page++ < $max_page);

        return $catalogs;
    }
}
