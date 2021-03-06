<?php

namespace App\Http\Controllers;

use Stripe\Charge;
use Stripe\Stripe;
use App\Models\User;
use Stripe\Customer;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\Stripe\Transaction;
use Illuminate\Support\Facades\Auth;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::paginate(12);
        return view('product.index')->with('products', $products);
    }

    /**
     * Purchase a product from a seller.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function purchase(Request $request)
    {
        $this->validate($request, [
            'upc' => 'required'
        ]);
        $user = Auth::user();
        $product = Product::where('upc', '=', $request->upc)->first();
        if (is_null($product)) {
            return back()->with('error', 'Product not found');
        }

        //Create a Transaction.
        Transaction::create($user, $product);

        return back()->with('success', sprintf('You have bought the %s', $product->name));
    }

    /**
     * Create a product.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $this->validate($request, [
            'name' => 'required',
            'price' => 'required'
        ]);

        $product = new Product();
        $product->upc = Str::uuid();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->seller_id = $user->id;

        if ($request->has('description')) {
            $product->description = $request->description;
        }

        if ($request->has('image')) {
            $product->image = $request->file('image')->store('images', ['disk' => 'public']);
        }
        $product->save();

        return back()->with('success', 'Product created.');
    }

    /**
     * Add new product view.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function add()
    {
        return view('product.add');
    }

    /**
     * Edit a product view.
     *
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function edit($id)
    {
        $product = Product::find($id);
        if (is_null($product)) {
            return back()->with('error', 'Product Not Found');
        }
        return view('product.edit');
    }
}
