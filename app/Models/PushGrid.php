<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\SoftDeletes;

class PushGrid extends BaseModel
{
    use Cachable;
    use SoftDeletes;
}
