<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Project $project)
    {
        if (!Auth::user()->projects->contains($project)) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $request->validate([
            'content' => 'required|string',
        ]);

        $message = $project->messages()->create([
            'user_id' => Auth::id(),
            'content' => $request->content,
        ]);

        // Eager load the user relationship
        $message->load('user');

        // Broadcast the event
        broadcast(new MessageSent($message))->toOthers();

        return response()->json($message);
    }
}
