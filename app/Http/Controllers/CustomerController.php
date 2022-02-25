<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\Stripe\Customer;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function form()
    {
        return view('stripe.form');
    }

    public function save(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'cc_number' => 'required',
            'month' => 'required',
            'year' => 'required',
            'cvv' => 'required'
        ]);
        $card = [
            'card' => [
                'number' => $request->cc_number,
                'exp_month' => $request->month,
                'exp_year' => $request->year,
                'cvc' => $request->cvv
            ]
        ];
        $user = Auth::user();
        try {
            /* creating a customer in Stripe and save customer ID in database */
            Customer::save($user, $card);
        } catch (\Stripe\Exception\CardException $e) {
            // dd($e->getError());

            return back()->with('error', $e->getError()->message);
        }
        return redirect()->route('products')->with('success', 'Card has been saved.');
    }
}
