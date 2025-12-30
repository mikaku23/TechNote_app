<?php

namespace App\Events;

use App\Models\Message;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use SerializesModels;

    public $message;

    public function __construct(Message $message)
    {
        $this->message = $message->load('sender');
    }

    // broadcast ke channel penerima
    public function broadcastOn()
    {
        return new PrivateChannel('chat.user.' . $this->message->receiver_id);
    }

    public function broadcastWith()
    {
        return [
            'message' => $this->message->toArray(),
        ];
    }
}
