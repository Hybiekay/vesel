<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Stripe\Checkout\Session;
use Stripe\Stripe;


class StripeController extends Controller
{
    public function payment(Request $request)
    {
        try {
//Validation
            /*$validator = Validator::make($request->all(), [
                'product_ids' => 'required|array',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }*/


            $productIds = $request->product_ids;

            $products = Product::whereIn('id', $productIds)->get();

            // Ensure products are not empty
            if ($products->isEmpty()) {
                return response()->json(['error' => 'No valid products found'], 400);
            }

            $lineItems = [];

            foreach ($products as $product) {
                $priceInCents = is_numeric($product->price) ? $product->price * 100 : 0;

                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'unit_amount' => $priceInCents,
                        'product_data' => [
                            'name' => $product->name,
                            'description' => $product->description,
                        ],
                    ],
                    'quantity' => 1,
                ];
            }

            Stripe::setApiKey(config('stripe.secret_key'));

            $session = Session::create([
                'payment_method_types' => ['card', 'cashapp', 'us_bank_account'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => 'http://localhost:3000/payment/success',
                'cancel_url' => 'http://localhost:3000/payment/cancel',
            ]);

            //$userId = Auth::id();

            // Create an order record in the database
// TODO: order should be stored after payment
                $order = Order::create([
                'user_id' => 1,
                //'user_id' => $userId,
                'stripe_session_id' => 2139,
                'stripe_session_id' => $session->id,
            ]);

            $order->products()->attach($productIds);

            //return response()->json(['url' => 'www']);
            return response()->json(['url' => $session->url]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
