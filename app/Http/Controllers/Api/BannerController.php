<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\StoreBannerRequest;
use App\Http\Requests\Banner\UpdateBannerRequest;
use App\Http\Resources\BannerResource;
use App\Models\Banner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function index(Request $request)
    {
        $query = Banner::query();

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        $banners = $query->ordered()->get();

        return response()->json([
            'success' => true,
            'message' => 'Banners retrieved successfully',
            'data' => [
                'banners' => BannerResource::collection($banners)
            ]
        ]);
    }

    public function show(Banner $banner)
    {
        return response()->json([
            'success' => true,
            'message' => 'Banner retrieved successfully',
            'data' => [
                'banner' => new BannerResource($banner)
            ]
        ]);
    }

    public function store(StoreBannerRequest $request)
    {
        $banner = Banner::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Banner created successfully',
            'data' => [
                'banner' => new BannerResource($banner)
            ]
        ], 201);
    }

    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $banner->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Banner updated successfully',
            'data' => [
                'banner' => new BannerResource($banner)
            ]
        ]);
    }

    public function destroy(Banner $banner)
    {
        $banner->delete();

        return response()->json([
            'success' => true,
            'message' => 'Banner deleted successfully',
            'data' => null
        ]);
    }
}