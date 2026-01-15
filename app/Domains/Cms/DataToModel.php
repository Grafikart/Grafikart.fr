<?php

namespace App\Domains\Cms;

use Illuminate\Database\Eloquent\Model;

/**
 * @template T of Model
 */
interface DataToModel
{
    /**
     * @param  T  $model
     * @return T
     */
    public function toModel(Model $model): Model;
}
