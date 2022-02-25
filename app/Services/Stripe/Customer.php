<?php

namespace App\Services\Stripe;


use App\Models\User;

class Customer
{
    public static function save(User $user, array $card)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

        // Use a Stripe PHP library method that may throw an exception....
        $token = \Stripe\Token::create($card);
        $customer = \Stripe\Customer::create([
            'name' => $user->name,
            'source' => $token->id,
            'email' => $user->email
        ]);
        $user->update(['stripe_customer_id' => $customer->id]);
    }
}
