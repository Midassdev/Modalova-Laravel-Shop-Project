<?php

namespace App\Services\UpdateSources;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class Shareasale extends BaseSource
{
    // Note: API requests are limited to 200 per month.
    const API_VERSION                = 2.3;
    const API_URL                    = 'https://api.shareasale.com/x.cfm';
    const GET_DATA_FEEDS_INFO_ACTION = 'merchantDataFeeds';
    const FEEDS_INFO_FORMAT          = 'xml';
    const FEED_FILE_EXT              = 'zip';
    const FEED_FORMAT_LABEL          = 'csv';

    private $affiliateId;
    private $apiToken;
    private $apiSecretKey;
    private $ftpHost;
    private $ftpUser;
    private $ftpPass;

    protected $requiredEnvParams = [
        'SHAREASALE_AFFILIATE_ID',
        'SHAREASALE_API_TOKEN',
        'SHAREASALE_API_SECRET_KEY',
        'SHAREASALE_FTP_HOST',
        'SHAREASALE_FTP_USER',
        'SHAREASALE_FTP_PASS',
    ];

    public function __construct()
    {
        $this->affiliateId  = env('SHAREASALE_AFFILIATE_ID');
        $this->apiToken     = env('SHAREASALE_API_TOKEN');
        $this->apiSecretKey = env('SHAREASALE_API_SECRET_KEY');
        $this->ftpHost      = env('SHAREASALE_FTP_HOST');
        $this->ftpUser      = env('SHAREASALE_FTP_USER');
        $this->ftpPass      = env('SHAREASALE_FTP_PASS');
    }

    public function update()
    {
        if ($feeds = $this->getFeeds()) {
            collect($feeds)->each(function ($feed) {
                $this->update_source(
                    $feed['unique_name'],
                    $feed['title'],
                    $feed['path'],
                    $feed['raw'],
                    $feed['params']);
            });

            return true;
        }

        throw new \Exception("Failed to update sources of ShareASale");
    }

    public function getFeeds(): ?array
    {
        $feedsDetails      = [];
        $ftpDirs           = Storage::disk('share_a_sale')->directories();
        $ftpDirsCollection = collect($ftpDirs)->filter(function ($dirName) {
            return preg_match('/^\d+$/', $dirName);
        });

        // to avoid unnecessary requests to API due to the limit of requests per month
        $feedsInfo = $ftpDirsCollection->isNotEmpty() ? $this->getFeedsInfo() : [];
        if (is_null($feedsInfo)) {
            throw new \Exception("Failed to retrieve data feeds info of ShareASale by API.");
        }

        $ftpDirsCollection->each(function ($merchantId) use (&$feedsDetails, $feedsInfo) {
            $feedInfo = $feedsInfo[$merchantId];

            $feedsDetails[] = [
                'unique_name' => $this->getUniqueName($feedInfo),
                'title'       => $feedInfo['merchant'],
                'path'        => $this->buildFeedPath($merchantId),
                'raw'         => $feedInfo,
                'params'      => ['nb_of_products' => $feedInfo['numberofproducts']],
            ];
        });

        return $feedsDetails;
    }

    private function getUniqueName(array $feedInfo): string
    {
        return "${feedInfo['merchant']} (${feedInfo['merchantid']}) [" . self::FEED_FORMAT_LABEL . "]";
    }

    private function buildFeedPath(int $merchantId): string
    {
        return "ftp://$this->ftpUser:$this->ftpPass@$this->ftpHost/$merchantId/$merchantId." . self::FEED_FILE_EXT;
    }

    private function getFeedsInfo(): ?array
    {
        if (!$xmlObj = simplexml_load_string($feedsXmlString = $this->getFeedsInfoXml())) {
            throw new \Exception("Failed to interpret a string of XML into an object.");
        }

        $feeds = [];
        foreach ($xmlObj->datafeedlistreportrecord as $merchantFeed) {
            $feedInfo                       = (array) $merchantFeed;
            $feeds[$feedInfo['merchantid']] = $feedInfo;
        }

        return $feeds;
    }

    private function getFeedsInfoXml(): ?string
    {
        $headerDate = gmdate(DATE_RFC1123);
        $signature  = "$this->apiToken:$headerDate:" . self::GET_DATA_FEEDS_INFO_ACTION . ":$this->apiSecretKey";

        $response = Http::withHeaders([
            'x-ShareASale-Date'           => $headerDate,
            'x-ShareASale-Authentication' => hash("sha256", $signature),
        ])->get(self::API_URL, [
            'action'      => self::GET_DATA_FEEDS_INFO_ACTION,
            'affiliateId' => $this->affiliateId,
            'token'       => $this->apiToken,
            'version'     => self::API_VERSION,
            'format'      => self::FEEDS_INFO_FORMAT,
        ]);

        if ($response->ok()) {
            return $response->body();
        }

        throw new \Exception("Failed to retrieve feeds from ShareASale.");
    }
}
