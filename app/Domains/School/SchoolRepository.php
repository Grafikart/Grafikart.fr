<?php

namespace App\Domains\School;

use App\Domains\Coupon\Coupon;
use App\Domains\History\Progress;
use App\Domains\School\Data\SchoolStudentData;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class SchoolRepository
{

    public function countPending(int $schoolId): int
    {
        return Coupon::query()
            ->notClaimed()
            ->where('school_id', $schoolId)
            ->count();
    }

    /**
     * @return LengthAwarePaginator<SchoolStudentData>
     */
    public function activeStudents(int $schoolId): LengthAwarePaginator
    {
        $coupons = Coupon::query()
            ->with('user')
            ->whereNotNull('user_id')
            ->where('school_id', $schoolId)
            ->claimed()
            ->paginate(20);

        $completedCoursesCount = Progress::query()
            ->completed()
            ->where('progressable_type', 'course')
            ->whereIn('user_id', $coupons->getCollection()->pluck('user_id'))
            ->select('user_id')
            ->selectRaw('count(*) as completions')
            ->groupBy('user_id')
            ->pluck('completions', 'user_id');

        $coupons->setCollection(
            $coupons->getCollection()->map(
                fn (Coupon $coupon): SchoolStudentData => new SchoolStudentData(
                    email: $coupon->user->email,
                    createdAt: $coupon->user->created_at,
                    endAt: $coupon->user->premium_end_at,
                    completions: (int) ($completedCoursesCount[$coupon->user_id] ?? 0),
                )
            )
        );

        /** @var LengthAwarePaginator<SchoolStudentData> $coupons */
        return $coupons;
    }

    /**
     * @return Collection<Coupon>
     */
    public function pendingCoupons(int $schoolId): Collection
    {
        return Coupon::query()
            ->latest()
            ->where('school_id', $schoolId)
            ->notClaimed()
            ->limit(20)
            ->get();
    }
}
