<?php

namespace App\Providers;

use Illuminate\Routing\Router;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

use Route;
use App\Models\Brand;
use App\Models\BrandType;
use App\Models\Color;
use App\Models\Category;
use App\Models\Gender;
use App\Exceptions\BrandNotFoundException;
use App\Exceptions\CategoryNotFoundException;
use App\Exceptions\OldGenderDetectedException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class RouteServiceProvider extends ServiceProvider {

  /**
   * This namespace is applied to the controller routes in your routes file.
   *
   * In addition, it is set as the URL generator's root namespace.
   *
   * @var string
   */
  protected $namespace = 'App\Http\Controllers';

  public function boot() {
    $this->configurePatterns();

    if(!$this->app->request->is(config('admin.route.prefix').'*'))
      $this->configureBindings();

    Route::namespace($this->namespace)->group(app_path('Http/routes.php'));

    parent::boot();
  }

  private function configurePatterns()
  {
    Route::pattern('brand', '^[a-zA-Z0-9_-]+$');
    Route::pattern('category', '^[a-zA-Z0-9_-]+$');
    Route::pattern('product', '^.+$');
    Route::pattern('color_pattern', '^(' . Color::all_as_string('|') . ')$');
    Route::pattern('page', '^[0-9]+$');

    $allowed_genders = [
      Gender::GENDER_MALE(),
      Gender::GENDER_FEMALE()
    ];

    if('de_DE' == config('app.locale')) {
      $allowed_genders = [...$allowed_genders, ...OldGenderDetectedException::OLD_GENDERS_FOR_DE];
    }

    Route::pattern('gender', '^(' . implode('|', $allowed_genders) . ')$');
  }

  private function configureBindings()
  {
    Route::model('brand_type', BrandType::class);
    Route::model('color', Color::class);

    Route::model('brand', Brand::class, function($value) {
      $e = new ModelNotFoundException;

      if(false !== strpos(\Request::route()->getName(), '.byBrand')) {
        $e = (new BrandNotFoundException);
        $e->slug = $value;
      }

      throw $e->setModel(Brand::class);
    });

    Route::model('category', Category::class, function($value) {
      $route_name = \Request::route()->getName();
      $e = new ModelNotFoundException;

      if('get.products.byCategory' == $route_name) {
        $e = (new CategoryNotFoundException);
        $e->slug = $value;
      } elseif('get.products.byBrand.byCategory' == $route_name) {
        $e = (new CategoryNotFoundException);
        $e->slug = $value;
        $e->brand = \Request::route()->parameter('brand');
      }

      throw $e->setModel(Category::class);
    });

    Route::bind('gender', function ($value) {
      if(in_array($value, OldGenderDetectedException::OLD_GENDERS_FOR_DE)) {
        $e = new OldGenderDetectedException();
        $e->old_gender = $value;
        throw $e;
      }

      return Gender::gender_from_string($value);
    });
  }

}
