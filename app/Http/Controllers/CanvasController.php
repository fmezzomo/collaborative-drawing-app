<?php

namespace App\Http\Controllers;

use App\Events\UserJoined;
use App\Events\UserLeft;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CanvasController extends Controller
{
    public function joinCanvas(Request $request)
    {
        echo ',,,';
        exit;
        return;
        $user = [
            'user_id' => Auth::id(),
            'name' => $request->name,
            'color' => $request->color,
            'canvas_id' => $request->canvas_id,
        ];

        broadcast(new UserJoined($user));

        return response()->json(['status' => 'success']);
    }

    public function leaveCanvas(Request $request)
    {
        $user = [
            'user_id' => Auth::id(),
            'canvas_id' => $request->canvas_id,
        ];

        broadcast(new UserLeft($user));

        return response()->json(['status' => 'success']);
    }
}
