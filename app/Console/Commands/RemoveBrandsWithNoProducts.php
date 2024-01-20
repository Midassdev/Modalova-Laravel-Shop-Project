<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Brand;
use App\Libraries\ElasticsearchHelper;
use App\Models\ProductFromElasticsearch;

class RemoveBrandsWithNoProducts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'brands:remove-when-no-products {--all : Remove all Brands (not only the ones in listing.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove brands with no products';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $brands = Brand::where('in_listing', true)->get();
        if($this->option('all')) {
            $brands = Brand::all();
        }

        $max = $brands->count();

        $nb_removed = 0;
        $i = -1;
        foreach($brands as $brand) {
            $i++;
            echo "\r";
            echo str_pad("[$i/$max] ", 15);
            echo str_pad("ID: {$brand->id} | BRAND: {$brand->slug}", 60);
            echo "\t" . ' | IN_LISTING: ' . ($brand->in_listing ? 1 : 0) .' | IS_TOP: ' . ($brand->is_top ? 1 : 0) . ' | ';

            $query_params = ElasticsearchHelper::buildQuery([
                'brand' => $brand,
                'category' => null,
            ]);

            try {
                $products = ProductFromElasticsearch::all($query_params);
                echo "TOTAL: " . str_pad($products['response']['total']['value'], 5);
            } catch (\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e) {
                $nb_removed++;
                echo 'TOTAL: 0 | REMOVING: ' . ($brand->delete()) . "\tRemoved: #{$nb_removed} ($brand->name)\n";
            }
        }

        echo "\n";

        return 0;
    }
}
