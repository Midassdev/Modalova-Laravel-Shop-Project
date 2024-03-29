<?php

namespace App\Services\UpdateSources;

use App\Models\Parsers\Tradedoubler as ParserTradedoubler;
use App\Models\Source;

class Tradedoubler extends BaseSource
{
    protected $requiredEnvParams = [
        'TRADEDOUBLER_API_TOKENS',
    ];

    public function update()
    {
        $tokens = explode(',', env('TRADEDOUBLER_API_TOKENS'));

        foreach ($tokens as $token) {
            $this->_update($token);
        }
    }

    private function _update($token)
    {
        $client = new \GuzzleHttp\Client(['cookies' => true]);

        try {
            $res = $client->get("http://api.tradedoubler.com/1.0/productFeeds?token={$token}");
            $feeds = json_decode($res->getBody()->getContents());
        } catch (\Exception $e) {
            throw new \Exception('[-] Cannot get access feeds for Tradedoubler.'."\n\t".$e->getMessage());
        }

        if (empty($feeds) || empty($feeds->feeds)) {
            throw new \Exception('[-] Found no feeds for Tradedoubler');
        }

        // $csvSeparators = urlencode((new ParserTradedoubler)->col_sep.':;');
        $csvSeparators = urlencode('|:;');

        foreach ($feeds->feeds as $feed) {
            $this->update_source(
                $feed->name,
                $feed->name,
                "https://api.tradedoubler.com/1.0/productsUnlimited.csv;csvSeparators={$csvSeparators};csvEmbrace=%22;csvFlattenFields=true;fid={$feed->feedId}?token={$token}",
                $feed,
                [
                    'language' => $feed->languageISOCode,
                    'nb_of_products' => $feed->numberOfProducts,
                ],
            );
        }
    }
}
