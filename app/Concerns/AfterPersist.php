<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Apply logic after the model is persisted
 */
trait AfterPersist
{
    public function afterPersist(Model $model, callable $cb)
    {
        if ($model->exists) {
            $cb($model);

            return;
        }
        $model::created(function (Model $m) use ($model, $cb) {
            if ($m->is($model)) {
                $cb($m);
            }
        });
    }
}
