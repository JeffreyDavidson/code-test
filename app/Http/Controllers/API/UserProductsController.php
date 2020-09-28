<?php

namespace App\Http\Controllers\API;

use App\User;
use App\Product;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class UserProductsController extends Controller
{
    public function index(User $user)
    {
        $products = $user->products;

        return response()->json($products, 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Product $product)
    {
        abort_if(Auth::guard('api')->user()->products->contains($product), 403);

        Auth::guard('api')->user()->products()->attach($product);

        return response()->json($product, 201);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        abort_unless(Auth::guard('api')->user()->products->contains($product), 403);

        Auth::guard('api')->user()->products()->detach($product);

        return response()->json(null, 200);
    }
}
