<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class UpdateSources extends Command
{
    protected $signature = 'update:sources {provider?}';

    protected $description = 'Update Sources from all providers';

    public static $all_providers = [
        'awin',
        'cj',
        'partnerize',
        'netaffiliation',
        'tradedoubler',
        'rakuten',
        'tradetracker',
        'daisycon',
        'effiliation',
        'flexoffers',
        // 'modalova',
        'impact',
        'affilae',
        'shareasale',
        'paidonresults',
        'admitad',
        'kelkoo',
    ];

    public function handle()
    {
        $providers = self::$all_providers;

        if ($opts_provider = $this->argument('provider')) {
            $providers = [$opts_provider];
        }

        foreach ($providers as $provider) {
            $className = '\App\Services\UpdateSources\\'.ucfirst($provider);
            try {
                (new $className)();
            } catch (\Exception $e) {
                error_log($e->getMessage());
                app('sentry')->captureException($e);
            }
        }
    }
}
