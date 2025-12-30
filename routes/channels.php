<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('chat.user.{id}', function ($user, $id) {
    // hanya user yang id == param yang bisa subscribe private channel
    return (int) $user->id === (int) $id;
});
