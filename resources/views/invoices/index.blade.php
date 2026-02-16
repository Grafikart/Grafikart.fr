@extends('users.user-layout')

@section('title', 'Mes factures')

@section('content')

    <div class="grid gap-10 grid-cols-1 lg:grid-cols-[1fr_300px]">
        <main>
            <section class="flex flex-col gap-4">
                <h2 class="text-2xl font-bold text-foreground-title flex items-center gap-2">
                    <x-lucide-file-text class="size-6"/>
                    Mes derniers paiements
                </h2>
                <table class="table">
                    <thead>
                    <tr>
                        <th class="uppercase text-muted">Date</th>
                        <th class="uppercase text-muted">Description</th>
                        <th class="uppercase text-muted">Prix</th>
                        <th class="uppercase text-muted"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transactions as $transaction)
                        <tr>
                            <td>{{ $transaction->created_at->translatedFormat('j F Y') }}</td>
                            <td>Compte premium {{ $transaction->duration }} mois</td>
                            <td>{{ number_format($transaction->price / 100, 2, ',', ' ') }} €</td>
                            <td>
                                <x-atoms.button target="_blank" variant="secondary" href="{{ route('transactions.show', ['transaction' => $transaction->id]) }}" class="btn btn-outline ml-auto">
                                    <x-lucide-download/>
                                    Télécharger la facture
                                </x-atoms.button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </section>
        </main>

        <aside class="space-y-8">
            @include('users._subscription')
            @if($transactions->isNotEmpty())
                <section>
                    <h2 class="text-xl font-bold text-foreground-title mb-2">
                        Informations de facturations
                    </h2>
                    <form action="{{ route('transactions.update') }}" class="flex flex-col gap-2" method="post">
                        @csrf
                        <p class="text-muted">
                            Si vous souhaitez préciser des informations supplémentaires (n° de TVA, n° de SIRET ou autres) vous pouvez les ajouter ici.
                        </p>
                        <div>
                            <x-atoms.input type="textarea" name="info" id="invoice-info"
                                      placeholder="Informations à faire figurer sur chaque facture"
                                      aria-label="Informations de facturations"
                                      class="form-textarea w-full" :value="$user->invoice_info"/>
                        </div>
                        <div>
                            <x-atoms.button>Sauvegarder</x-atoms.button>
                        </div>
                    </form>
                </section>
            @endif
        </aside>
    </div>
@endsection
