@php
$subscription = app(\App\Domains\Premium\PremiumService::class)->findSubscriptionForUser(auth()->user());
@endphp

<section>
    @if($subscription)
        <h2 class="text-xl font-bold text-foreground-title mb-2">
            Mon abonnement
        </h2>
        @if($subscription->isActive())
            <p class="mb-2 text-muted">
                Vous êtes actuellement abonné. Votre prochain prélèvement aura lieu le
                <strong class="text-foreground-title">{{ $subscription->next_payment->translatedFormat('j F Y') }}</strong>.
            </p>
            <form action="{{ route('users.subscription') }}" method="post" target="_blank">
                @csrf
                <x-atoms.button>
                    <x-lucide-pen class="size-4"/>
                    Gérer mon abonnement
                </x-atoms.button>
            </form>
        @else
            <p class="mb-2 text-muted">
                Vous avez annulé votre abonnement. Il sera automatiquement suspendu après le
                <strong class="text-foreground-title">{{ $subscription->next_payment->translatedFormat('j F Y') }}</strong>
            </p>
            <form action="{{ route('users.subscription') }}" method="post" target="_blank">
                @csrf
                <x-atoms.button>
                    <x-lucide-pen class="size-4"/>
                    Réactiver mon abonnement
                </x-atoms.button>
            </form>
        @endif
    @elseif($user->isPremium())
        <h2 class="text-xl font-bold text-foreground-title mb-2">
            Mon abonnement
        </h2>
        <p class="mb-2 text-muted">
            Vous êtes actuellement premium jusqu'au <strong class="text-foreground-title">{{ $user->premium_end_at->translatedFormat('j F Y') }}</strong>.
        </p>
    @else
        <h2 class="text-xl font-bold text-foreground-title mb-2">
            Vous n'êtes pas premium :(
        </h2>
        <x-atoms.button variant="secondary" href="{{ route('premium') }}">
            <x-lucide-star class="size-4"/>
            Devenir premium
        </x-atoms.button>
    @endif
</section>
