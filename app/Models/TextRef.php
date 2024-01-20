<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class TextRef extends BaseModel
{
    const CACHING_TIME_ALL = 10080; // 1 week in minutes

    use Cachable;

    public $guarded = [];
}
