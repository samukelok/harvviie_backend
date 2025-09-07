<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Message\StoreMessageRequest;
use App\Http\Requests\Message\UpdateMessageRequest;
use App\Http\Resources\MessageResource;
use App\Models\Message;
use Illuminate\Http\Request;

class MessageController extends Controller
{
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
                'messages' => MessageResource::collection($messages),
                'pagination' => [
                    'current_page' => $messages->currentPage(),
                    'total_pages' => $messages->lastPage(),
                    'per_page' => $messages->perPage(),
                    'total' => $messages->total(),
                ]
            ]
        ]);
    }

    public function show(Message $message)
    {
        return response()->json([
            'success' => true,
            'message' => 'Message retrieved successfully',
            'data' => [
                'message' => new MessageResource($message)
            ]
        ]);
    }

    public function store(StoreMessageRequest $request)
    {
        $message = Message::create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Message sent successfully',
            'data' => [
                'message' => new MessageResource($message)
            ]
        ], 201);
    }

    public function update(UpdateMessageRequest $request, Message $message)
    {
        $message->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Message updated successfully',
            'data' => [
                'message' => new MessageResource($message)
            ]
        ]);
    }

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