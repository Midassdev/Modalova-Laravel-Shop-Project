<?php

declare(strict_types=1);

namespace App\Services\UpdateSources;

class Paidonresults extends BaseSource
{
    protected $requiredEnvParams = [
        'PAIDONRESULTS_DATAFEEDS_CSV',
    ];

    private $dataFeedsFileUrl;

    public function __construct()
    {
        $this->dataFeedsFileUrl = env('PAIDONRESULTS_DATAFEEDS_CSV');
    }

    public function update()
    {
        $dataFeedsInfo = $this->readDataFeedsFromCsv($this->dataFeedsFileUrl);

        foreach ($dataFeedsInfo as $dataFeedInfo) {
            $this->update_source(
                $this->getUniqueName($dataFeedInfo),
                $dataFeedInfo['MerchantName'],
                $dataFeedInfo['FullProductFeedURL'],
                $dataFeedInfo
            );
        }
    }

    private function readDataFeedsFromCsv(string $fileUrl): array
    {
        $csvData = array_map('str_getcsv', file($fileUrl));
        array_walk($csvData, function (&$item) use ($csvData) {
            $item = array_combine($csvData[0], $item);
        });
        array_shift($csvData); // remove column header

        return $csvData;
    }

    private function getUniqueName(array $feedInfo): string
    {
        return "paidonresults - {$feedInfo['MerchantName']}";
    }
}
