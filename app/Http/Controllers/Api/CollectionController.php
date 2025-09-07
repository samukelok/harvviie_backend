<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Collection\StoreCollectionRequest;
use App\Http\Requests\Collection\UpdateCollectionRequest;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Collections",
 *     description="API endpoints for managing collections"
 * )
 */
class CollectionController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/collections",
     *     tags={"Collections"},
     *     summary="Get paginated list of collections",
     *     @OA\Parameter(
     *         name="is_active",
     *         in="query",
     *         description="Filter collections by active status",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Collections retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="collections",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="name", type="string"),
     *                         @OA\Property(property="slug", type="string"),
     *                         @OA\Property(property="is_active", type="boolean"),
     *                         @OA\Property(property="created_at", type="string", format="date-time"),
     *                         @OA\Property(property="updated_at", type="string", format="date-time")
     *                     )
     *                 ),
     *                 @OA\Property(
     *                     property="pagination",
     *                     type="object",
     *                     @OA\Property(property="current_page", type="integer"),
     *                     @OA\Property(property="total_pages", type="integer"),
     *                     @OA\Property(property="per_page", type="integer"),
     *                     @OA\Property(property="total", type="integer")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Collection::with(['products']);

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        if ($request->boolean('include_deleted') && $request->user()?->isAdmin()) {
            $query->withTrashed();
        }

        $collections = $query->orderBy('created_at', 'desc')
                             ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Collections retrieved successfully',
            'data' => [
                'collections' => $collections->items(),
                'pagination' => [
                    'current_page' => $collections->currentPage(),
                    'total_pages' => $collections->lastPage(),
                    'per_page' => $collections->perPage(),
                    'total' => $collections->total(),
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/collections/{collection}",
     *     tags={"Collections"},
     *     summary="Get a single collection",
     *     @OA\Parameter(
     *         name="collection",
     *         in="path",
     *         description="Collection ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Collection retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="collection",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="name", type="string"),
     *                     @OA\Property(property="slug", type="string"),
     *                     @OA\Property(property="is_active", type="boolean"),
     *                     @OA\Property(property="created_at", type="string", format="date-time"),
     *                     @OA\Property(property="updated_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function show(Collection $collection)
    {
        $collection->load(['products.images']);

        return response()->json([
            'success' => true,
            'message' => 'Collection retrieved successfully',
            'data' => [
                'collection' => $collection
            ]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/collections",
     *     tags={"Collections"},
     *     summary="Create a new collection",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Collection created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function store(StoreCollectionRequest $request)
    {
        $collection = Collection::create($request->validated());
        $collection->load(['products']);

        return response()->json([
            'success' => true,
            'message' => 'Collection created successfully',
            'data' => [
                'collection' => $collection
            ]
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/collections/{collection}",
     *     tags={"Collections"},
     *     summary="Update a collection",
     *     @OA\Parameter(
     *         name="collection",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Collection updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(UpdateCollectionRequest $request, Collection $collection)
    {
        $collection->update($request->validated());
        $collection->load(['products']);

        return response()->json([
            'success' => true,
            'message' => 'Collection updated successfully',
            'data' => [
                'collection' => $collection
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/collections/{collection}",
     *     tags={"Collections"},
     *     summary="Delete a collection",
     *     @OA\Parameter(
     *         name="collection",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Collection deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function destroy(Collection $collection)
    {
        $collection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Collection deleted successfully',
            'data' => null
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/collections/{collection}/assign-products",
     *     tags={"Collections"},
     *     summary="Assign products to a collection",
     *     @OA\Parameter(
     *         name="collection",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="product_ids",
     *                 type="array",
     *                 @OA\Items(type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Products assigned successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function assignProducts(Request $request, Collection $collection)
    {
        $validator = Validator::make($request->all(), [
            'product_ids' => 'required|array',
            'product_ids.*' => 'required|integer|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'data' => ['errors' => $validator->errors()]
            ], 422);
        }

        $syncData = [];
        foreach ($request->product_ids as $index => $productId) {
            $syncData[$productId] = ['position' => $index + 1];
        }

        $collection->products()->sync($syncData);
        $collection->load(['products.images']);

        return response()->json([
            'success' => true,
            'message' => 'Products assigned to collection successfully',
            'data' => [
                'collection' => $collection
            ]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/collections/{collection}/remove-product/{productId}",
     *     tags={"Collections"},
     *     summary="Remove product from collection",
     *     @OA\Parameter(
     *         name="collection",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="productId",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product removed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="null")
     *         )
     *     )
     * )
     */
    public function removeProduct(Collection $collection, $productId)
    {
        $collection->products()->detach($productId);

        return response()->json([
            'success' => true,
            'message' => 'Product removed from collection successfully',
            'data' => null
        ]);
    }
}
