<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Http\Cms\Data\Chart\DailyData;
use App\Http\Cms\Data\Chart\MonthlyData;
use App\Http\Cms\Data\User\UserRowData;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Response;

class UserController extends CmsController
{

    protected string $componentPath = 'users';
    protected string $model = UserRowData::class;
    protected string $rowData = UserRowData::class;
    protected string $formData = UserRowData::class;
    protected string $requestData = UserRowData::class;
    protected string $route = 'users';

    public function index(Request $request): Response
    {
        $bannedFilter = $request->boolean('banned');

        $query = User::query()
            ->orderByDesc('created_at');

        if ($bannedFilter) {
            $query->onlyTrashed();
        } else {
            $query->withTrashed();
        }

        $extra = [
            'banned_filter' => $bannedFilter,
        ];

        // Show charts only on the first page without filters
        if ($request->integer('page', 1) === 1 && !$bannedFilter && !$request->filled('q')) {
            $extra['months'] = $this->getMonthlySignups();
            $extra['days'] = $this->getDailySignups();
        }

        return $this->cmsIndex(query: $query, extra: $extra);
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->isPremium()) {
            return back()->with('error', 'Impossible de bannir un utilisateur premium');
        }

        return $this->cmsDestroy(model: $user, message: "L'utilisateur {$user->name} a été banni");
    }

    /**
     * @return array<DailyData>
     */
    private function getDailySignups(): array
    {
        $results = User::query()
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as value'))
            ->where('created_at', '>=', now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return $results->map(fn($row) => new DailyData(
            date: $row->date,
            value: $row->value,
        ))->all();
    }

    /**
     * @return array<MonthlyData>
     */
    private function getMonthlySignups(): array
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            $monthExpr = DB::raw('EXTRACT(MONTH FROM created_at)::integer as month');
            $yearExpr = DB::raw('EXTRACT(YEAR FROM created_at)::integer as year');
        } else {
            $monthExpr = DB::raw("CAST(strftime('%m', created_at) AS INTEGER) as month");
            $yearExpr = DB::raw("CAST(strftime('%Y', created_at) AS INTEGER) as year");
        }

        $results = User::query()
            ->select($monthExpr, $yearExpr, DB::raw('COUNT(*) as value'))
            ->where('created_at', '>=', now()->subMonths(24))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        return $results->map(fn($row) => new MonthlyData(
            month: $row->month,
            year: $row->year,
            value: $row->value,
        ))->all();
    }
}
