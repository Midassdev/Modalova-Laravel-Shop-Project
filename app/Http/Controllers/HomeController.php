<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Http;

class HomeController extends BaseController
{
    public function index()
    {
        if ($q = Request::get('q')) {
            return redirect()->route('get.products.search', ['q' => $q]);
        }

        \GoogleTagManager::set('pageType', 'home');

        $blog_articles = [];
        if(config('features.enable_blog')) {
            $blog_articles = Http::get(
                config('app.url') .
                '/zine/wp-json/wp/v2/posts?' .
                '_fields=id,date,slug,link,title,excerpt,_links.wp:featuredmedia,_embedded.wp:featuredmedia&_embed&per_page=5' .
                '&source=' . config('app.heroku_app_name')
            )->json();
        }

        return $this->handle('pages.shop.home', compact('blog_articles'));
    }

    public function opensearch() {
        return response()->view('xml.opensearch')->withHeaders([
            'Content-Type' => 'text/xml',
        ]);
    }
}
