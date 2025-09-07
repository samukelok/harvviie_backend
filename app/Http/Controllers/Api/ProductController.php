<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * @OA\Tag(
 *     name="Products",
 *     description="Endpoints for managing products"
 * )
 */
class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Get paginated list of products",
     *     @OA\Parameter(name="q", in="query", description="Search query", required=false, @OA\Schema(type="string")),
     *     @OA\Parameter(name="collection", in="query", description="Filter by collection ID", required=false, @OA\Schema(type="integer")),
     *     @OA\Parameter(name="in_stock", in="query", description="Filter products in stock", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="is_active", in="query", description="Filter active products", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="include_deleted", in="query", description="Include soft-deleted products (admin only)", required=false, @OA\Schema(type="boolean")),
     *     @OA\Parameter(name="per_page", in="query", description="Number of products per page", required=false, @OA\Schema(type="integer", default=15)),
     *     @OA\Response(
     *         response=200,
     *         description="Products retrieved successfully"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Product::with(['images', 'collections']);

        if ($request->filled('q')) {
            $query->search($request->q);
        }

        if ($request->filled('collection')) {
            $query->whereHas('collections', fn($q) => $q->where('collections.id', $request->collection));
        }

        if ($request->boolean('in_stock')) $query->inStock();
        if ($request->has('is_active')) $query->where('is_active', $request->boolean('is_active'));
        if ($request->boolean('include_deleted') && $request->user()?->isAdmin()) $query->withTrashed();

        $products = $query->orderBy('created_at', 'desc')->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'data' => [
                'products' => $products->items(),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'total_pages' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Get single product by ID",
     *     @OA\Parameter(name="id", in="path", description="Product ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product retrieved successfully")
     * )
     */
    public function show(Product $product)
    {
        $product->load(['images', 'collections']);

        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully',
            'data' => ['product' => $product]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/products",
     *     tags={"Products"},
     *     summary="Create a new product",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price_cents", type="integer"),
     *             @OA\Property(property="is_active", type="boolean"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string", format="binary"))
     *         )
     *     ),
     *     @OA\Response(response=201, description="Product created successfully")
     * )
     */
    public function store(StoreProductRequest $request)
    {
        DB::beginTransaction();

        try {
            $product = Product::create($request->validated());

            if ($request->hasFile('images')) {
                $this->uploadImages($product, $request->file('images'));
            }

            $product->load(['images', 'collections']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => ['product' => $product]
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create product',
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Update a product",
     *     @OA\Parameter(name="id", in="path", description="Product ID", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="price_cents", type="integer"),
     *             @OA\Property(property="is_active", type="boolean"),
     *             @OA\Property(property="images", type="array", @OA\Items(type="string", format="binary"))
     *         )
     *     ),
     *     @OA\Response(response=200, description="Product updated successfully")
     * )
     */
    public function update(UpdateProductRequest $request, Product $product)
    {
        DB::beginTransaction();

        try {
            $product->update($request->validated());

            if ($request->hasFile('images')) {
                $this->uploadImages($product, $request->file('images'));
            }

            $product->load(['images', 'collections']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => ['product' => $product]
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to update product',
                'data' => null
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/products/{id}",
     *     tags={"Products"},
     *     summary="Delete a product",
     *     @OA\Parameter(name="id", in="path", description="Product ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product deleted successfully")
     * )
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
            'data' => null
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/products/{id}/restore",
     *     tags={"Products"},
     *     summary="Restore a soft-deleted product",
     *     @OA\Parameter(name="id", in="path", description="Product ID", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Product restored successfully")
     * )
     */
    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return response()->json([
            'success' => true,
            'message' => 'Product restored successfully',
            'data' => ['product' => $product]
        ]);
    }

    private function uploadImages(Product $product, array $images)
    {
        foreach ($images as $index => $image) {
            $filename = uniqid() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('products', $filename, 'public');
            $url = Storage::url($path);

            $product->images()->create([
                'filename' => $filename,
                'url' => $url,
                'order' => $product->images()->count() + $index + 1,
            ]);
        }
    }
}
