<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['images', 'collections']);

        // Apply filters
        if ($request->filled('q')) {
            $query->search($request->q);
        }

        if ($request->filled('collection')) {
            $query->whereHas('collections', function ($q) use ($request) {
                $q->where('collections.id', $request->collection);
            });
        }

        if ($request->boolean('in_stock')) {
            $query->inStock();
        }

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Include trashed if requested (admin only)
        if ($request->boolean('include_deleted') && $request->user()?->isAdmin()) {
            $query->withTrashed();
        }

        $products = $query->orderBy('created_at', 'desc')
                         ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Products retrieved successfully',
            'data' => [
                'products' => ProductResource::collection($products),
                'pagination' => [
                    'current_page' => $products->currentPage(),
                    'total_pages' => $products->lastPage(),
                    'per_page' => $products->perPage(),
                    'total' => $products->total(),
                ]
            ]
        ]);
    }

    public function show(Product $product)
    {
        $product->load(['images', 'collections']);

        return response()->json([
            'success' => true,
            'message' => 'Product retrieved successfully',
            'data' => [
                'product' => new ProductResource($product)
            ]
        ]);
    }

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
                'data' => [
                    'product' => new ProductResource($product)
                ]
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
                'data' => [
                    'product' => new ProductResource($product)
                ]
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

    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully',
            'data' => null
        ]);
    }

    public function restore($id)
    {
        $product = Product::withTrashed()->findOrFail($id);
        $product->restore();

        return response()->json([
            'success' => true,
            'message' => 'Product restored successfully',
            'data' => [
                'product' => new ProductResource($product)
            ]
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