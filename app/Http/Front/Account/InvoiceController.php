<?php

namespace App\Http\Front\Account;

use App\Domains\Premium\Models\Transaction;
use App\Http\Front\Data\User\InvoiceInfoData;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class InvoiceController
{
    public function index(Request $request)
    {
        $user = $request->user();
        assert($user instanceof User);
        $transactions = Transaction::where('user_id', $user->id)->get();

        return view('invoices.index', [
            'user' => $user,
            'transactions' => $transactions,
        ]);
    }

    public function show(Transaction $transaction, Request $request)
    {
        $user = $request->user();
        \Gate::authorize('view', $transaction);
        assert($user instanceof User);

        return view('invoices.show', [
            'user' => $user,
            'transaction' => $transaction,
        ]);
    }

    public function update(InvoiceInfoData $data, Request $request): RedirectResponse
    {
        $user = $request->user();
        assert($user instanceof User);
        $user->invoice_info = $data->info;
        $user->save();

        return redirect()->route('transactions.index')
            ->with('success', 'Vos informations ont bien été enregistrées');
    }
}
