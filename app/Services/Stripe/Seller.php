<?php

namespace App\Services\Stripe;

class Seller
{
    /**
     * Create express account via Stripe OAuth
     *
     * @param $code
     * @return object
     */
    public static function create($code)
    {
        \Stripe\Stripe::setApiKey(config('services.stripe.secret'));
        return  \Stripe\OAuth::token([
            'grant_type' => 'authorization_code',
            'code' => $code,
            'assert_capabilities' => ['transfers'],
        ]);
    }
}
