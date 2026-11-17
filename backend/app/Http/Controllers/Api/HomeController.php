<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\BannerResource;
use App\Http\Resources\GameResource;
use App\Http\Resources\ProductResource;
use App\Models\Banner;
use App\Models\Game;
use App\Models\Product;

class HomeController extends Controller
{
    public function __invoke()
    {
        return response()->json([
            'banners' => BannerResource::collection(
                Banner::visible()->with('game:id,slug')->orderBy('sort_order')->orderBy('id')->get()
            ),
            'promos' => ProductResource::collection(
                Product::purchasable()->promo()->where('stock', '>', 0)
                    ->with('game:id,name,slug')->latest('updated_at')->limit(8)->get()
            ),
            'games' => GameResource::collection(
                Game::active()->orderBy('sort_order')->orderBy('name')->get()
            ),
        ]);
    }
}
