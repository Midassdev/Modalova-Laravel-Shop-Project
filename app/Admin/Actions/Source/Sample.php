<?php

namespace App\Admin\Actions\Source;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class Sample extends RowAction
{
    public $name = 'Sample';

    public function handle(Model $model)
    {
        return $this->response()->success('Success!')->open(route(config('admin.route.prefix') . '.sources.sample', [$model]));
    }
}
