<?php

namespace App\Http\Controllers;

use App\Libraries\ElasticsearchHelper;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFromElasticsearch;
use App\Models\TextRef;
use App\Models\WhiteList;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;
use Spatie\Feed\Feed;
use Spatie\Feed\Helpers\ResolveFeedItems;

class ProductsController extends BaseController
{
	const NB_OF_SIMILARS = 12;
	const PRODUCTS_BY_PAGE = 70;
	const PRODUCTS_ON_FIRST_PAGE = 5; // for lazy loading
	const NB_FACETS_MAX = 100;

	public function getSearch()
	{
		$query = trim(Request::get('q'));

		if(preg_match("/\p{Han}+/u", $query) || false !== stripos($query, '.com'))
			abort(410);

		$params = [
			'query' => $query,
		];

		if (empty($query)) {
			return redirect()->route('get.products.all_products');
		}

		if ($_brand = trim(Request::get('brand'))) {
			$brand = Brand::where('slug', $_brand)->first();

			if($brand) {
				$params['brand'] = $brand;
			}
		} else {
			if ($brand = Brand::where('name', 'ilike', $query)
				->orWhere('display_name', 'ilike', $query)
				->orWhere('slug', 'ilike', $query)
				->orderBy('is_top', 'desc')->orderBy('in_listing', 'desc')
				->first()) {
				return redirect()->route('get.products.byBrand', $brand);
			}

			if ($category = Category::where('title', 'ilike', $query)
				->orWhere('slug', 'ilike', $query)
				->first()) {
				return redirect()->route('get.products.byCategory', $category);
			}
		}

		return $this->getProducts('pages.shop.grid.search', $params, true);
	}

	public function getGrid()
	{
		$template = null;
		$arguments = func_get_args();

		$parts = explode('.', strtolower(\Request::route()->getName()));
		array_shift($parts);
		array_shift($parts);

		foreach (array_map(function ($v) {
			return substr($v, 2);
		}, $parts) as $part) {
			if ('promotion' == $part) {
				$argument = true;
			} else {
				$argument = array_shift($arguments);
			}

			$params[$part] = $argument;

			if (null == $template && !in_array($part, ['gender', 'color'])) {
				$template = 'pages.shop.grid.'.$part;
			}
		}

		$template = $template ?: 'pages.shop.grid.grid';

		return $this->getProducts($template, $params);
	}

	public function getProducts($template = 'pages.shop.grid.grid', $params = [], $is_search_page = false)
	{
		if ($category = @$params['category']) {
			if ($category->isRoot()) {
				abort(404);
			}
		}

		$route = \Request::route()->getName();

		$facetsConfiguration = [
			'price' => 'price',
			'gender' => 'gender',
			'colors' => 'color',
			'brand_original' => 'brand_original',
			'merchant_original' => 'merchant_original',
			'sizes' => 'size',
			'materials' => 'material',
			// 'promotion' => 'promotion',
			'motifss' => 'motifs',
			'styles' => 'style',
			'coupes' => 'coupe',
			'events' => 'event',
			'cols' => 'col',
			'manchess' => 'manches',
			// 'models' => 'model',
			// 'livraison' => 'livraison',
		];

		$current_route_params = \Route::current()->parameters();
		if(isset($current_route_params['gender']))
			$current_route_params['gender'] = _i($current_route_params['gender']);

		$page = LengthAwarePaginator::resolveCurrentPage('page', 1);
		if($page > 100)
			abort(422);

		$query_params = ElasticsearchHelper::buildQuery($params, $page, $facetsConfiguration);
		$current_url_canonical = route(
			\Route::currentRouteName(),
			$is_search_page ? ['q' => $params['query']] : $current_route_params
		);

		try {
			$products = ProductFromElasticsearch::all($query_params);
		}
		catch(\Symfony\Component\HttpKernel\Exception\NotFoundHttpException $e)
		{
			if ('get.products.byBrand' == $route && empty(\Request::all())) {
				$brand = $params['brand'];

				if(true == config('features.remove_brands_if_no_products_even_if_public') || !$brand->isPublic())
					$brand->delete();
			}

			if(@$params['color']) {
				unset($params['color']);
				return redirect(get_magic_route($params));
			}

			if(@$params['gender']) {
				unset($params['gender']);
				return redirect(get_magic_route($params));
			}

			if($category = @$params['category']) {
				if(1 < $category->depth) {
					$params['category'] = $category->parent;
				} else {
					unset($params['category']);
				}

				return redirect(get_magic_route($params));
			}

			abort(404, _i('Aucun produit trouvé'));
		}

		if ($brand = @$params['brand']) {
			if ($brand->in_listing && ! WhiteList::where('brand_id', $brand->id)->exists()) {
				\App\Jobs\UpdateWhiteList::dispatch($brand);
			} else {
				$brand->in_listing = true;
				$brand->save();
			}
		}

		$total = min(ProductFromElasticsearch::MAX_PRODUCTS_BY_REQUEST, $products['response']['total']['value']);
		$facets = $products['response']['facets'];
		$aggs = $products['response']['aggs'];

		$key_facetsConfiguration = array_keys($facetsConfiguration);

		uksort($facets, function ($a, $b) use ($key_facetsConfiguration) {
			$ka = array_search($a, $key_facetsConfiguration);
			$kb = array_search($b, $key_facetsConfiguration);

			if ($ka == $kb) {
				return 0;
			}

			return ($ka < $kb) ? -1 : 1;
		});

		$products_paginated = new LengthAwarePaginator(
			$products,
			min($total, 70 * self::PRODUCTS_BY_PAGE),
			self::PRODUCTS_BY_PAGE,
			$page
		);
		$products_paginated->setPath($current_url_canonical);

		if(!$is_search_page) {
			$ref_text = TextRef::where('url', ltrim(\Request::getRequestUri(), '/'))->first();
		}

		$white_list = false;
		if(@$params['brand'])
			$white_list = WhiteList::getWhiteList(@$params['gender'], @$params['brand'], @$params['category'], @$params['color']);

		$urls = [];
		$urls['prev'] = $products_paginated->previousPageUrl();
		$urls['next'] = $products_paginated->nextPageUrl();
		$urls['canonical'] = $current_url_canonical;

		if(@$params['category']->allow_indexing_of_paginated_pages)
			$urls['canonical'] = $products_paginated->url($page);

		if(1 == $page) {
			$urls['canonical'] = $current_url_canonical;
		}
		if(2 == $page) {
			$urls['prev'] = $current_url_canonical;
		}

		$params = array_merge([
			'urls' => $urls,
			'aggs' => $aggs,
			'facets' => $facets,
			'facetsConfiguration' => $facetsConfiguration,
			'products' => $products,
			'products_paginated' => $products_paginated,
			'ref_text' => @$ref_text,
			'white_list' => $white_list,
			'category_title' => @$params['category']->title,
			'hitsOnFirstPage' => self::PRODUCTS_ON_FIRST_PAGE,
			'route' => $route,

			'query_params' => $query_params,
		], $params);

		\GoogleTagManager::set('pageType', 'grid');

		if($format = \Request::get('format')) {
			if(!in_array($format, ['rss', 'google_merchant']))
				abort(400);

			$feed = [
				'items' => [
          'App\Models\ProductFromElasticsearch@allFor',
          'gender' => @$params['gender'],
          'category' => @$params['category'],
          'brand_original' => @$params['brand_original'],
        ],
      ];

      $name = Str::after(app('router')->currentRouteName(), 'feeds.');
      $items = ResolveFeedItems::resolve($name, $feed['items']);

      return new Feed(
          ucwords(get_human_readable_url(request()->path())),
          $items,
          request()->fullUrl(),
          'feed::' . $format,
          $feed['description'] ?? '',
          str_replace('_', '-', env('LOCALE')),
          $feed['image'] ?? '',
          $format,
      );
		}

		return $this->handle(
			$template,
			$params
		);
	}

	public function getProduct($product) {
		return $this->__getProduct($product, 'pages.shop.products.product');
	}

	public function getProductRedirect($product) {
		return $this->__getProduct($product, 'pages.shop.products.redirect');
	}

	private function __getProduct($product, $template) {
		try {
			$product = Product::findOrFail($product);
		} catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
			try {
				$product = ProductFromElasticsearch::get($product);
				\Log::info("Had to fetch product ({$product->slug}) from ES instead of DB");
			} catch (\Exception $e) {
				return redirect()->route('get.products.search', ['q' => $product]);
			}
		}

		$chunks = explode('.', $template);
		$page_type = end($chunks);

		$gtm_ecommerce_name = ('redirect' == $page_type) ? 'add' : 'detail';

		\GoogleTagManager::set('pageType', $page_type);
		\GoogleTagManager::set('ecommerce', [
			'currencyCode' => $product['currency_original'],
			$gtm_ecommerce_name => [
				'products' => [[
					'id' => $product['slug'],
					'name' => $product['name'],
					'slug' => $product['slug'],
					'price' => $product['price'],
					'gender' => $product['gender'],
					'brand' => $product['brand_original'],
				]],
			],
		]);

		return $this->handle($template, ['product' => $product]);
	}
}
