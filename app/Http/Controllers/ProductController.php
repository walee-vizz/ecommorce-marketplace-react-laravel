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
        $products = Product::isPublished()->paginate(10);

        return Inertia::render('Home', [
            'products' => ProductListResource::collection($products),
        ]);
    }

    public function show(Product $product)
    {
        return Inertia::render('Product', [
            'product' => new ProductResource($product),
        ]);
    }
}
