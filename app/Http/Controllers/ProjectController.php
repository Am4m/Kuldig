<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\JsonResponse;

class ProjectController extends Controller
{
    public function indexJson(string $name): JsonResponse
    {
        $user = User::where('name', $name)->firstOrFail();
        $projects = $user->projects()
        ->select('projects.id', 'projects.name', 'projects.description')
        ->get();

        return response()->json([
            'user' => $user->name,
            'projects' => $projects,
        ]);
    }

    public function indexView(string $name): View
    {
        $user = User::where('name', $name)->firstOrFail();
        $projects = $user->projects()->get();

        return view('profile.projects', [
            'user' => $user,
            'projects' => $projects,
        ]);
    }

    /**
     * Display the specified project.
     */
    public function show(string $name, string $projectName): View
    {
        $user = User::where('name', $name)->firstOrFail();
        $project = $user->projects()->where('name', $projectName)->firstOrFail();
        
        return view('profile.project-show', [
            'user' => $user,
            'project' => $project,
        ]);
    }

     public function create()
    {
        return view('projects.create');
    }

     public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'required|boolean',
            'language' => 'nullable|string|max:100',
        ]);

        $validated['user_id'] = Auth::id();

        Project::create($validated);

        return redirect()->route('projects.index')->with('success', 'Project created successfully.');
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_public' => 'required|boolean',
            'language' => 'nullable|string|max:100',
        ]);

        $project->update($validated);

        return redirect()->route('projects.index')->with('success', 'Project updated successfully.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);
        $project->delete();

        return redirect()->route('projects.index')->with('success', 'Project deleted.');
    }
}