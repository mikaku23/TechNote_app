<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Message;
use App\Events\MessageSent;
use App\Models\Conversation;
use Illuminate\Http\Request;
use App\Events\MessageStatusUpdated;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    // daftar kontak: behavior berbeda jika admin atau user
    public function contacts()
    {
        $user = Auth::user();
        if (!$user) abort(401);

        // as admin: tampilkan semua conversation dimana admin_id = user->id (list mahasiswa/dosen)
        if ($user->role && $user->role->status === 'admin') {
            $convos = Conversation::where('admin_id', $user->id)
                ->with(['user'])
                ->orderBy('last_message_at', 'desc')
                ->get();
        } else {
            // sebagai user: tampilkan conversation mereka
            $convos = Conversation::where('user_id', $user->id)
                ->with(['admin'])
                ->orderBy('last_message_at', 'desc')
                ->get();
        }

        $data = $convos->map(function ($c) use ($user) {
            $unread = $c->messages()
                ->where('receiver_id', $user->id)
                ->whereNull('read_at')
                ->count();

            return [
                'conversation' => $c,
                'unread_count' => $unread,
                'last_message' => $c->last_message,
                'last_message_at' => $c->last_message_at,
            ];
        });

        return response()->json($data);
    }

    // ambil pesan conversation
    public function messages($conversationId)
    {
        $user = Auth::user();
        if (!$user) abort(401);

        $conversation = Conversation::with('messages.sender')->findOrFail($conversationId);

        if (!in_array($user->id, [$conversation->user_id, $conversation->admin_id])) {
            abort(403);
        }

        $messages = $conversation->messages;

        $firstUnread = $conversation->messages()
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->orderBy('created_at', 'asc')
            ->first();

        return response()->json([
            'messages' => $messages,
            'first_unread_id' => $firstUnread ? $firstUnread->id : null,
            'conversation' => $conversation,
        ]);
    }

    // kirim pesan
    public function send(Request $request)
    {
        $user = Auth::user();
        if (!$user) abort(401);

        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'body' => 'required|string',
        ]);

        $conversation = Conversation::findOrFail($request->conversation_id);

        if (!in_array($user->id, [$conversation->user_id, $conversation->admin_id])) {
            abort(403);
        }

        $receiverId = ($conversation->user_id === $user->id) ? $conversation->admin_id : $conversation->user_id;

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_id' => $user->id,
            'receiver_id' => $receiverId,
            'body' => $request->body,
            'type' => 'text',
            'status' => 0,
        ]);

        $conversation->update([
            'last_message' => Str::limit($request->body, 100),
            'last_message_at' => Carbon::now(),
        ]);

        // broadcast ke penerima
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }

    // tandai delivered (dipanggil oleh client penerima ketika menerima pesan)
    public function markDelivered($messageId)
    {
        $user = Auth::user();
        if (!$user) abort(401);

        $message = Message::findOrFail($messageId);

        if ($message->receiver_id !== $user->id) abort(403);

        $message->update([
            'status' => 1,
            'delivered_at' => Carbon::now(),
        ]);

        broadcast(new MessageStatusUpdated($message))->toOthers();

        return response()->json($message);
    }

    // tandai dibaca (panggil saat membuka conversation)
    public function markRead($conversationId)
    {
        $user = Auth::user();
        if (!$user) abort(401);

        $conversation = Conversation::findOrFail($conversationId);

        if (!in_array($user->id, [$conversation->user_id, $conversation->admin_id])) {
            abort(403);
        }

        $unreadMessages = Message::where('conversation_id', $conversation->id)
            ->where('receiver_id', $user->id)
            ->whereNull('read_at')
            ->get();

        foreach ($unreadMessages as $msg) {
            $msg->update([
                'status' => 2,
                'read_at' => Carbon::now(),
            ]);
            broadcast(new MessageStatusUpdated($msg))->toOthers();
        }

        return response()->json([
            'marked' => $unreadMessages->count(),
        ]);
    }

    // endpoint bantu: buat conversation jika belum ada (dipakai saat user pertama kali chat)
    public function startConversation(Request $request)
    {
        $user = Auth::user();
        if (!$user) abort(401);

        $request->validate([
            'admin_id' => 'required|exists:Users,id',
        ]);

        $conv = Conversation::firstOrCreate([
            'user_id' => $user->id,
            'admin_id' => $request->admin_id,
        ]);

        return response()->json($conv);
    }
}
