<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Message;
use Illuminate\Http\Request;

/**
 * @OA\Tag(
 *     name="Messages",
 *     description="Endpoints for managing messages"
 * )
 */
class MessageController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/messages",
     *     tags={"Messages"},
     *     summary="Get paginated list of messages",
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter messages by status",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="type",
     *         in="query",
     *         description="Filter messages by type",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of messages per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Messages retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="messages",
     *                     type="array",
     *                     @OA\Items(
     *                         type="object",
     *                         @OA\Property(property="id", type="integer"),
     *                         @OA\Property(property="type", type="string"),
     *                         @OA\Property(property="status", type="string"),
     *                         @OA\Property(property="content", type="string"),
     *                         @OA\Property(property="created_at", type="string", format="date-time")
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
        $query = Message::query();

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('type')) {
            $query->byType($request->type);
        }

        $messages = $query->orderBy('created_at', 'desc')
                         ->paginate($request->input('per_page', 15));

        return response()->json([
            'success' => true,
            'message' => 'Messages retrieved successfully',
            'data' => [
                'messages' => $messages->items(), // simplified inline schema
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'total_pages' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                ]
            ]
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/messages/{id}",
     *     tags={"Messages"},
     *     summary="Get single message by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the message",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(
     *                     property="message",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer"),
     *                     @OA\Property(property="type", type="string"),
     *                     @OA\Property(property="status", type="string"),
     *                     @OA\Property(property="content", type="string"),
     *                     @OA\Property(property="created_at", type="string", format="date-time")
     *                 )
     *             )
     *         )
     *     )
     * )
     */
    public function show(Message $message)
    {
        return response()->json([
            'success' => true,
            'message' => 'Message retrieved successfully',
            'data' => ['message' => $message]
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/messages",
     *     tags={"Messages"},
     *     summary="Send a new message",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"type","content"},
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="content", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Message sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function store(StoreMessageRequest $request)
    {
        $message = Message::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => ['message' => $message]
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/messages/{id}",
     *     tags={"Messages"},
     *     summary="Update a message",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the message",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="type", type="string"),
     *             @OA\Property(property="content", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     )
     * )
     */
    public function update(UpdateMessageRequest $request, Message $message)
    {
        $message->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Message updated successfully',
            'data' => ['message' => $message]
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/messages/{id}",
     *     tags={"Messages"},
     *     summary="Delete a message",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the message",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Message deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", type="object", nullable=true)
     *         )
     *     )
     * )
     */
    public function destroy(Message $message)
    {
        $message->delete();

        return response()->json([
            'success' => true,
            'message' => 'Message deleted successfully',
            'data' => null
        ]);
    }
}
