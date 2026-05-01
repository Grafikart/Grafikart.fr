<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\Premium\Models\Transaction;
use App\Http\Cms\Data\Transaction\TransactionReportRowData;
use App\Http\Cms\Data\Transaction\TransactionRowData;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class TransactionController extends CmsController
{
    protected string $componentPath = 'transactions';

    protected string $model = Transaction::class;

    protected string $rowData = TransactionRowData::class;

    protected string $route = 'transactions';

    public function index(Request $request): Response
    {
        $query = Transaction::query()
            ->with('user')
            ->orderByDesc('created_at');

        return $this->cmsIndex(query: $query);
    }

    public function report(Request $request): Response
    {
        $year = $request->integer('year', now()->year);
        $isSqlite = DB::connection()->getDriverName() === 'sqlite';

        $monthExpr = $isSqlite ? "CAST(strftime('%m', created_at) AS INTEGER)" : 'EXTRACT(MONTH FROM created_at)';
        $yearExpr = $isSqlite ? "CAST(strftime('%Y', created_at) AS INTEGER)" : 'EXTRACT(YEAR FROM created_at)';

        $results = Transaction::query()
            ->whereNull('refunded_at')
            ->whereRaw("{$yearExpr} = ?", [$year])
            ->selectRaw("method, {$monthExpr} as month, SUM(price) as price, SUM(tax) as tax, SUM(fee) as fee")
            ->groupByRaw("{$monthExpr}, method")
            ->orderByRaw("{$monthExpr} DESC")
            ->get();

        return Inertia::render('transactions/report', [
            'year' => $year,
            'items' => TransactionReportRowData::collect($results),
        ]);
    }

    protected function applySearch(string $search, Builder $query): Builder
    {
        if (str_starts_with($search, 'user:')) {
            $userId = (int) substr($search, 5);

            return $query->where('user_id', $userId);
        } else {
            return $query->whereLike('method_id', "%{$search}%");
        }
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->refunded_at = now();
        $transaction->save();

        return back()->with('success', 'Le paiement a bien été marqué comme remboursé');
    }
}
