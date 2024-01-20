<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class GoogleProductCategory extends BaseModel
{
    use Cachable;

    public $guarded = [];
}
