<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageStatusUpdated implements ShouldBroadcast
{
    use SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function broadcastOn()
    {
        // kirim ke pengirim (agar sender lihat perubahan centang)
        return new PrivateChannel('chat.user.' . $this->message->sender_id);
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
            'status' => $this->message->status,
            'delivered_at' => $this->message->delivered_at,
            'read_at' => $this->message->read_at,
            'conversation_id' => $this->message->conversation_id,
        ];
    }
}
