<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductImageResource;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductImageController extends Controller
{
    public function upload(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'images' => 'required|array',
            'images.*' => 'required|image|mimes:jpg,jpeg,png,webp|max:20000',
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

        $uploadedImages = [];
        $currentOrder = $product->images()->count();

        foreach ($request->file('images') as $index => $image) {
            $filename = uniqid() . '_' . $image->getClientOriginalName();
            $path = $image->storeAs('products', $filename, 'public');
            $url = Storage::url($path);

            $productImage = $product->images()->create([
                'filename' => $filename,
                'url' => $url,
                'order' => $currentOrder + $index + 1,
            ]);

            $uploadedImages[] = $productImage;
        }

        return response()->json([
            'success' => true,
            'message' => 'Images uploaded successfully',
            'data' => [
                'images' => ProductImageResource::collection($uploadedImages)
            ]
        ], 201);
    }

    public function destroy(Product $product, ProductImage $image)
    {
        if ($image->product_id !== $product->id) {
            return response()->json([
                'success' => false,
                'message' => 'Image does not belong to this product',
                'data' => null
            ], 404);
        }

        // Delete from storage
        $filename = basename($image->url);
        if (Storage::disk('public')->exists('products/' . $filename)) {
            Storage::disk('public')->delete('products/' . $filename);
        }

        $image->delete();

        return response()->json([
            'success' => true,
            'message' => 'Image deleted successfully',
            'data' => null
        ]);
    }
}