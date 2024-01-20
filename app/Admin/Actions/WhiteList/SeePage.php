<?php

namespace App\Admin\Actions\WhiteList;

use Encore\Admin\Actions\RowAction;
use Illuminate\Database\Eloquent\Model;

class SeePage extends RowAction
{
    public $name = 'SeePage';

    public function handle(Model $model)
    {
        return $this->response()->success('Success!')->open(
            $model->getRoute()
        );
    }
}
