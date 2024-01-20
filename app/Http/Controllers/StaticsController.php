<?php

namespace App\Http\Controllers;

class StaticsController extends BaseController
{
    public function getFAQ()
    {
        \GoogleTagManager::set('pageType', 'faq');

        return $this->handle('pages.statics.faq', []);
    }

    public function getLegals()
    {
        \GoogleTagManager::set('pageType', 'legals');

        return $this->handle('pages.statics.legals', []);
    }

    public function getAbout()
    {
        \GoogleTagManager::set('pageType', 'about');

        return $this->handle('pages.statics.about', []);
    }
}
