<?php

namespace App\Infrastructure\Payment\Paypal;

use App\Infrastructure\Payment\Payment;

class PaypalPayment extends Payment
{
    public function __construct(\stdClass $order)
    {
        $unit = $order->purchase_units[0];
        $item = $unit->items[0];
        $this->id = $order->id;
        $this->planId = (int) $unit->custom_id;
        $this->firstname = $order->payer->name->given_name;
        $this->lastname = $order->payer->name->surname;
        $this->address = $unit->shipping->address->address_line_1;
        $this->city = $unit->shipping->address->admin_area_2;
        $this->postalCode = $unit->shipping->address->postal_code;
        $this->countryCode = $unit->shipping->address->country_code;
        $this->amount = floatval($item->unit_amount->value);
        $this->vat = floatval($item->tax->value);
    }
}
