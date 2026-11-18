<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GameRequest;
use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GameController extends Controller
{
    public function index()
    {
        return Game::withCount('products')->orderBy('sort_order')->orderBy('name')->get();
    }

    public function show(Game $game)
    {
        return $game;
    }

    public function store(GameRequest $request)
    {
        $data = $request->safe()->except(['image', 'banner']);
        $data['slug'] = $this->uniqueSlug($data['name']);

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('games', 'public');
        }

        if ($request->hasFile('banner')) {
            $data['banner_path'] = $request->file('banner')->store('game-banners', 'public');
        }

        return response()->json(Game::create($data), 201);
    }

    public function update(GameRequest $request, Game $game)
    {
        $data = $request->safe()->except(['image', 'banner']);

        if ($data['name'] !== $game->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $game->id);
        }

        if ($request->hasFile('image')) 
        {
            if($game->image_path) 
            {
                Storage::disk('public')->delete($game->image_path);
            }

            $data['image_path'] = $request->file('image')->store('games', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($game->banner_path) {
                Storage::disk('public')->delete($game->banner_path);
            }

            $data['banner_path'] = $request->file('banner')->store('game-banners', 'public');
        }

        $game->update($data);

        return $game;
    }

    public function destroy(Game $game) 
    {
        if($game->products()->exists())
        {
            return response()->json([
                'message' => 'This game still has packages. Delete them or set the game to inactive.',
            ], 409);
        }

        if($game->image_path) {
            Storage::disk('public')->delete($game->image_path);
        }

        if ($game->banner_path) {
            Storage::disk('public')->delete($game->banner_path);
        }

        $game->delete();
        return response()->noContent();
    }

    private function uniqueSlug(string $name, ?int $ignoreId = null):string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i = 2;

        while(Game::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()){
            $slug = $base.'-'.$i++;
        }

        return $slug;

    }
}
