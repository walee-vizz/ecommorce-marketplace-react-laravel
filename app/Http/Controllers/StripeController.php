<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Order;
use App\Models\CartItem;
use Illuminate\Http\Request;
use App\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\OrderViewResource;

class StripeController extends Controller
{
    public function success(Request $request)
    {
        $session = $request->query('session_id');
        $user = auth()->user();
        $orders = Order::where('stripe_session_id', $session)->where('user_id', $user?->id)->get();
        if ($orders->isEmpty()) {
            abort(404);
        }

        return Inertia::render('stripe/success', [
            'orders' => OrderViewResource::collection($orders),
        ]);
    }

    public function failure(Request $request)
    {
        $sessionId = $request->query('session_id');

        try {
            // Retrieve the session from Stripe
            $stripe = new \Stripe\StripeClient(config('services.stripe.secret_key'));

            // Update the order status in your database
            $orders = Order::where('stripe_session_id', $sessionId)->with('orderItems', 'user', 'vendorUser', 'vendor')->get();
            if ($orders->isNotEmpty()) {
                foreach ($orders as $order) {
                    $order->status = OrderStatusEnum::Draft;
                    $order->save();
                }
            }

            // Optionally, log the cancellation
            Log::info("Checkout session {$sessionId} was cancelled.");

            // Redirect or render a view to inform the user
            return Inertia::render('stripe/failure', [
                'orders' => OrderViewResource::collection($orders),
            ]);
        } catch (\Exception $e) {
            Log::error("Error retrieving Stripe session: {$e->getMessage()}");

            // Handle the error gracefully
            abort(500, 'An error occurred while processing your request.');
        }
    }

    public function webhook(Request $request)
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret_key'));

        $webhook_secret = config('services.stripe.webhook_secret');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $webhook_secret
            );


            // Handle the event
            Log::info('==========================');
            Log::info('Stripe Webhook Event Type ->: ' . json_encode($event?->type));
            Log::info($event);
            Log::info('==========================');

            switch ($event->type) {
                case 'checkout.session.completed':
                    $session = $event->data->object; // contains a \Stripe\Checkout\Session
                    $paymentIntent = $session['payment_intent'];
                    $orders = Order::with('orderItems')->where('stripe_session_id', $session['id'])->get();

                    $productsToBeDeletedFromCart = [];
                    foreach ($orders as $order) {
                        $order->payment_intent = $paymentIntent;
                        $order->status = OrderStatusEnum::Paid;
                        $order->save();

                        // Send notification to vendor/user

                        $productsToBeDeletedFromCart = [
                            ...$productsToBeDeletedFromCart,
                            ...$order->orderItems->map(fn($item) => ($item->product_id))->toArray(),
                        ];


                        // Reduce Product Quantity
                        foreach ($order->orderItems as $item) {
                            $product = $item->product;
                            $options = $item->variation_type_option_ids;
                            if ($options) {
                                sort($options);
                                $variation = $product->variations()->whereJsonContains('variation_type_option_ids', $options)->first();
                                if ($variation && $variation->quantity != null) {
                                    $variation->quantity -= $item->quantity;
                                    $variation->save();
                                }
                            } else if ($product->quantity != null) {
                                $product->quantity -= $item->quantity;
                                $product->save();
                            }
                        }
                    }
                    // Log::info('Products to be deleted from cart: ', $productsToBeDeletedFromCart);
                    // Log::info('User: ' .  $orders->first()->user_id);
                    // Delete Cart Items
                    CartItem::where('user_id', $orders->first()->user_id)
                        ->whereIn('product_id', $productsToBeDeletedFromCart)
                        ->where('saved_for_later', false)
                        ->delete();

                    break;
                case 'charge.updated':
                    $charge = $event->data->object; // contains a \Stripe\Checkout\Session
                    $transactionId = $charge['balance_transaction'];
                    $paymentIntent = $charge['payment_intent'];
                    $balanceTransaction = $stripe->balanceTransactions->retrieve($transactionId, []);

                    $orders = Order::where('payment_intent', $paymentIntent)->get();
                    $totalAmount = $balanceTransaction['amount'];
                    $stripeFee = 0;
                    foreach ($balanceTransaction['fee_details'] as $feeDetail) {
                        if ($feeDetail['type'] == 'stripe_fee') {
                            $stripeFee = $feeDetail['amount'];
                        }
                    }
                    $plateFormFeePercent = config('app.plateform_fee_pct');


                    foreach ($orders as $order) {
                        $vendorShare = $order->total_price / $totalAmount;
                        $order->online_payment_commission = $vendorShare * $stripeFee;
                        $order->website_commission = ($order->total_price - $order->online_payment_commission) / 100 * $plateFormFeePercent;
                        $order->vendor_subtotal = $order->total_price - $order->online_payment_commission - $order->website_commission;
                        $order->status = OrderStatusEnum::Processing;
                        $order->save();

                        // Send notification to vendor/user
                    }

                    // Fulfill the purchase...
                    break;
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object; // contains a \Stripe\PaymentIntent
                    // Handle successful payment here
                    break;
                default:
                    // Unexpected event type
                    Log::warning('Unhandled event type ' . $event->type);
            }
        } catch (\UnexpectedValueException $e) {
            Log::error($e);
            // Invalid payload
            return response()->json(['error' => 'Invalid payload'], 400);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            Log::error($e, [
                'payload' => $payload,
                'sig_header' => $sig_header,
                'webhook_secret' => $webhook_secret,
                'type' => 'SignatureVerificationException',
            ]);

            // Invalid signature
            return response()->json(['error' => 'Invalid payload'], 400);
        }
    }
}
