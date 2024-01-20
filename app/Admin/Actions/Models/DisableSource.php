<?php

namespace App\Admin\Actions\Models;

use Encore\Admin\Actions\BatchAction;
use Illuminate\Database\Eloquent\Collection;

class DisableSource extends BatchAction
{
    public $name = 'Batch disable';

    public function handle(Collection $collection)
    {
        foreach ($collection as $model) {
            $model->enabled = false;
            $model->save();
        }

        return $this->response()->success('Success message...')->refresh();
    }

}
