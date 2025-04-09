<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Enums\OrderStatusEnum;
use Illuminate\Support\Facades\Log;

class StripeController extends Controller
{
    public function success(Request $request)
    {
        $session = $request->query('session_id');
        dd($session);
        // $order = Order::where('stripe_session_id', $session)->first();
        // if ($order) {
        //     $order->update([
        //         'status' => OrderStatusEnum::COMPLETED,
        //     ]);
        // }
        // return view('stripe.success');
    }

    public function failure() {}

    public function webhook(Request $request)
    {
        $stripe = new \Stripe\StripeClient(config('services.stripe.secret_key'));

        $endpoint_secret = config('services.stripe.endpoint_secret');

        $payload = $request->getContent();
        $sig_header = $request->header('Stripe-Signature');
        $event = null;
        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sig_header,
                $endpoint_secret
            );


            // Handle the event
            Log::info('==========================');
            Log::info('Stripe Webhook Event Type ->: ' . json_encode($event?->type));
            Log::info('Stripe Webhook Event Data ->: ' . json_encode($event));
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

                        $productsToBeDeletedFromCart[] = [
                            ...$productsToBeDeletedFromCart,
                            ...$order->orderItems->map(fn($item) => ($item->product_id))->toArray(),
                        ];
                    }


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
                        $order->status = OrderStatusEnum::Confirmed;
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
            Log::error($e);

            // Invalid signature
            return response()->json(['error' => 'Invalid payload'], 400);
        }
    }
}
