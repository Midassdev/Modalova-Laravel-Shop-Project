<?php

declare(strict_types=1);

namespace App\Services\UpdateSources;

use App\Models\Source;
use Illuminate\Support\Facades\Http;

class Affilae extends BaseSource
{
    const API_ENDPOINT = 'https://rest.affilae.com';

    protected $requiredEnvParams = [
        'AFFILAE_ACCESS_TOKEN',
    ];

    private $httpClient;
    private $affiliateProfileId;

    public function __construct()
    {
        $this->httpClient  = Http::withToken(env('AFFILAE_ACCESS_TOKEN'))
            ->acceptJson()
            ->baseUrl(self::API_ENDPOINT);
    }

    private function getAffiliateProfileId(): ?string
    {
        $response = $this->httpClient->get('publisher/affiliateProfiles.list');

        if (!$response->ok())
            throw new \Exception("Failed to retrieve affiliate profile id from Affilae.");

        return $response->json('affiliateProfiles.data.0.id');
    }

    private function getPrograms(): array
    {
        $programs = [];

        $response = $this->httpClient->get('publisher/partnerships.list', [
            'affiliateProfile' => $this->affiliateProfileId,
        ]);

        if (!$response->ok()) {
            throw new \Exception("Failed to retrieve programs from Affilae.");
        }

        $partnerships = $response->json('partnerships.data');

        foreach ($partnerships as $partnership) {
            $programs[$partnership['program']['id']] = $partnership;
        }

        return $programs;
    }

    private function getFeeds(): ?array
    {
        $response = $this->httpClient->get('publisher/feeds.list', [
            'affiliateProfile' => $this->affiliateProfileId,
        ]);

        if (!$response->ok()) {
            throw new \Exception("Failed to retrieve feeds from Affilae.");
        }

        return $response->json();
    }

    public function update()
    {
        $this->affiliateProfileId = $this->getAffiliateProfileId();

        $programs = $this->getPrograms();
        $feeds    = $this->getFeeds();

        foreach ($feeds as $feed) {
            $program = $programs[$feed['program']];
            $title   = $program['program']['name'];
            $name    = $title . ' - ' . $feed['title'];
            $url     = $feed['urlTracked'];

            $need_to_transform_url = ('N/A' == $feed['urlTracked'] || 'N/A' == $feed['format']);

            if ($need_to_transform_url) {
                $name .= ' (untracked)';
                $url = $feed['url'];
            }

            $source = $this->update_source(
                $name,
                $title,
                $url,
                [
                    'feed'    => $feed,
                    'program' => $program,
                ]
            );

            if ($need_to_transform_url) {
                $source->config = array_merge($source->config ?: [], [
                    Source::CONFIG_TRANSFORM_URL => '{url}#ae' . $program['trackingId'],
                ]);
            }

            if (true != $feed['isActive'] || 'active' != $program['status']) {
                $source->enabled = false;
            }

            $source->save();
        }
    }
}
