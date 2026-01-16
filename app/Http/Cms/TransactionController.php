<?php

namespace App\Http\Cms;

use App\Domains\Cms\CmsController;
use App\Domains\Premium\Models\Transaction;
use App\Http\Cms\Data\Transaction\TransactionRowData;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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

        $search = $request->string('q')->trim()->toString();
        if ($search !== '') {
            if (str_starts_with($search, 'user:')) {
                $userId = (int) substr($search, 5);
                $query->where('user_id', $userId);
            } else {
                $query->where('method_id', 'LIKE', "%{$search}%");
            }
        }

        return $this->cmsIndex(query: $query, extra: ['q' => $search]);
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        $transaction->refunded_at = now();
        $transaction->save();

        return back()->with('success', 'Le paiement a bien été marqué comme remboursé');
    }
}
