<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatusEnum;
use App\Models\Order;
use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use function Termwind\render;

class CartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(CartService $cartService)
    {
        // dd($cartService->getCartItemsGrouped());

        return Inertia::render('carts/index', [
            'cartItems' => $cartService->getCartItemsGrouped(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, Product $product, CartService $cartService)
    {
        $request->mergeIfMissing([
            'quantity' => 1,
        ]);
        $data = $request->validate([
            'option_ids' => 'nullable|array',
            'quantity' => 'required|integer|min:1',
        ]);
        try {
            $cartService->addItemToCart($product, $data['quantity'], $data['option_ids'] ?: []);
        } catch (\Exception $exception) {
            Log::error('Error while adding item to cart: ' . $exception->getMessage());
            return back()->withErrors($exception->getMessage());
        }

        return back()->with('success', 'Product added to cart successfully.');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, CartService $cartService)
    {
        $data = $request->validate([
            'option_ids' => 'nullable|array',
            'quantity' => 'required|integer|min:1',
        ]);

        $optionIds = $request->input('option_ids') ?: [];
        $quantity = $request->input('quantity');

        $cartService->updateItemQuantity($product->id, $quantity, $optionIds);

        return back()->with('success', 'Quantity updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product, CartService $cartService)
    {
        $optionIds = $request->input('option_ids');
        $cartService->removeItemFromCart($product->id, $optionIds);

        return back()->with('success', 'Product removed from cart successfully.');
    }


    public function checkout(Request $request, CartService $cartService)
    {
        $cartItems = $cartService->getCartItems();
        if (empty($cartItems)) {
            return back()->with('error', 'Your cart is empty.');
        }

        \Stripe\Stripe::setApiKey(config('services.stripe.secret_key'));

        $vendorId = $request->input('vendor_id');
        $allCartItems = $cartService->getCartItemsGrouped();

        DB::beginTransaction();
        try {
            $checkoutCartitems = $allCartItems;
            if ($vendorId) {
                $checkoutCartitems = [$allCartItems[$vendorId]];
            }
            $orders = [];
            $items = [];
            $lineItems = [];
            foreach ($checkoutCartitems as $item) {
                $user = $item['user'];
                $cartItems = $item['items'];

                $order = Order::create([
                    'stripe_session_id' => null,
                    'user_id' => $user['id'],
                    'vendor_user_id' => $user['id'],
                    'total_price' => $item['totalPrice'],
                    'status' => OrderStatusEnum::Draft,
                ]);
                $orders[] = $order;
                foreach ($cartItems as $cartItem) {
                    $items[] = [
                        'order_id' => $order->id,
                        'product_id' => $cartItem['product_id'],
                        'price' => $cartItem['price'],
                        'quantity' => $cartItem['quantity'],
                        'variation_type_option_ids' => $cartItem['option_ids'],
                    ];
                    $description = collect($cartItem['options'])->map(function ($option) {
                        return "{$option['type']['name']}: {$option['name']}";
                    })->implode(', ');
                    $cartItem['description'] = $description;
                    $lineItem = [
                        'price_data' => [
                            'currency' => config('app.currency'),
                            'product_data' => [
                                'name' => $cartItem['title'],
                                'images' => [$cartItem['image']],
                            ],
                            'unit_amount' => $cartItem['price'] * 100,
                        ],
                        'quantity' => $cartItem['quantity'],
                    ];
                    if ($description) {
                        $lineItem['price_data']['product_data']['description'] = $description;
                    }
                    $lineItems[] = $lineItem;
                }
                $order->orderItems()->createMany($items);
            }
            // dd($lineItems, $items);
            // Create Stripe Checkout Session
            $session = \Stripe\Checkout\Session::create([
                'customer_email' => $request->user()->email,
                'payment_method_types' => ['card'],
                'line_items' => $lineItems,
                'mode' => 'payment',
                'success_url' => route('stripe.success', []) . "?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => route('stripe.failure', []),
            ]);

            // Update the order with the Stripe session ID
            foreach ($orders as $order) {
                $order->update([
                    'stripe_session_id' => $session->id,
                ]);
            }

            DB::commit();
            // Redirect to Stripe Checkout
            return redirect()->away($session->url);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error while creating checkout session: ' . $e->getMessage());
            return back()->with('error', $e->getMessage() ?: 'Something went wrong');
        }
        // Proceed to checkout logic here
        // For example, redirect to a checkout page or process payment

        return redirect()->route('checkout.index');
    }
}
