<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class SalesPeriod extends BaseModel
{
    use Cachable;

    public $guarded = [];

  protected $dates = [
    'starts_at',
    'ends_at',
  ];

  public function __toString() {
    return $this->name;
  }

  public function is_active($now = null) {
    $now = $now ?: Carbon::now();
    return $now->between($this->starts_at, $this->ends_at);
  }

  protected static function boot() {
    parent::boot();
    static::addGlobalScope('order', function (Builder $builder) {
      $builder->orderBy('starts_at', 'asc');
    });
  }
}
