<?php

namespace App\Http\Controllers;

use App\Events\FileShared;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FileShareController extends Controller
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
            'file' => 'required|file|max:10240', // 10MB max size
        ]);

        $path = $request->file('file')->store('files', 'public');

        $fileShare = $project->fileShares()->create([
            'user_id' => Auth::id(),
            'file_name' => $request->file('file')->getClientOriginalName(),
            'file_path' => $path,
        ]);
        
        $fileShare->load('user');

        // We will broadcast an event here later.
        broadcast(new FileShared($fileShare))->toOthers();

        return response()->json($fileShare);
    }
}
