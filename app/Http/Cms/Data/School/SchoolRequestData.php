<?php

namespace App\Http\Cms\Data\School;

use App\Domains\Cms\DataToModel;
use App\Domains\School\School;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;
use Spatie\LaravelData\Data;

final class SchoolRequestData extends Data implements DataToModel
{
    public function __construct(
        public readonly string $name,
        public readonly int $userId,
        public readonly string $couponPrefix,
        public readonly int $credits,
        public readonly string $emailSubject,
        public readonly string $emailMessage,
    ) {}

    public static function rules(): array
    {
        /** @var School|null $school */
        $school = request()->route('school');

        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'userId' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::unique('schools', 'user_id')->ignore($school),
            ],
            'couponPrefix' => ['nullable', 'string', 'max:255'],
            'credits' => ['required', 'integer', 'min:0'],
            'emailSubject' => ['nullable', 'string'],
            'emailMessage' => ['nullable', 'string'],
        ];
    }

    public static function prepareForPipeline(array $properties): array
    {
        $properties['couponPrefix'] = trim((string) ($properties['couponPrefix'] ?? ''));
        $properties['emailSubject'] = trim((string) ($properties['emailSubject'] ?? ''));
        $properties['emailMessage'] = trim((string) ($properties['emailMessage'] ?? ''));

        return $properties;
    }

    public function toModel(Model $model): Model
    {
        assert($model instanceof School);

        return $model->fill([
            'name' => $this->name,
            'user_id' => $this->userId,
            'coupon_prefix' => $this->couponPrefix === '' ? null : $this->couponPrefix,
            'credits' => $this->credits,
            'email_subject' => $this->emailSubject === '' ? null : $this->emailSubject,
            'email_message' => $this->emailMessage === '' ? null : $this->emailMessage,
        ]);
    }
}
