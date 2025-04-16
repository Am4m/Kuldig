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

    public function show(string $name, string $projectName): View
    {
        $user = User::where('name', $name)->firstOrFail();
        $project = $user->projects()->where('name', $projectName)->firstOrFail();
        
        return view('project.project-show', [
            'project' => $project,
        ]);
    }
}