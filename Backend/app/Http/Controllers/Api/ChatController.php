<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ChatController extends Controller
{
    /**
     * Get messages between the current user and another user.
     */
    public function getMessages(string $userId)
    {
        $currentUserId = Auth::id();

        $messages = Message::where(function ($query) use ($currentUserId, $userId) {
                $query->where('sender_id', $currentUserId)
                      ->where('receiver_id', $userId);
            })
            ->orWhere(function ($query) use ($currentUserId, $userId) {
                $query->where('sender_id', $userId)
                      ->where('receiver_id', $currentUserId);
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark messages as read
        Message::where('sender_id', $userId)
            ->where('receiver_id', $currentUserId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return response()->json($messages);
    }

    /**
     * Send a new message.
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        $message->load(['sender', 'receiver']);

        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    /**
     * Get a list of users who have active chat sessions (for admin).
     */
    public function getActiveChats()
    {
        if (!Auth::user()->is_admin) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $currentUserId = Auth::id();

        // Find all users who have interacted with the admin
        $userIds = Message::where('sender_id', $currentUserId)
            ->orWhere('receiver_id', $currentUserId)
            ->select('sender_id', 'receiver_id')
            ->get()
            ->flatMap(function ($message) use ($currentUserId) {
                return [$message->sender_id, $message->receiver_id];
            })
            ->unique()
            ->filter(function ($id) use ($currentUserId) {
                return $id !== $currentUserId;
            });

        $users = User::whereIn('id', $userIds)
            ->select('id', 'name', 'email')
            ->get()
            ->map(function ($user) use ($currentUserId) {
                $lastMessage = Message::where(function ($query) use ($currentUserId, $user) {
                        $query->where('sender_id', $currentUserId)
                              ->where('receiver_id', $user->id);
                    })
                    ->orWhere(function ($query) use ($currentUserId, $user) {
                        $query->where('sender_id', $user->id)
                              ->where('receiver_id', $currentUserId);
                    })
                    ->latest()
                    ->first();

                $unreadCount = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $currentUserId)
                    ->where('is_read', false)
                    ->count();

                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'last_message' => $lastMessage ? $lastMessage->message : null,
                    'last_message_time' => $lastMessage ? $lastMessage->created_at : null,
                    'unread_count' => $unreadCount
                ];
            });

        return response()->json($users);
    }

    /**
     * Get the first admin user to start a chat with.
     */
    public function getAdmin()
    {
        $admin = User::where('is_admin', true)->first();
        if (!$admin) {
            return response()->json(['message' => 'No admin available'], 404);
        }
        return response()->json([
            'id' => $admin->id,
            'name' => $admin->name
        ]);
    }
}
