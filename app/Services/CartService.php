<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Product;
use App\Models\VariationTypeOption;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CartService
{
    private ?array $cachedCartItems = null;

    protected const COOKIE_NAME = 'cartItems';
    protected const COOKIE_LIFETIME = (60 * 24 * 365);


    public function addItemToCart(Product $product, int $quantity = 1, array $optionIds = [])
    {

        if (!$optionIds || empty($optionIds)) {
            $optionIds = $product->variationTypes
                ->mapWithKeys(fn(VariationTypeOption $type) => [$type->id => $type?->options[0]?->id])
                ->toArray();
        }
        $price = $product->getPriceForOptions($optionIds);

        if (!Auth::check()) {
            $this->saveItemToCookies($product->id, $quantity, $price, $optionIds);
        } else {

            $this->saveItemToDatabase($product->id, $quantity, $price, $optionIds);
        }
    }

    public function updateItemQuantity(int $productId, int $quantity, array $optionIds = [])
    {
        if (!Auth::check()) {
            $this->updateItemQuantityInCookies($productId, $quantity, $optionIds);
        } else {
            $this->updateItemQuantityInDatabase($productId, $quantity, $optionIds);
        }
    }

    public function removeItemFromCart(int $productId, $optionIds = null)
    {
        if (!Auth::check()) {
            $this->removeItemFromCookies($productId, $optionIds);
        } else {
            $this->removeItemFromDatabase($productId, $optionIds);
        }
    }


    public function getCartItems(): array
    {
        try {

            if (!$this->cachedCartItems) {
                if (Auth::check()) {
                    $cartItems = $this->getCartItemsFromDatabase();
                } else {
                    $cartItems = $this->getCartItemsFromCookies();
                }
                $productIds = collect($cartItems)->pluck('product_id')->toArray();
                $products = Product::with('user.vendor')
                    ->whereIn('id', $productIds)
                    ->forWebsite()
                    ->get()
                    ->keyBy('id');


                $cartItemData = [];

                foreach ($cartItems as $key => $cartItem) {
                    $product = data_get($products, $cartItem['product_id']);
                    if (!$product) continue;

                    $optionInfo = [];

                    $options = VariationTypeOption::with('variationType')
                        ->whereIn('id', $cartItem['option_ids'])
                        ->get()
                        ->keyBy('id');


                    $imageUrl = null;
                    foreach ($cartItem['option_ids'] as $option_id) {
                        $option = data_get($options, $option_id);
                        if (!$option) continue;
                        if (!$imageUrl) {
                            $imageUrl = $option->getFirstMediaUrl('images', 'small');
                        }

                        $optionInfo[] = [
                            'id' => $option->id,
                            'name' => $option->name,
                            'type' => [
                                'id' => $option->variationType->id,
                                'name' => $option->variationType->name,
                            ]
                        ];
                    }
                    // dd($product->user);

                    $cartItemData[] = [
                        'id' => $cartItem['id'],
                        'product_id' => $cartItem['product_id'],
                        'title' => $product->title,
                        'slug' => $product->slug,
                        'price' => $cartItem['price'],
                        'quantity' => $cartItem['quantity'],
                        'option_ids' => $cartItem['option_ids'],
                        'options' => $optionInfo,
                        'image' => $imageUrl ?: $product->getFirstMediaUrl('images', 'small'),
                        'user' => [
                            'id' => $product->created_by,
                            'name' => $product->user?->vendor?->store_name ?? $product->user->name,
                        ]
                    ];
                }

                $this->cachedCartItems = $cartItemData;
            }
            return $this->cachedCartItems;
        } catch (\Exception $e) {
            Log::error($e->getMessage() . PHP_EOL . $e->getTraceAsString());
        }
        return [];
    }

    public function getTotalQuantity(): int
    {
        $totalQuantity = 0;
        foreach ($this->getCartItems() as $cartItem) {
            $totalQuantity += $cartItem['quantity'];
        }
        return $totalQuantity;
    }


    public function getTotalPrice(): float
    {
        $totalPrice = 0;
        foreach ($this->getCartItems() as $cartItem) {
            $totalPrice += ($cartItem['price'] * $cartItem['quantity']);
        }
        return $totalPrice;
    }

    protected function updateItemQuantityInDatabase(int $productId, int $quantity, array $optionIds = [])
    {

        $userId = Auth::user()->id;

        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->whereJsonContains('variation_type_option_ids', $optionIds)
            ->first();

        if ($cartItem) {
            $cartItem->update([
                'quantity' => $quantity
            ]);
        }
    }

    protected function updateItemQuantityInCookies(int $productId, int $quantity, array $optionIds = [])
    {

        $cartItems = $this->getCartItemsFromCookies();
        ksort($optionIds);

        $itemKey = $productId . '_' . json_encode($optionIds);
        if (isset($cartItems[$itemKey])) {
            $cartItems[$itemKey]['quantity'] = $quantity;
        }

        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFETIME);
    }

    protected function saveItemToDatabase(int $productId, int $quantity, $price, array $optionIds = [])
    {
        $userId = Auth::user()->id;
        ksort($optionIds);

        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->whereJsonContains('variation_type_option_ids', $optionIds)
            ->first();
        if ($cartItem) {
            $cartItem->update([
                'quantity' => DB::raw('quantity + ' . $quantity),
            ]);
        } else {
            CartItem::create([
                'user_id' => $userId,
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'variation_type_option_ids' => json_encode($optionIds),
            ]);
        }
    }

    protected function saveItemToCookies(int $productId, int $quantity, $price, array $optionIds = [])
    {
        $cartItems = $this->getCartItemsFromCookies();
        ksort($optionIds);

        $itemKey = $productId . '_' . json_encode($optionIds);

        if (isset($cartItems[$itemKey])) {
            $cartItems[$itemKey]['quantity'] += $quantity;
            $cartItems[$itemKey]['price'] = $price;
        } else {
            $cartItems[$itemKey] = [
                'id' => Str::uuid(),
                'product_id' => $productId,
                'quantity' => $quantity,
                'price' => $price,
                'option_ids' => $optionIds,
            ];
        }
        //Update Cart items back To Cookies.
        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFETIME);
    }

    protected function removeItemFromDatabase(int $productId, array $optionIds = [])
    {
        $userId = Auth::user()->id;
        ksort($optionIds);

        $cartItem = CartItem::where('user_id', $userId)
            ->where('product_id', $productId)
            ->whereJsonContains('variation_type_option_ids', $optionIds)
            ->delete();
        // dd($cartItem);
    }

    protected function removeItemFromCookies(int $productId, array $optionIds = [])
    {
        $cartItems = $this->getCartItemsFromCookies();
        ksort($optionIds);

        $itemKey = $productId . '_' . json_encode($optionIds);

        unset($cartItems[$itemKey]);
        Cookie::queue(self::COOKIE_NAME, json_encode($cartItems), self::COOKIE_LIFETIME);
    }

    protected function getCartItemsFromDatabase()
    {
        $userId = Auth::user()->id;

        $cartItems = CartItem::where('user_id', $userId)
            ->get()->map(function ($cartItem) {
                return [
                    'id' => $cartItem->id,
                    'product_id' => $cartItem->product_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->price,
                    'option_ids' => json_decode($cartItem->variation_type_option_ids, true),
                ];
            })->toArray();
        return $cartItems;
    }

    protected function getCartItemsFromCookies()
    {
        $cartItems = json_decode(Cookie::get(self::COOKIE_NAME, '[]'), true);
        // dd($cartItems);
        return $cartItems;
    }


    public function getCartItemsGrouped(): array
    {
        $cartItems = $this->getCartItems();
        return collect($cartItems)
            ->groupBy(fn($item) => $item['user']['id'])
            ->map(fn($items, $userid) => [
                'user' => $items->first()['user'],
                'items' => $items->toArray(),
                'totalQuantity' => $items->sum('quantity'),
                'totalPrice' => $items->sum(fn($item) => $item['price'] * $item['quantity']),
            ])
            ->toArray();
    }


    public function moveCartItemsToDatabase($userId)
    {
        if (!Auth::check()) {
            return;
        }
        $cartItems = $this->getCartItemsFromCookies();
        if (empty($cartItems)) {
            return;
        }
        foreach ($cartItems as $item) {
            $existingItem = CartItem::where('user_id', $userId)
                ->where('product_id', $item['product_id'])
                ->whereJsonContains('variation_type_option_ids', $item['option_ids'])
                ->first();
            if ($existingItem) {
                $existingItem->update([
                    'quantity' => DB::raw('quantity + ' . $item['quantity']),
                ]);
                continue;
            }
            // Create a new cart item
            CartItem::create([
                'user_id' => $userId,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'variation_type_option_ids' => json_encode($item['option_ids']),
            ]);
        }
        Cookie::queue(self::COOKIE_NAME, '', -1);
    }
}
