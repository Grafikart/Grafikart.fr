<?php

namespace App\Domains\School;

use App\Domains\Coupon\Coupon;
use App\Domains\Coupon\Event\CouponCreatedEvent;
use App\Domains\School\Data\SchoolImportRow;
use App\Domains\School\Data\SchoolPreprocessResult;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SchoolImportService
{
    /**
     * @param SchoolImportRow[] $rows
     */
    public function import(School $school, array $rows, string $subject, string $message)
    {
        DB::beginTransaction();
        /** @var Coupon[] $coupons */
        $coupons = [];
        foreach ($rows as $row) {
            $prefix = $school->coupon_prefix ?? date('Y');
            $coupons[] = Coupon::create([
                'school_id' => $school->id,
                'email' => $row->email,
                'months' => $row->months,
                'id' => sprintf('%s_%s', $prefix, Str::random(8))
            ]);
        }

        // Update school credits
        $months =  array_sum(array_map(fn (SchoolImportRow $row) => $row->months, $rows));
        $school->update([
            'email_subject' => $subject,
            'email_message' => $message,
            'credits' => $school->credits - $months
        ]);

        DB::commit();

        // Dispatch the events
        foreach ($coupons as $coupon) {
            event(new CouponCreatedEvent($coupon));
        }
    }
}
