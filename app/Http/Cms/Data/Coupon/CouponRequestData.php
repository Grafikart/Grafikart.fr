<?php

namespace App\Http\Cms\Data\Coupon;

use App\Domains\Cms\DataToModel;
use App\Domains\Coupon\Coupon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

final class CouponRequestData extends Data implements DataToModel
{
    public function __construct(
        public readonly string $id,
        public readonly int $months,
    ) {}

    public static function rules(): array
    {
        /** @var Coupon|null $coupon */
        $coupon = request()->route('coupon');

        return [
            'id' => [
                'required',
                'string',
                'min:2',
                'max:255',
                Rule::unique('coupons', 'id')->ignore($coupon?->getKey(), 'id'),
            ],
            'months' => ['required', 'integer', 'min:1'],
        ];
    }

    public static function prepareForPipeline(array $properties): array
    {
        $properties['id'] = trim((string) ($properties['id'] ?? ''));

        return $properties;
    }

    public function toModel(Model $model): Model
    {
        assert($model instanceof Coupon);

        if (! $model->exists) {
            $model->email = '';
        }

        return $model->fill([
            'id' => $this->id,
            'months' => $this->months,
        ]);
    }
}
