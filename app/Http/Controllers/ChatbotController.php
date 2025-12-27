<?php

namespace App\Http\Controllers;

use App\Services\ChatbotService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatbotController extends Controller
{
    public function handle(Request $request, ChatbotService $service)
    {
        $request->validate(['message' => 'required|string']);

        $user = Auth::user();
        if (!$user) {
            return response()->json(['reply' => 'silakan login terlebih dahulu.'], 401);
        }

        $reply = $service->handle($user, $request->message);

        return response()->json(['reply' => $reply]);
    }
}
