<?php

namespace App\Services\UpdateSources;

use App\Models\Source;

class Cj extends BaseSource
{
    protected $requiredEnvParams = [
        'CJ_USER',
        'CJ_PASS',
        'CJ_HTTP_USER',
        'CJ_HTTP_PASS',
        'CJ_CATALOG_ID',
    ];

    public function update()
    {
        $client = new \GuzzleHttp\Client([
            'base_uri' => 'https://members.cj.com/member/',
            'cookies' => true,
            'allow_redirects' => false,
        ]);

        $client->request(
            'POST',
            'login/foundation/memberlogin.do', [
                'form_params' => [
                    'uname' => env('CJ_USER'),
                    'pw' => env('CJ_PASS')
                ],
            ]
        );

        $res = $client->get(
            'api/publisher/'.env('CJ_HTTP_USER').'/subscription/'.env('CJ_CATALOG_ID').'/productCatalogDetails?page=0&rpp=10000'
        );

        $raw = json_decode($res->getBody());

        if(!@$raw->details)
            throw new \Exception('[-] Cannot update Sources from CJ: cannot retrieve details (wrong login?)');

        foreach ($raw->details as $data) {
            $this->update_source(
                $data->advertiserName.' - '.$data->adName,
                $data->advertiserName,
                $this->get_url($data),
                $data,
                [
                    'language' => 'Language:'.$data->targetCountry.' - Region:'.$data->targetCountry,
                    'nb_of_products' => $data->numberOfRecords,
                ],
            );
        }
    }

    private function get_url($data)
    {
        return str_replace(
          ['___', '__'],
          ['_', '_'],
          'https://'.
          env('CJ_HTTP_USER').':'.env('CJ_HTTP_PASS').
          '@datatransfer.cj.com/datatransfer/files/'.
          env('CJ_HTTP_USER').
          '/outgoing/productcatalog/'.
          env('CJ_CATALOG_ID').
          '/'.
          str_replace(
            [' ', '(', ')', '.', ','],
            ['_', '_', '_', '_', '_'],
            str_replace(
              ['-'],
              [' '],
              $data->advertiserName
          ).
            '-'.
            str_replace(
              ['-'],
              [' '],
              $data->adName
          )
        ).
          '-shopping.txt.zip'
      );
    }
}
