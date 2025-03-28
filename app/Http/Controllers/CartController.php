<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
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
        //        dd($cartService);

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
            //            'product_id' => 'required|exists:products,id',
        ]);
        //    dd($data);
        try {
            $cartService->addItemToCart($product, $data['quantity'], $data['option_ids']);
        } catch (\Exception $exception) {
            Log::error('Error while adding item to cart: ' . $exception->getMessage());
            return back()->withErrors($exception->getMessage());
        }

        return back()->with('success', 'Product added to cart successfully.');
    }

    public function checkout(Request $request, CartService $cartService)
    {
        $cartItems = $cartService->getCartItems();
        if (empty($cartItems)) {
            return back()->with('error', 'Your cart is empty.');
        }

        // Proceed to checkout logic here
        // For example, redirect to a checkout page or process payment

        return redirect()->route('checkout.index');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product, CartService $cartService)
    {
        $data = $request->validate([
            'option_ids' => 'nullable|array',
            'quantity' => 'required|integer|min:1',
            //            'product_id' => 'required|exists:products,id',
        ]);

        $optionIds = $request->input('option_ids');
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
}
