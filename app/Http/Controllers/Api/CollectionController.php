<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Collection\StoreCollectionRequest;
use App\Http\Requests\Collection\UpdateCollectionRequest;
use App\Http\Resources\CollectionResource;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CollectionController extends Controller
{
    public function index(Request $request)
    {
        $query = Collection::with(['products']);

        if ($request->has('is_active')) {
            $query->where('is_active', $request->boolean('is_active'));
        }

        // Include trashed if requested (admin only)
        if ($request->boolean('include_deleted') && $request->user()?->isAdmin()) {
            $query->withTrashed();
        }

        $collections = $query->orderBy('created_at', 'desc')
                            ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Collections retrieved successfully',
            'data' => [
                'collections' => CollectionResource::collection($collections),
                'pagination' => [
                    'current_page' => $collections->currentPage(),
                    'total_pages' => $collections->lastPage(),
                    'per_page' => $collections->perPage(),
                    'total' => $collections->total(),
                ]
            ]
        ]);
    }

    public function show(Collection $collection)
    {
        $collection->load(['products.images']);

        return response()->json([
            'success' => true,
            'message' => 'Collection retrieved successfully',
            'data' => [
                'collection' => new CollectionResource($collection)
            ]
        ]);
    }

    public function store(StoreCollectionRequest $request)
    {
        $collection = Collection::create($request->validated());
        $collection->load(['products']);

        return response()->json([
            'success' => true,
            'message' => 'Collection created successfully',
            'data' => [
                'collection' => new CollectionResource($collection)
            ]
        ], 201);
    }

    public function update(UpdateCollectionRequest $request, Collection $collection)
    {
        $collection->update($request->validated());
        $collection->load(['products']);

        return response()->json([
            'success' => true,
            'message' => 'Collection updated successfully',
            'data' => [
                'collection' => new CollectionResource($collection)
            ]
        ]);
    }

    public function destroy(Collection $collection)
    {
        $collection->delete();

        return response()->json([
            'success' => true,
            'message' => 'Collection deleted successfully',
            'data' => null
        ]);
    }

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
                'data' => [
                    'errors' => $validator->errors()
                ]
            ], 422);
        }

        // Sync products with positions
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
                'collection' => new CollectionResource($collection)
            ]
        ]);
    }

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