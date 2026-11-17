<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\BannerRequest;
use App\Models\Banner;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    public function index()
    {
        return Banner::with('game:id,name')->orderBy('sort_order')->orderBy('id')->get();
    }

    public function show(Banner $banner)
    {
        return $banner->load('game:id,name');
    }

    public function store(BannerRequest $request)
    {
        $data = $request->safe()->except('image');
        $data['image_path'] = $request->file('image')->store('banners', 'public');

        return response()->json(Banner::create($data), 201);
    }

    public function update(BannerRequest $request, Banner $banner)
    {
        $data = $request->safe()->except('image');

        if ($request->hasFile('image')) {
            Storage::disk('public')->delete($banner->image_path);
            $data['image_path'] = $request->file('image')->store('banners', 'public');
        }

        $banner->update($data);

        return $banner;
    }

    public function destroy(Banner $banner)
    {
        Storage::disk('public')->delete($banner->image_path);
        $banner->delete();

        return response()->noContent();
    }
}
