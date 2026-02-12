import { loadScript } from '@paypal/paypal-js';
import { useEffect, useEffectEvent, useRef, useState } from 'react';
import { toast } from 'sonner';

import { APIError, apiFetch } from '@/hooks/use-api-fetch.ts';

type Props = {
    price: number;
    duration: number;
    clientId: string;
    plan: number;
};
const countries = [
    'France',
    'Belgique',
    'Suisse',
    'Canada',
    'Luxembourg',
    'Monaco',
    'Allemagne',
    'Espagne',
    'Italie',
    'Pays-Bas',
    'Portugal',
    'Royaume-Uni',
    'États-Unis',
    'Maroc',
    'Tunisie',
    'Algérie',
    'Sénégal',
    "Côte d'Ivoire",
    'Cameroun',
    'Madagascar',
];

export function vatRate(countryCode: string): number {
    if (countryCode === 'FR') {
        return 0.2;
    }
    return 0;
}

export function vatPrice(price: number, countryCode: string) {
    return Math.floor((price - price / (1 + vatRate(countryCode))) * 100) / 100;
}

export function PaypalPayment(props: Props) {
    const [country, setCountry] = useState('FR');
    const description = `Compte premium ${props.duration} mois`;
    const tax = country ? vatPrice(props.price, country) : 0;
    const currency = 'EUR';
    const priceWithoutTax = props.price - tax;

    const loadButtons = useEffectEvent(async () => {
        try {
            const paypal = await loadScript({
                clientId: props.clientId,
                currency: currency,
            });
            if (!paypal || !paypal.Buttons) {
                throw new Error('Cannot load paypal Button SDK');
            }
            container.current!.innerHTML = '';
            const buttons = await paypal
                .Buttons({
                    style: {
                        label: 'pay',
                        color: 'blue',
                        tagline: false,
                    },
                    createOrder: (data, actions) =>
                        actions.order.create({
                            intent: 'CAPTURE',
                            purchase_units: [
                                {
                                    description,
                                    custom_id: props.plan.toString(),
                                    items: [
                                        {
                                            name: description,
                                            quantity: '1',
                                            unit_amount: {
                                                value: priceWithoutTax.toString(),
                                                currency_code: currency,
                                            },
                                            tax: {
                                                value: tax.toString(),
                                                currency_code: currency,
                                            },
                                            category: 'DIGITAL_GOODS',
                                        },
                                    ],
                                    amount: {
                                        currency_code: currency,
                                        value: props.price.toString(),
                                        breakdown: {
                                            item_total: {
                                                currency_code: currency,
                                                value: priceWithoutTax.toString(),
                                            },
                                            tax_total: {
                                                currency_code: currency,
                                                value: tax.toString(),
                                            },
                                        },
                                    },
                                },
                            ],
                        }),
                    onApprove: async (data) => {
                        apiFetch(`/api/premium/paypal/${data.orderID}`, {
                            method: 'POST',
                        })
                            .then((r) => {
                                window.location.href = '?success=1';
                            })
                            .catch((e: APIError) => {
                                toast.error(e.message);
                            });
                    },
                })
                .render(container.current!);
        } catch (e) {
            toast.error(
                'Une erreur est survenue lors du chargement des boutons PaypPal',
            );
        }
    });
    const container = useRef<HTMLDivElement>(null);

    useEffect(() => {
        if (country) {
            loadButtons();
        }
    }, [country]);

    return (
        <div>
            <div className="space-y-2">
                <label className="text-muted text-xs font-medium uppercase tracking-wide">
                    Pays de résidence
                </label>
                <select
                    value={country}
                    onChange={(e) => setCountry(e.target.value)}
                    className="border-border bg-background w-full rounded-md border px-4 py-3"
                >
                    <option value="">Veuillez sélectionner un pays</option>
                    {countries.map((c) => (
                        <option key={c} value={c}>
                            {c}
                        </option>
                    ))}
                </select>
                <div ref={container}></div>
            </div>
        </div>
    );
}
