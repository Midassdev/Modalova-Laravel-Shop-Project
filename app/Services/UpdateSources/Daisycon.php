<?php

namespace App\Services\UpdateSources;

use Illuminate\Support\Facades\Http;

class Daisycon extends BaseSource
{
    const API_ENDPOINT = 'https://services.daisycon.com';

    protected $requiredEnvParams = [
        'DAISYCON_USER',
        'DAISYCON_PASS',
        'DAISYCON_PUBLISHER_ID',
        'DAISYCON_MEDIA_ID',
    ];

    private $httpClient;
    private $publisherId;
    private $mediaId;
    private $prevStandard = []; // keep prev value to avoid extra queries where it's possible, that's often case

    public function __construct()
    {
        $this->publisherId = env('DAISYCON_PUBLISHER_ID');
        $this->mediaId     = env('DAISYCON_MEDIA_ID');
        $this->httpClient  = Http::withBasicAuth(env('DAISYCON_USER'), env('DAISYCON_PASS'))
            ->acceptJson()
            ->baseUrl(self::API_ENDPOINT);
    }

    public function update()
    {
        $page    = 1;
        $perPage = 1000;
        while ($feeds = $this->getFeeds($page, $perPage)) {
            foreach ($feeds as $feed) {
                $program = $this->getProgram($feed['program_id']);

                if ($program['status'] !== 'active') {
                    continue;
                }

                // set type to csv, xml by default
                $csvLink = $feed['url'] . '&type=csv';

                if (substr($csvLink, 0, 2) === '//') {
                    $csvLink = 'https:' . $csvLink;
                }

                $title = preg_replace('/ \([A-Z]{2,3}\)$/', '', $program['name']);

                $this->update_source(
                    $this->getUniqueName($program, $feed),
                    $title,
                    $csvLink,
                    $feed,
                    [
                        'language'       => $feed['language_code'],
                        'nb_of_products' => $feed['products'],
                    ]
                );
            }

            if (count($feeds) < $perPage) {
                break;
            }

            $page++;
        }
    }

    private function getFeeds(int $page, int $perPage): ?array
    {
        $response = $this->httpClient->get("publishers/{$this->publisherId}/productfeeds.v2/program", [
            'media_id'             => $this->mediaId,
            'placeholder_media_id' => $this->mediaId,
            'page'                 => $page,
            'per_page'             => $perPage, // max 1000
        ]);

        if (!$response->ok()) {
            throw new \Exception("Failed to retrieve feeds from Daisycon.");
        }

        return $response->json();
    }

    private function getProgram(int $programId): ?array
    {
        $response = $this->httpClient->get("publishers/{$this->publisherId}/programs/{$programId}");

        if (!$response->ok()) {
            throw new \Exception("Failed to retrieve program by id from Daisycon.");
        }

        return $response->json();
    }

    private function getUniqueName(array $program, array $feedInfo): string
    {
        $standard = $this->getStandard($feedInfo['standard_id']);

        return "{$program['name']} - {$feedInfo['currency_code']} - {$standard['name']}";
    }

    private function getStandard(int $standardId): ?array
    {
        if (!empty($this->prevStandard) && $this->prevStandard['id'] === $standardId) {
            return $this->prevStandard;
        }

        $response = $this->httpClient->get("publishers/{$this->publisherId}/productfeeds.v2/standard/{$standardId}");

        if (!$response->ok()) {
            throw new \Exception("Failed to retrieve standard by id from Daisycon.");
        }

        return $this->prevStandard = $response->json();
    }

}
