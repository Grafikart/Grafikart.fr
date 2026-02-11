import { useState } from 'react';

import { PaypalPayment } from '@/components/payment/paypal-payment.tsx';
import { StripePayment } from '@/components/payment/stripe-payment.tsx';
import {
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog.tsx';
import { Toaster } from '@/components/ui/sonner.tsx';

type PaymentMethod = 'stripe' | 'paypal';

type Props = {
    duration: number;
    onClose: () => void;
    plan: number;
    price: number;
    paypalId: string;
};

export function PremiumDialog({
    duration,
    plan,
    onClose,
    price,
    paypalId,
}: Props) {
    const [open, setOpen] = useState(true);
    const [method, setMethod] = useState<PaymentMethod>('stripe');

    const handleOpenChange = (isOpen: boolean) => {
        setOpen(isOpen);
        if (!isOpen) {
            onClose();
        }
    };

    return (
        <Dialog open={open} onOpenChange={handleOpenChange}>
            <Toaster />
            <DialogContent className="max-w-100">
                <DialogHeader>
                    <DialogTitle className="text-lg font-bold">
                        Méthode de paiement
                    </DialogTitle>
                </DialogHeader>

                <div className="space-y-6">
                    {/* Payment method selector */}
                    <div className="space-y-2">
                        <label className="text-muted text-xs font-medium uppercase tracking-wide">
                            Sélectionner la méthode de paiement
                        </label>
                        <div className="flex gap-2">
                            <button
                                type="button"
                                onClick={() => setMethod('stripe')}
                                className={`cursor-pointer rounded-md border-2 p-2 transition-colors ${
                                    method === 'stripe'
                                        ? 'border-primary'
                                        : 'border-border hover:border-muted'
                                }`}
                            >
                                <img
                                    src="/images/payment-methods.png"
                                    alt="Visa / Mastercard"
                                    className="h-8"
                                />
                            </button>
                            <button
                                type="button"
                                onClick={() => setMethod('paypal')}
                                className={`cursor-pointer rounded-md border-2 p-2 transition-colors ${
                                    method === 'paypal'
                                        ? 'border-primary'
                                        : 'border-border hover:border-muted'
                                }`}
                            >
                                <img
                                    src="/images/paypal.svg"
                                    alt="PayPal"
                                    className="h-8"
                                />
                            </button>
                        </div>
                    </div>

                    {/* Stripe: auto-renewal for monthly */}
                    {method === 'stripe' && <StripePayment plan={plan} />}

                    {/* PayPal: country selector */}
                    {method === 'paypal' && (
                        <PaypalPayment
                            plan={plan}
                            price={price}
                            duration={duration}
                            clientId={paypalId}
                        />
                    )}
                </div>
            </DialogContent>
        </Dialog>
    );
}
