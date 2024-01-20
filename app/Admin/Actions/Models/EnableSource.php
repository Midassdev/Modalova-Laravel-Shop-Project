<?php

namespace App\Admin\Actions\Models;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class EnableSource extends BatchAction
{
    public $name = 'Batch enable';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
            $model->enabled = true;
            $model->save();
        }

        return $this->response()->success('Success message...')->refresh();
    }

}
