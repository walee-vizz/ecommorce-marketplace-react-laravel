<?php

namespace App\Http\Controllers;

use App\Http\Resources\ProductResource;
use App\Http\Resources\ProductListResource;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{

    public function index()
    {
        $products = Product::forWebsite()->paginate(10);

        return Inertia::render('home', [
            'products' => ProductListResource::collection($products),
        ]);
    }

    public function show(Product $product)
    {
        // dd($product);
        return Inertia::render('products/show', [
            'product' => new ProductResource($product),
            'variationOptions' => request('options', []),
        ]);
    }
}
