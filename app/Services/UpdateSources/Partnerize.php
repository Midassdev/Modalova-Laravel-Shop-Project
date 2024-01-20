<?php

namespace App\Services\UpdateSources;

use App\Models\Source;

class Partnerize extends BaseSource
{
    protected $requiredEnvParams = [
        'PARTNERIZE_API_LOGIN',
        'PARTNERIZE_API_PASSWORD',
    ];

    public function update()
    {
        $client = new \GuzzleHttp\Client([
            'cookies' => true,
            'http_errors' => false,
            'headers' => [
                'Authorization' => 'Basic '.base64_encode(env('PARTNERIZE_API_LOGIN').':'.env('PARTNERIZE_API_PASSWORD')),
            ],
        ]);

        $res = $client->get('https://api.partnerize.com/user/account');
        $accountInfo = json_decode($res->getBody()->getContents());

        foreach ($accountInfo->user_accounts as $account) {
            $publisherId = $account->publisher->publisher_id;

            $res = $client->get("https://api.partnerize.com/user/publisher/{$publisherId}/feed");
            $campaigns = json_decode($res->getBody()->getContents());

            if (! $campaigns) {
                return error_log('[-] Could not get campaigns!' . print_r([
                    'contents' => $res->getBody()->getContents(),
                    'status' => $res->getStatusCode(),
                ], true));
            }

            foreach ($campaigns->campaigns as $campaign) {
                foreach ($campaign->campaign->feeds as $feed) {
                    $real_feed = $feed->feed;
                    $name = 'partnerize - '.$real_feed->title.' - '.$real_feed->name;
                    $url = $real_feed->location_compressed ?: $real_feed->location;

                    if ($real_feed->filesize <= 0) {
                        echo "[+] Feed is not ready ($url).\t→ Sending request: ";
                        $res = $client->get($real_feed->location);

                        if (404 == ($code = $res->getStatusCode())) {
                            echo 'done';
                        } else {
                            echo "weird, got ($code)";
                        }

                        echo " (not saving)\n";
                        continue;
                    }

                    $this->update_source(
                        "{$real_feed->title} - {$real_feed->name}",
                        $real_feed->name,
                        $url,
                        $real_feed,
                        ['nb_of_products' => -1],
                    );
                }
            }
        }
    }
}
