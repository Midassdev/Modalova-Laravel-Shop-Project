<?php

namespace App\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class BrandType extends BaseModel implements Sortable
{
    use Cachable;
    use Sluggable;
    use SortableTrait;

    public function sluggable(): array
    {
        return ['slug' => ['source' => 'name']];
    }

    public function __toString()
    {
        return $this->name;
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function brands()
    {
        return $this->hasMany(\App\Models\Brand::class);
    }
}
