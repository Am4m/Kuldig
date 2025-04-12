<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Project;
use Illuminate\Support\Facades\Auth;

class ProjectList extends Component
{
    public $sortBy = 'name';
    public $filter = 'all';
    public $sortDirection = 'desc';
    public $search;
    public $user;
    
    // Project modal properties
    public $showModal = false;
    public $modalMode = 'create'; // 'create' or 'edit'
    public $projectId;
    public $projectName;
    public $projectDescription;
    public $isPublic = false;
    
    // Delete confirmation properties
    public $showDeleteConfirmation = false;
    public $projectToDelete;

    protected $rules = [
        'projectName' => 'required|min:3|max:255',
        'projectDescription' => 'nullable|max:1000',
        'isPublic' => 'boolean',
    ];

    public function togglePin($projectId)
    {
        if (!auth()->check()) {
            return;
        }
        
        $project = Project::findOrFail($projectId);
        $membership = $project->members()->where('user_id', auth()->id())->first();
        
        if ($membership) {
            // Toggle the pinned status in the pivot table
            $project->members()->updateExistingPivot(auth()->id(), [
                'is_pinned' => !$membership->pivot->is_pinned
            ]);
        } else {
            // If the user is not a member yet, add them with pinned set to true
            $project->members()->attach(auth()->id(), ['is_pinned' => true]);
        }
    }

    public function sort($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }
    
    // Open create project modal
    public function openCreateModal()
    {
        $this->resetProjectForm();
        $this->modalMode = 'create';
        $this->showModal = true;
    }
    
    // Open edit project modal
    public function openEditModal($projectId)
    {
        $this->resetProjectForm();
        $this->modalMode = 'edit';
        $this->projectId = $projectId;
        
        $project = Project::findOrFail($projectId);
        $this->projectName = $project->name;
        $this->projectDescription = $project->description;
        $this->isPublic = $project->is_public;
        
        $this->showModal = true;
    }
    
    // Close modal
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetProjectForm();
    }
    
    // Reset form fields
    public function resetProjectForm()
    {
        $this->projectId = null;
        $this->projectName = '';
        $this->projectDescription = '';
        $this->isPublic = false;
        $this->resetErrorBag();
    }
    
    // Save project (create or update)
    public function saveProject()
    {
        $this->validate();
        
        if ($this->modalMode === 'create') {
            $project = new Project();
            $project->owner_id = auth()->id(); // Set owner
        } else {
            $project = Project::findOrFail($this->projectId);
            
            // Check if user has permission to edit
            if ($project->owner_id !== auth()->id()) {
                // You might want to check for admin permissions too
                session()->flash('error', 'You do not have permission to edit this project.');
                return;
            }
        }
        
        // Update project attributes
        $project->name = $this->projectName;
        $project->description = $this->projectDescription;
        $project->is_public = $this->isPublic;
        $project->save();
        
        // Make sure the creator is a member
        if ($this->modalMode === 'create') {
            $project->members()->attach(auth()->id(), [ 'is_pinned' => false]);
        }
        
        // Close modal and show success message
        $this->closeModal();
        $this->dispatch('notify', [
            'message' => $this->modalMode === 'create' ? 'Project created successfully!' : 'Project updated successfully!',
            'type' => 'success'
        ]);
    }
    
    // Open delete confirmation
    public function confirmDelete($projectId)
    {
        $this->projectToDelete = Project::findOrFail($projectId);
        $this->showDeleteConfirmation = true;
    }
    
    // Cancel delete
    public function cancelDelete()
    {
        $this->showDeleteConfirmation = false;
        $this->projectToDelete = null;
    }
    
    // Delete project
    public function deleteProject()
    {
        if (!$this->projectToDelete) {
            return;
        }
        
        // Check if user has permission to delete
        if ($this->projectToDelete->owner_id !== auth()->id()) {
            // You might want to check for admin permissions too
            session()->flash('error', 'You do not have permission to delete this project.');
            $this->cancelDelete();
            return;
        }
        
        $projectName = $this->projectToDelete->name;
        
        // Delete the project
        $this->projectToDelete->delete();
        
        // Close confirmation and show success message
        $this->cancelDelete();
        $this->dispatch('notify', [
            'message' => "Project \"$projectName\" has been deleted.",
            'type' => 'success'
        ]);
    }

   public function render()
{
    $projects = Project::query()
        ->whereHas('members', fn ($q) =>
            $q->where('user_id', $this->user->id)
        )
        ->when($this->search, fn ($q) =>
            $q->where('name', 'like', '%' . $this->search . '%')
        )
        ->when($this->filter !== 'all', fn ($q) =>
            $q->where('is_public', $this->filter === 'public')
        )
        ->with(['members' => function ($query) {
            $query->where('user_id', $this->user->id);
        }])
        ->with('owner')
        ->orderByRaw("EXISTS (
            SELECT 1 FROM project_member 
            WHERE project_member.project_id = projects.id 
            AND project_member.user_id = ? 
            AND project_member.is_pinned = 1
        ) DESC", [auth()->id()])
        ->orderBy($this->sortBy, $this->sortDirection)
        ->get();

    return view('livewire.project-list', compact('projects'));
}

}