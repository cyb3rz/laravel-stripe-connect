<?php

namespace App\Services\Stripe;


use App\Models\Payment;
use App\Models\Product;
use Illuminate\Foundation\Auth\User;

class Transaction
{
    public static function create(User $user, Product $product)
    {
        // Initial data.
        $amount = $product->price;
        $payout = $amount * 0.90;
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        // Create a Stripe charge from the customer purchase.
        $charge = \Stripe\Charge::create([
            'amount' => self::toStripeFormat($amount),
            'currency' => 'usd',
            'customer' => $user->stripe_customer_id,
            'description' => $product->name
        ]);

        // Pay funds to seller, with platform fees extracted.
        \Stripe\Transfer::create([
            'amount' => self::toStripeFormat($payout),
            "currency" => "usd",
            "source_transaction" => $charge->id,
            'destination' => $product->seller->stripe_connect_id
        ]);

        // Save transaction to database.
        $payment = new Payment();
        $payment->customer_id = $user->id;
        $payment->product_id = $product->id;
        $payment->stripe_charge_id = $charge->id;
        $payment->paid_out = $payout;
        $payment->fees_collected = $amount - $payout;
        $payment->save();
    }

    public static function toStripeFormat(float $amount)
    {
        return $amount * 100;
    }
}
