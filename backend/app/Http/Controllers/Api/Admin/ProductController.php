<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProductRequest;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $products = Product::with('game:id,name')->when($request->game_id, fn($q, $gameId) => $q->where('game_id', $gameId))->orderBy('game_id')->orderBy('price')->get();

        return $products;
    }

    public function show(Product $product) 
    {
        return $product->load('game:id,name');
    }

    public function store(ProductRequest $request) {

        $data = $request->safe()->except('image');

        if($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        return response()->json(Product::create($data), 201);
    }

    public function update(ProductRequest $request, Product $product) 
    {
        $data = $request->safe()->except('image');
        
        if($request->hasFile('image')) {

            if($product->image_path) {
                Storage::disk('public')->delete($product->image_path);
            }

            $data['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return $product;
    }

    public function destroy(Product $product) {
        try {
            $product->delete();
        } catch (QueryException) {
            return response()->json([
                'message' => 'This package has been ordered before. Set it to inactive instead.',
            ], 409);
        }

        if($product->image_path) {
            Storage::disk('public')->delete($product->image_path);
        }

        return response()->noContent();
    }


}
