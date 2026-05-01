<?php

namespace App\Domains\Account\Data;

use App\Concerns\PasswordValidationRules;
use App\Concerns\ProfileValidationRules;
use App\Domains\Coupon\CouponClaimable;
use App\Domains\Coupon\CouponService;
use App\Infrastructure\Spam\CaptchaRulesFactory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Laravel\Fortify\Contracts\CreatesNewUsers;

class CreateNewUser implements CreatesNewUsers
{
    use PasswordValidationRules, ProfileValidationRules;

    public function __construct(
        public CouponService $couponService,
    ) {}

    /**
     * Validate and create a newly registered user.
     *
     * @param  array<string, string>  $input
     */
    public function create(array $input): User
    {
        Validator::make($input, [
            ...$this->profileRules(),
            'password' => $this->passwordRules(),
            'coupon' => ['nullable', 'string', new CouponClaimable],
            ...CaptchaRulesFactory::rules(),
        ])->validate();

        $couponId = trim((string) ($input['coupon'] ?? ''));

        return DB::transaction(function () use ($input, $couponId): User {
            $user = User::create([
                'name' => $input['name'],
                'email' => $input['email'],
                'password' => $input['password'],
            ]);

            if ($couponId === '') {
                return $user;
            }

            $this->couponService->claim($couponId, $user);

            return $user;
        });
    }
}
