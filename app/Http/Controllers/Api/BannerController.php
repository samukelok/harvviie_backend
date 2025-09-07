<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Banner\StoreBannerRequest;
use App\Http\Requests\Banner\UpdateBannerRequest;
use App\Models\Banner;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Banners",
 *     description="API endpoints for managing banners"
 * )
 */
class BannerController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/banners",
     *     tags={"Banners"},
     *     summary="Get list of banners",
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Banners retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="banners",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="title", type="string"),
     *                         @OA\Property(property="image_url", type="string"),
     *                         @OA\Property(property="is_active", type="boolean"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     )
     *                 )
     *             )
     *         )
     *     )
     * )
     */
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
                'banners' => $banners
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/banners/{banner}",
     *     tags={"Banners"},
     *     summary="Get single banner",
     *     @OA\Parameter(
     *         name="banner",
     *         in="path",
     *         required=true,
     *         description="Banner ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Banner retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="banner",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="image_url", type="string"),
     *                     @OA\Property(property="is_active", type="boolean"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function show(Banner $banner)
    {
        return response()->json([
            'success' => true,
            'message' => 'Banner retrieved successfully',
            'data' => [
                'banner' => $banner
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/banners",
     *     tags={"Banners"},
     *     summary="Create a new banner",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","image_url"},
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="image_url", type="string"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Banner created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="banner",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="image_url", type="string"),
     *                     @OA\Property(property="is_active", type="boolean"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function store(StoreBannerRequest $request)
    {
        $banner = Banner::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Banner created successfully',
            'data' => [
                'banner' => $banner
            ]
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/banners/{banner}",
     *     tags={"Banners"},
     *     summary="Update a banner",
     *     @OA\Parameter(
     *         name="banner",
     *         in="path",
     *         required=true,
     *         description="Banner ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="title", type="string"),
     *             @OA\Property(property="image_url", type="string"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Banner updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="banner",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="title", type="string"),
     *                     @OA\Property(property="image_url", type="string"),
     *                     @OA\Property(property="is_active", type="boolean"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function update(UpdateBannerRequest $request, Banner $banner)
    {
        $banner->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Banner updated successfully',
            'data' => [
                'banner' => $banner
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/banners/{banner}",
     *     tags={"Banners"},
     *     summary="Delete a banner",
     *     @OA\Parameter(
     *         name="banner",
     *         in="path",
     *         required=true,
     *         description="Banner ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Banner deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
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
