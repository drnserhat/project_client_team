<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Auth::user()->projects()->latest()->get();
        return view('dashboard', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_name' => 'required|string|max:255',
        ]);

        $project = Project::create([
            'project_name' => $request->project_name,
            'unique_key' => Str::upper(Str::random(8)),
        ]);

        $project->users()->attach(auth()->id());

        return redirect()->route('dashboard');
    }

    public function join(Request $request)
    {
        $request->validate([
            'unique_key' => 'required|string|exists:projects,unique_key',
        ]);

        $project = Project::where('unique_key', $request->unique_key)->firstOrFail();
        $user = Auth::user();

        if ($project->users()->where('user_id', $user->id)->exists()) {
            return redirect()->back()->with('error', 'Bu projeye zaten üyesiniz.');
        }

        $project->users()->attach($user->id);

        return redirect()->route('projects.show', $project)->with('success', 'Projeye başarıyla katıldınız!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        // Authorize that the user is a member of the project
        if (!Auth::user()->projects->contains($project)) {
            abort(403);
        }

        $messages = $project->messages()->with('user')->latest()->get();

        return view('projects.show', compact('project', 'messages'));
    }

    public function fetchUpdates(Request $request, Project $project)
    {
        $lastTimestamp = $request->input('last_timestamp');

        $messages = $project->messages()
            ->where('created_at', '>', $lastTimestamp)
            ->with('user')
            ->get();

        $files = $project->fileShares()
            ->where('created_at', '>', $lastTimestamp)
            ->with('user')
            ->get();
            
        $items = $messages->concat($files)->sortBy('created_at')->values();

        return response()->json($items);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
