<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UploadController extends Controller
{
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

    public function destroy($filename)
    {
        // Find the file in different folders
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