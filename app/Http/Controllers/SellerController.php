<?php

namespace App\Http\Controllers;

use Exception;
use Stripe\Stripe;
use Stripe\Account;
use App\Models\User;
use Illuminate\Http\Request;
use Stripe\BitcoinTransaction;
use App\Services\Stripe\Seller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SellerController extends Controller
{
    /**
     * Redirect to Stripe To Create Account
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function create()
    {
        $user = Auth::user();
        if (!is_null($user->stripe_connect_id)) {
            return redirect()->route('stripe.login');
        }
        $session = request()->session()->getId();

        $url = 'https://connect.stripe.com/express/oauth/authorize?redirect_uri=' . config('services.stripe.redirect_uri') . '&client_id=' . config('services.stripe.connect_id') . '&stripe_user[email]=' . $user->email . '&state=' . $session;

        return redirect($url);
    }

    /**
     * Redirect To Stripe Connect Account.
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function login()
    {
        $user = Auth::user();
        Stripe::setApiKey(config('services.stripe.secret'));
        $account_link = Account::createLoginLink($user->stripe_connect_id);
        return redirect($account_link->url);
    }

    /**
     * Save a Stripe Connect Account.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function save(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
            'state' => 'required'
        ]);

        try {
            $session = DB::table('sessions')->where('id', '=', $request->state)->first();
            $data = Seller::create($request->code);
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
        User::find($session->user_id)->update(['stripe_connect_id' => $data->stripe_user_id]);
        return redirect()->route('products')->with('success', 'Account information has been saved.');
    }
}
