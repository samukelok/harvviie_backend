<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Tag(
 *     name="Uploads",
 *     description="Endpoints for general file uploads"
 * )
 */
class UploadController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/upload",
     *     tags={"Uploads"},
     *     summary="Upload an image",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="image",
     *                     type="string",
     *                     format="binary",
     *                     description="Image file to upload"
     *                 ),
     *                 @OA\Property(
     *                     property="folder",
     *                     type="string",
     *                     enum={"banners","collections","general"},
     *                     description="Optional folder to store the image"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(response=201, description="Image uploaded successfully"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function upload(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
            'folder' => 'nullable|string|in:banners,collections,general',
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

        $folder = $request->input('folder', 'general');
        $file = $request->file('image');

        $filename = uniqid() . '_' . $file->getClientOriginalName();
        $path = $file->storeAs($folder, $filename, 'public');
        $url = Storage::url($path);

        return response()->json([
            'success' => true,
            'message' => 'Image uploaded successfully',
            'data' => [
                'filename' => $filename,
                'url' => $url,
                'path' => $path,
            ]
        ], 201);
    }

    /**
     * @OA\Delete(
     *     path="/api/upload/{filename}",
     *     tags={"Uploads"},
     *     summary="Delete an uploaded image",
     *     @OA\Parameter(
     *         name="filename",
     *         in="path",
     *         required=true,
     *         description="Filename of the image to delete",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(response=200, description="Image deleted successfully"),
     *     @OA\Response(response=404, description="Image not found")
     * )
     */
    public function destroy($filename)
    {
        $folders = ['banners', 'collections', 'general', 'products'];
        $deleted = false;

        foreach ($folders as $folder) {
            $path = $folder . '/' . $filename;
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
                $deleted = true;
                break;
            }
        }

        if ($deleted) {
            return response()->json([
                'success' => true,
                'message' => 'Image deleted successfully',
                'data' => null
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Image not found',
            'data' => null
        ], 404);
    }
}
