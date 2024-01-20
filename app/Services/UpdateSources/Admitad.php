<?php

declare(strict_types=1);

namespace App\Services\UpdateSources;

use Illuminate\Support\Facades\Http;

class Admitad extends BaseSource
{
    const API_ENDPOINT = 'https://api.admitad.com';

    private $clientId;
    private $clientSecret;
    private $adSpaceId;
    private $accessToken;

    protected $requiredEnvParams = [
        'ADMITAD_CLIENT_ID',
        'ADMITAD_CLIENT_SECRET',
        'ADMITAD_AD_SPACE_ID',
    ];

    public function __construct()
    {
        $this->clientId     = env('ADMITAD_CLIENT_ID');
        $this->clientSecret = env('ADMITAD_CLIENT_SECRET');
        $this->adSpaceId    = env('ADMITAD_AD_SPACE_ID');
    }

    public function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $response = Http::baseUrl(self::API_ENDPOINT)
            ->withBasicAuth($this->clientId, $this->clientSecret)
            ->asForm()
            ->post('token/', [
                'grant_type' => 'client_credentials',
                'client_id'  => $this->clientId,
                'scope'      => 'advcampaigns_for_website',
            ]);

        if (!$response->ok()) {
            throw new \Exception("Failed to retrieve access token from Admitad.");
        }

        return $this->accessToken = $response->json('access_token');
    }

    public function update()
    {
        $programs = $this->getAffiliatePrograms();

        foreach ($programs as $program) {
            foreach ($program['feeds_info'] as $feedInfo) {
                $this->update_source(
                    $this->getUniqueName($program, $feedInfo),
                    $program['name'],
                    $feedInfo['csv_link'],
                    $program
                );
            }
        }
    }

    private function getAffiliatePrograms(): array
    {
        $response = Http::baseUrl(self::API_ENDPOINT)
            ->withToken($this->getAccessToken())
            ->get("advcampaigns/website/$this->adSpaceId/", [
                'connection_status' => 'active',
                'limit'             => 100,
                'language'          => 'fr',
            ]);

        if (!$response->ok()) {
            throw new \Exception("Failed to retrieve programs from Admitad.");
        }

        return $response->json('results');
    }

    private function getUniqueName(array $program, array $feedInfo): string
    {
        return "{$program['name']} - {$feedInfo['name']}";
    }

}
