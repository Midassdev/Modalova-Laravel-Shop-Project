<?php

declare(strict_types=1);

namespace App\Services\UpdateSources;

use Illuminate\Support\Facades\Http;

class Kelkoo extends BaseSource
{
    const API_ENDPOINT          = 'https://api.kelkoogroup.net/publisher/shopping/v2/';
    const MERCHANTS_FEED_FORMAT = 'json'; // csv, json or xml
    const OFFERS_FEED_FORMAT    = 'json'; // csv, json or xml

    private $httpClient;
    private $country;

    protected $requiredEnvParams = [
        'KELKOO_COUNTRY',
        'KELKOO_API_TOKEN',
    ];

    public function __construct()
    {
        $this->country    = env('KELKOO_COUNTRY');
        $this->httpClient = Http::withToken(env('KELKOO_API_TOKEN'))
            ->withHeaders(['Accept-Encoding' => 'gzip'])
            ->baseUrl(self::API_ENDPOINT);
    }

    public function update()
    {
        $merchants = $this->getMerchants();

        foreach ($merchants as $merchant) {
            $numberOfOffers = collect($merchant['categories'])->sum('numberOfOffers');
            $offerPath      = $this->getOfferPath($merchant['id']);

            $this->update_source(
                $this->getUniqueName($merchant),
                $merchant['name'],
                $offerPath,
                $merchant,
                [
                    'language'       => $this->country,
                    'nb_of_products' => $numberOfOffers,
                ]
            );
        }
    }

    private function getMerchants(): ?array
    {
        $response = $this->httpClient->acceptJson()->get('feeds/merchants', [
            'country'       => $this->country,
            'format'        => self::MERCHANTS_FEED_FORMAT,
            'offerMatch'    => 'yes',
            'merchantMatch' => 'yes', // filters on the merchant to get only the merchant available for links
        ]);

        if (!$response->ok()) {
            throw new \Exception("Failed to retrieve merchants from Kelkoo.");
        }

        return $response->json();
    }

    private function getUniqueName(array $merchantInfo): string
    {
        return "{$merchantInfo['name']} - {$this->country} - {$merchantInfo['currency']}";
    }

    private function getOfferPath(int $merchantId): string
    {
        $queryParams = [
            'country'     => $this->country,
            'merchantId'  => $merchantId,
            'format'      => self::OFFERS_FEED_FORMAT,
            'fieldsAlias' => 'all', // minimal, all
        ];

        return self::API_ENDPOINT . 'feeds/offers' . '?' . http_build_query($queryParams);
    }

}
