<?php

namespace App\Domains\Premium;

use App\Domains\Premium\Models\Transaction;
use App\Http\Cms\Data\Chart\DailyData;
use App\Http\Cms\Data\Chart\MonthlyData;
use Illuminate\Support\Facades\DB;

class TransactionRepository
{
    /**
     * @return array<DailyData>
     */
    public function getDailyRevenues(): array
    {
        $results = Transaction::query()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(price - tax - fee) / 100 as value')
            )
            ->whereNull('refunded_at')
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->toBase()
            ->get();

        return $results->map(
            /** @param object{date: string, value: int} $row */
            fn (object $row) => new DailyData(
                date: $row->date,
                value: (int) $row->value,
            )
        )->all();
    }

    /**
     * @return array<MonthlyData>
     */
    public function getMonthlyRevenues(): array
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            $monthExpr = DB::raw('EXTRACT(MONTH FROM created_at)::integer as month');
            $yearExpr = DB::raw('EXTRACT(YEAR FROM created_at)::integer as year');
        } else {
            $monthExpr = DB::raw("CAST(strftime('%m', created_at) AS INTEGER) as month");
            $yearExpr = DB::raw("CAST(strftime('%Y', created_at) AS INTEGER) as year");
        }

        $results = Transaction::query()
            ->select($monthExpr, $yearExpr, DB::raw('SUM(price - tax - fee) / 100 as value'))
            ->whereNull('refunded_at')
            ->where('created_at', '>=', now()->subMonths(24))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->toBase()
            ->get();

        return $results->map(
            /** @param object{month: int, year: int, value: int} $row */
            fn (object $row) => new MonthlyData(
                month: (int) $row->month,
                year: (int) $row->year,
                value: (int) $row->value,
            )
        )->all();
    }
}
