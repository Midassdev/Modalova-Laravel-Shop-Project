<?php

namespace App\Models;

use App\Libraries\ElasticsearchHelper;
use Log;

class ZeroProductException extends \Exception{}

class ProductFromElasticsearch
{
    const CACHING_TIME_ALL = 1440; // 60*24 ( 1 day )
    const MAX_PRODUCTS_BY_REQUEST = 3000;

    public static function getIndexName()
    {
        return 'products-' . strtolower(config('app.locale'));
    }

    public static function get($slug)
    {
        Log::info("ProductFromElasticsearch::get($slug)");

        $options = [
            'index' => self::getIndexName(),
            'id' => $slug,
        ];

        $get_results = ElasticsearchHelper::getClient()->get($options);

        if ($product = @$get_results['_source']) {
            return new Product($product);
        }

        throw new \Illuminate\Database\Eloquent\ModelNotFoundException();
    }

    public static function allFor($category, $gender, $brand_original = null)
    {
        if(is_string($category))
            $category = Category::where('slug', $category)->firstOrFail();

        $params = compact('category', 'gender', 'brand_original');

        $query_params = ElasticsearchHelper::buildQuery($params);
        $query_params['size'] = self::MAX_PRODUCTS_BY_REQUEST;

        return collect(array_map(function ($data) use ($category) {
            return (new Product($data))->toFeedItem($category);
        }, self::all($query_params)['response']['data']));
    }

    public static function all($query = [])
    {
        // Log::info("ProductFromElasticsearch::all()");

        $options = [
            'index' => self::getIndexName(),
            'request_cache' => true,
            'body' => array_merge([
                'query' => [],
            ], $query),
        ];

        if (extension_loaded('newrelic')) {
            newrelic_add_custom_tracer('ProductFromElasticsearch::all');
        }

        try {
            $search_results = ElasticsearchHelper::getClient()->search($options);
            $result = ElasticsearchHelper::responseAdapter($search_results);
        } catch (\Elasticsearch\Common\Exceptions\Missing404Exception $e) {
            app('sentry')->captureException($e);
            abort(503);
        }

        if (extension_loaded('newrelic')) {
            newrelic_end_transaction();
        }

        if (0 == @$result['response']['total']['value']) {
            throw new \Symfony\Component\HttpKernel\Exception\NotFoundHttpException();
        }

        return $result;
    }
}
