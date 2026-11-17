<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\GameResource;
use App\Models\Game;
use Illuminate\Http\Request;

class GameController extends Controller
{
    public function index(Request $request)
    {
        $games = Game::active()->when($request->search, fn($q, $search) => $q->where('name', 'like', "%{$search}%"))->when($request->genre, fn($q, $genre) => $q->where('genre', $genre))->orderBy('sort_order')->orderBy('name')->get();

        return GameResource::collection($games);
    }

    public function show (string $slug) {

        $game = Game::active()->where('slug', $slug)->with(['products' => fn ($q) => $q->active()->orderBy('price')])->firstOrFail();

        return new GameResource($game);
    }

    public function genres()
    {
        return Game::active()->distinct()->orderBy('genre')->pluck('genre');
    }
}
