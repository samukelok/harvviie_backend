<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\About\UpdateAboutRequest;
use App\Http\Resources\AboutResource;
use App\Models\About;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="About",
 *     description="Endpoints for managing About content"
 * )
 */
class AboutController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/about",
     *     tags={"About"},
     *     summary="Get About content",
     *     description="Retrieve the About page content",
     *     @OA\Response(
     *         response=200,
     *         description="About content retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="about", type="object")
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/about",
     *     tags={"About"},
     *     summary="Update About content",
     *     description="Update the About page content",
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"content","milestones"},
     *             @OA\Property(property="content", type="string"),
     *             @OA\Property(property="milestones", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="About content updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="about", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthorized")
     * )
     */
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
