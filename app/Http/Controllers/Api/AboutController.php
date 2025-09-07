<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\About\UpdateAboutRequest;
use App\Http\Resources\AboutResource;
use App\Models\About;
use Illuminate\Http\Request;

class AboutController extends Controller
{
    public function show()
    {
        $about = About::getSingle();

        return response()->json([
            'success' => true,
            'message' => 'About content retrieved successfully',
            'data' => [
                'about' => new AboutResource($about)
            ]
        ]);
    }

    public function update(UpdateAboutRequest $request)
    {
        $about = About::getSingle();
        
        if ($about->exists) {
            $about->update([
                'content' => $request->content,
                'milestones' => $request->milestones,
                'updated_by_user_id' => $request->user()->id,
            ]);
        } else {
            $about = About::create([
                'content' => $request->content,
                'milestones' => $request->milestones,
                'updated_by_user_id' => $request->user()->id,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'About content updated successfully',
            'data' => [
                'about' => new AboutResource($about)
            ]
        ]);
    }
}