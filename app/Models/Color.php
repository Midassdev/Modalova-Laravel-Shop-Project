<?php

namespace App\Models;

use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Color extends BaseModel
{
    use Cachable;

    public $guarded = [];

    public function __toString()
    {
        return $this->name;
    }

    public function getRouteKeyName()
    {
        return 'name';
    }

    public static function all_as_string($glue)
    {
        try {
            return implode($glue, self::all()->pluck('name')->toArray());
        } catch (\Exception $e) {
            return '';
        }
    }
}
