<?php

namespace App\Http\Cms\Data\Premium;

use App\Domains\Cms\DataToModel;
use App\Domains\Premium\Models\Plan;
use Illuminate\Database\Eloquent\Model;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;
use Spatie\TypeScriptTransformer\Attributes\TypeScript;

#[TypeScript]
class PlanData extends Data implements DataToModel
{
    public function __construct(
        public readonly ?int   $id = null,
        #[Required]
        #[Min(2)]
        public readonly string $name = '',
        #[Required]
        public readonly int    $price = 0,
        #[Required]
        public readonly int    $duration = 0,
        #[Required]
        #[Min(2)]
        public readonly string $stripeId = '',
    ) {}

    public function toModel(Model $model): Model
    {
        assert($model instanceof Plan);

        return $model->fill([
            'name' => $this->name,
            'price' => $this->price,
            'duration' => $this->duration,
            'stripe_id' => $this->stripeId,
        ]);
    }
}
