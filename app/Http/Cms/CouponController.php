<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\Coupon\Coupon;
use App\Http\Cms\Data\Coupon\CouponFormData;
use App\Http\Cms\Data\Coupon\CouponRequestData;
use App\Http\Cms\Data\Coupon\CouponRowData;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;

final class CouponController extends CmsController
{
    protected string $componentPath = 'coupons';

    protected string $model = Coupon::class;

    protected string $rowData = CouponRowData::class;

    protected string $formData = CouponFormData::class;

    protected string $requestData = CouponRequestData::class;

    protected string $route = 'coupons';

    public function index(): Response
    {
        $query = Coupon::query()->orderByDesc('created_at');

        return $this->cmsIndex(query: $query);
    }

    public function create(): Response
    {
        return $this->cmsCreate();
    }

    public function store(CouponRequestData $data): RedirectResponse
    {
        return $this->cmsStore($data);
    }

    public function edit(Coupon $coupon): Response
    {
        return $this->cmsEdit($coupon);
    }

    public function update(Coupon $coupon, CouponRequestData $data): RedirectResponse
    {
        return $this->cmsUpdate(model: $coupon, data: $data);
    }

    public function destroy(Coupon $coupon): RedirectResponse
    {
        return $this->cmsDestroy($coupon, "Le coupon {$coupon->id} a été supprimé");
    }
}
