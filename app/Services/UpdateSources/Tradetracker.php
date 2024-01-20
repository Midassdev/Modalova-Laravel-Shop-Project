<?php

namespace App\Services\UpdateSources;

use App\Models\Source;

class Tradetracker extends BaseSource
{
    protected $requiredEnvParams = [
        'TRADETRACKER_LOGIN',
        'TRADETRACKER_PASS',
        'TRADETRACKER_AID',
        'TRADETRACKER_CID',
    ];

    private $client;

    public function __construct()
    {
        $this->client = new \GuzzleHttp\Client(['cookies' => true]);
    }

    public function update()
    {
        $this->login();
        $campaigns = $this->get_campaigns();

        $csvLinkTemplate = str_replace(
            '%AID%',
            env('TRADETRACKER_AID'),
            'https://pf.tradetracker.net/?aid=%AID%&encoding=utf-8&type=csv&fid=%FID%&categoryType=2&additionalType=2&csvDelimiter=%3B&csvEnclosure=%22&filter_extended=1'
        );

        foreach ($campaigns as $campaign) {
            $fid = str_replace('#', '', $campaign->Product_feed_ID);
            $csvLink = str_replace('%FID%', $fid, $csvLinkTemplate);

            $this->update_source(
                $campaign->Campaign_Name.' - '.$campaign->Product_feed_Name,
                $campaign->Campaign_Name,
                $csvLink,
                ['campaign' => $campaign],
                ['nb_of_products' => intval(str_replace(',', '', $campaign->Product_feed_Products)),],
            );
        }
    }

    private function login()
    {
        $res = $this->client->get('https://affiliate.tradetracker.com/user/login');
        $content = $res->getBody()->getContents();

        preg_match('#name="__FORM" value="(.*?)"#', $content, $matches);
        $formToken = $matches[1];

        preg_match('#name="__SINGLESUBMIT" value="(.*?)"#', $content, $matches);
        $singleSubmit = $matches[1];

        if (empty($formToken) || empty($singleSubmit)) {
            throw new \Exception('[-] Cannot update Sources from TradeTracker: cannot parse parameters for authorization');
        }

        $res = $this->client->request(
            'POST',
            'https://affiliate.tradetracker.com/user/login', [
                'form_params' => [
                    'username' => env('TRADETRACKER_LOGIN'),
                    'password' => env('TRADETRACKER_PASS'),
                    'rememberMe' => 0,
                    'submitLogin' => 'Login',
                    'passwordReset' => 0,
                    'redirectURL' => '',
                    '__FORM' => $formToken,
                    '__SINGLESUBMIT' => $singleSubmit,
                ],
            ]
        );

        $res = $this->client->get('https://affiliate.tradetracker.com/?setCompanyID=' . env('TRADETRACKER_CID') . '&setCustomerSiteIDs=' . env('TRADETRACKER_AID'));
    }

    private function get_campaigns()
    {
        $rand = dechex((int) 99999999 * mt_rand(0, mt_getrandmax() - 1) / mt_getrandmax());

        $this->client->get('https://affiliate.tradetracker.com/affiliateMaterial/productFeed');

        $response = $this->client->request('GET',
             'https://affiliate.tradetracker.com/affiliateMaterial/productFeed?desc=&offset=0&outputType=2&generate=1&rand='.$rand,
             [
                'headers' => [
                    'Referer' => 'https://affiliate.tradetracker.com/affiliateMaterial/productFeed',
                    'Accept' => 'text/xml,*/*',
                    'X-Requested-With' => 'XMLHttpRequest',
                ],
            ]
        );

        $res = $this->client->get('https://affiliate.tradetracker.com/affiliateMaterial/productFeed?desc=&offset=0&outputType=2&generate=2&limit=500');

        $campaigns = simplexml_load_string($res->getBody()->getContents(), 'SimpleXMLElement', LIBXML_NOCDATA);

        return $campaigns;
    }
}
