<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\Project as ProjectModel;
use App\Models\Column;
use App\Models\Task;
use App\Models\Comment;
use App\Models\Label;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Project extends Component
{
    public ProjectModel $project;
    public $columns;
    public $showTaskModal = false;
    public $editingTask = false;
    public $taskColumnId;
    public $taskForm = [
        'id' => null,
        'title' => '',
        'description' => '',
        'due_date' => null,
        'column_id' => null,
    ];
    public $showColumnModal = false;
    public $editingColumn = false;
    public $columnForm = [
        'id' => null, 
        'name' => '',
        'project_id' => null,
    ];
    public $selectedTask = null;
    public $commentForm = [
        'content' => '',
        'task_id' => null,
    ];
    public $commentText = '';
    public $showShareModal = false;
    public $showEmailsPopup = false;
    public $shareForm = [
        'email' => '',
        'role' => 'member',
    ];
    public $searchTerm = '';
    public $filteredUsers = [];
    public $users = [];
    public $userSearch = '';
    public $availableLabels = [];
    public $selectedTaskId = null;
    public $isLoadingTask = false;
    public $showDeleteTaskModal = false;
    public $taskToDelete = null;


    public function mount(ProjectModel $project)
    {
        $this->project = $project;
        $this->loadColumns();
        $this->columnForm['project_id'] = $project->id;
        $this->loadAvailableLabels();
    }

    public function loadAvailableLabels()
    {
        $this->availableLabels = Label::all(); // Or your label model
        
    }

    public function loadColumns()
    {
        $this->columns = Column::where('project_id', $this->project->id)
            ->with(['tasks' => function($query) {
                $query->with(['labels', 'assignees']);
            }])
            ->orderBy('position')
            ->get();
    }

    public function showTaskForm($columnId)
    {
        $this->resetTaskForm();
        $this->taskColumnId = $columnId;
        $this->taskForm['column_id'] = $columnId;
        $this->showTaskModal = true;
        $this->editingTask = false;
    }

    public function editTask($taskId)
    {
        $task = Task::findOrFail($taskId);
        $this->taskForm = [
            'id' => $task->id,
            'title' => $task->title,
            'description' => $task->description,
            'due_date' => $task->due_date,
            'column_id' => $task->column_id,
        ];
        $this->taskColumnId = $task->column_id;
        $this->showTaskModal = true;
        $this->editingTask = true;
    }

    public function saveTask()
    {
        $this->validate([
            'taskForm.title' => 'required|string|max:255',
            'taskForm.description' => 'nullable|string',
            'taskForm.due_date' => 'nullable|date',
            'taskForm.column_id' => 'required|exists:columns,id',
        ]);
        
        if ($this->editingTask) {
            $task = Task::findOrFail($this->taskForm['id']);
            $task->update([
                'title' => $this->taskForm['title'],
                'description' => $this->taskForm['description'],
                'due_date' => $this->taskForm['due_date'],
            ]);
        } else {
            $maxPosition = Task::where('column_id', $this->taskForm['column_id'])->max('position') ?? 0;
            
            $task = Task::create([
                'title' => $this->taskForm['title'],
                'description' => $this->taskForm['description'],
                'due_date' => $this->taskForm['due_date'],
                'column_id' => $this->taskForm['column_id'],
                'position' => $maxPosition + 1,
                'created_by' => Auth::id(),
            ]);
        }
        
        $this->closeTaskModal();
        $this->loadColumns();
        
        if ($this->selectedTask && $this->selectedTask->id == $task->id) {
            $this->openTaskDetail($task->id);
        }
    }
    
    public function closeTaskModal()
    {
        $this->showTaskModal = false;
        $this->resetTaskForm();
    }
    
    public function resetTaskForm()
    {
        $this->taskForm = [
            'id' => null,
            'title' => '',
            'description' => '',
            'due_date' => null,
            'column_id' => null,
        ];
        $this->editingTask = false;
    }
    
    public function showColumnForm()
    {
        $this->resetColumnForm();
        $this->showColumnModal = true;
        $this->editingColumn = false;
    }
    
    public function editColumn($columnId)
    {
        $column = Column::findOrFail($columnId);
        $this->columnForm = [
            'id' => $column->id,
            'name' => $column->name,
            'project_id' => $column->project_id,
        ];
        $this->showColumnModal = true;
        $this->editingColumn = true;
    }
    
    public function saveColumn()
    {
        $this->validate([
            'columnForm.name' => 'required|string|max:255',
            'columnForm.project_id' => 'required|exists:projects,id',
        ]);
        
        if ($this->editingColumn) {
            $column = Column::findOrFail($this->columnForm['id']);
            $column->update([
                'name' => $this->columnForm['name'],
            ]);
        } else {
            // Find the highest position
            $maxPosition = Column::where('project_id', $this->columnForm['project_id'])->max('position') ?? 0;
            
            Column::create([
                'name' => $this->columnForm['name'],
                'project_id' => $this->columnForm['project_id'],
                'position' => $maxPosition + 1,
                'created_by' => Auth::id(),
            ]);
        }
        
        $this->closeColumnModal();
        $this->loadColumns();
    }
    
    public function closeColumnModal()
    {
        $this->showColumnModal = false;
        $this->resetColumnForm();
    }
    
    public function resetColumnForm()
    {
        $this->columnForm = [
            'id' => null,
            'name' => '',
            'project_id' => $this->project->id,
        ];
        $this->editingColumn = false;
    }
    
    public function openTaskDetail($taskId)
    {
        $this->selectedTaskId = $taskId;
        $this->isLoadingTask = true;
        
        try {
            $this->selectedTask = Task::with(['column', 'labels', 'assignees', 'comments.user'])
                                    ->find($taskId);
                                    
            if (!$this->selectedTask) {
                $this->closeTaskDetail();
                return;
            }
            
            $this->commentForm['task_id'] = $taskId;
        } finally {
            $this->isLoadingTask = false;
        }
    }

    public function getSelectedTaskProperty()
    {
        if (!$this->selectedTaskId) return null;
        
        return Task::with(['column', 'labels', 'assignees', 'comments.user'])
                ->find($this->selectedTaskId);
    }
    
    public function closeTaskDetail()
    {
        $this->selectedTask = null;
        $this->resetCommentForm();
    }
    
    public function addComment()
    {
        $this->validate([
            'commentText' => 'required|string',
        ]);
        
        Comment::create([
            'content' => $this->commentText,
            'task_id' => $this->selectedTask->id,
            'user_id' => Auth::id(),
        ]);
        
        $this->resetCommentForm();
        
        if ($this->selectedTask) {
            $this->openTaskDetail($this->selectedTask->id);
        }
    }
    
    public function resetCommentForm()
    {
        $this->commentText = '';
        $this->commentForm = [
            'content' => '',
            'task_id' => null,
        ];
    }
    
    public function updateTaskOrder($taskIds, $columnId, $movedTaskId, $originalColumnId)
    {
        try {
            DB::beginTransaction();
            
            // Get the task that was moved
            $task = Task::findOrFail($movedTaskId);
            
            // Check if the task was moved to a different column
            if ($originalColumnId !== $columnId) {
                // Update the task's column
                $task->column_id = $columnId;
                $task->save();
            }
            
            // Update the position of all tasks in the target column
            foreach ($taskIds as $index => $id) {
                Task::where('id', $id)->update(['position' => $index]);
            }
            
            DB::commit();
            
            // Refresh the columns to show the updated order
            $this->columns = $this->project->columns()
                ->with(['tasks' => function ($query) {
                    $query->orderBy('position');
                }])
                ->orderBy('position')
                ->get();
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('Failed to update task order: ' . $e->getMessage());
        }
    }

    private function refreshProjectData()
    {
        $this->project = Project::with([
            'columns.tasks.labels', 
            'columns.tasks.assignees', 
            'columns.tasks.creator'
        ])->find($this->project->id);
    }

    
    public function render()
    {
        return view('livewire.project');
    }

    public function openShareModal()
    {
        $this->showShareModal = true;
    }

    public function closeShareModal()
    {
        $this->showShareModal = false;
        $this->shareForm = [
            'email' => '',
            'role' => 'member',
        ];
        $this->resetErrorBag();
    }

    public function shareProject()
    {
        $this->validate([
            'shareForm.email' => 'required|email|exists:users,email',
            'shareForm.role' => 'required|in:admin,member,viewer',
        ], [
            'shareForm.email.exists' => 'This user doesn\'t exist in the system.',
        ]);
        
        $user = User::where('email', $this->shareForm['email'])->first();
        
        if ($this->project->members->contains($user->id)) {
            $this->project->members()->updateExistingPivot($user->id, [
                'role' => $this->shareForm['role']
            ]);
            
            session()->flash('message', "{$user->name}'s role has been updated to {$this->shareForm['role']}.");
        } else {
            $this->project->members()->attach($user->id, [
                'role' => $this->shareForm['role']
            ]);
            
            session()->flash('message', "{$user->name} has been added to the project as a {$this->shareForm['role']}.");
        }
        
        $this->project = ProjectModel::with([
            'columns.tasks.labels',
            'columns.tasks.assignees',
            'columns.tasks.creator',
            'members'
        ])->find($this->project->id);
        
        $this->closeShareModal();
    }

    public function removeMember($userId)
    {
        if ($userId == $this->project->created_by) {
            session()->flash('error', 'Cannot remove the project owner.');
            return;
        }
        
        if (!$this->project->members->contains($userId)) {
            session()->flash('error', 'This user is not a member of the project.');
            return;
        }
        
        $user = \App\Models\User::find($userId);
        
        $this->project->members()->detach($userId);
        
        session()->flash('message', "{$user->name} has been removed from the project.");

        $this->project = ProjectModel::with([
            'columns.tasks.labels',
            'columns.tasks.assignees',
            'columns.tasks.creator',
            'members'
        ])->find($this->project->id);
    }

    public function canManageProject()
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }
        
        if ($this->project->owner->id === $user->id) {
            return true;
        }
        
        foreach ($this->project->members as $member) {
            if ($member->id === $user->id && ($member->pivot->role === 'admin')) {
                return true;
            }
        }
        return true;
    }

    public function canEditTasks()
    {
        $user = auth()->user();

        if (!$user) {
            return false;
        }
        
        if ($this->canManageProject()) {
            return true;
        }
        
        foreach ($this->project->members as $member) {
            if ($member->id === $user->id && ($member->pivot->role === 'member')) {
                return true;
            }
        }
        
        return false;
    }

    public function openEmailsPopup()
    {
        // Load users when the popup is opened
        $this->users = User::where('id', '!=', auth()->id()) // exclude current user
                        ->when($this->userSearch, function($query, $search) {
                            return $query->where(function($q) use ($search) {
                                $q->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                        })
                        ->limit(10)
                        ->get();
    }

    public function updatedUserSearch()
    {
        $this->openEmailsPopup();
    }

    public function addAssigneeToTask($userId)
    {
        if (!$this->selectedTask) {
            return;
        }

        if ($this->selectedTask->assignees->contains($userId)) {
            return; // Already assigned
        }

        $this->selectedTask->assignees()->attach($userId);
        $this->selectedTask->refresh();
    }

    public function removeAssigneeFromTask($userId)
    {
        if (!$this->selectedTask) {
            return;
        }

        $this->selectedTask->assignees()->detach($userId);
        $this->selectedTask->refresh();
    }

    public function getContrastColor($hexColor)
    {
        // Remove # if present
        if (substr($hexColor, 0, 1) === '#') {
            $hexColor = substr($hexColor, 1);
        }
        
        // Convert to RGB
        $r = hexdec(substr($hexColor, 0, 2));
        $g = hexdec(substr($hexColor, 2, 2));
        $b = hexdec(substr($hexColor, 4, 2));
        
        // Calculate luminance
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        // Return black or white based on luminance
        return $luminance > 0.5 ? '#000000' : '#FFFFFF';
    }

    public function deleteComment($commentId)
    {
        $comment = Comment::find($commentId);
        
        if (!$comment) {
            return;
        }

        if ($comment->user_id !== auth()->id() && !$this->canManageProject()) {
            session()->flash('message', 'You are not authorized to delete this comment');
            return;
        }
        
        $comment->delete();

        // Refresh the task details
        $this->openTaskDetail($this->selectedTask->id);
    }

    public function deleteTask($taskId)
    {
        $task = Task::find($taskId);
        
        if (!$task) {
            return;
        }
        
        if ($task->created_by !== auth()->id() && !$this->canManageProject()) {
            session()->flash('message', 'You are not authorized to delete this task');
            return;
        }

        $task->delete();
        
        $this->dispatch('notify', [
            'message' => 'Task deleted successfully',
            'type' => 'success'
        ]);
        
        // Force page refresh after task deletion
        return redirect()->route('project.view', [
            'name' => $this->project->owner->name,
            'projectName' => $this->project->name
        ]);
    }

    public function confirmDeleteTask($taskId)
    {
        $this->taskToDelete = Task::find($taskId);
        $this->showDeleteTaskModal = true;
    }

    public function cancelDeleteTask()
    {
        $this->showDeleteTaskModal = false;
        $this->taskToDelete = null;
    }
    public $showDeleteColumnModal = false;
    public $columnToDelete = null;

    public function confirmDeleteColumn($columnId)
    {
        $this->columnToDelete = Column::find($columnId);
        
        if (!$this->columnToDelete) {
            session()->flash('message', 'Column not found');
            return;
        }
        
        $this->showDeleteColumnModal = true;
    }

    public function deleteColumn()
    {
        if (!$this->columnToDelete) {
            session()->flash('message', 'Column not found');
            return;
        }

        if (!$this->canManageProject()) {
            session()->flash('You are not authorized to delete columns');
            return;
        }

        try {
            DB::beginTransaction();

            $this->columnToDelete->tasks()->each(function($task) {
                // Delete comments, assignees, etc. if needed
                $task->comments()->delete();
                $task->assignees()->detach();
                $task->labels()->detach();
                $task->delete();
            });

            $this->columnToDelete->delete();

            DB::commit();
            $this->loadColumns();

        } catch (\Exception $e) {
            DB::rollBack();
        }

        $this->cancelDeleteColumn();
    }

    public function cancelDeleteColumn()
    {
        $this->showDeleteColumnModal = false;
        $this->columnToDelete = null;
    }
    
    public $availableUsers = [];

    public function searchUsers($query)
    {        
        $this->availableUsers = User::where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                ->orWhere('email', 'like', "%{$query}%");
            })
            ->whereNotIn('id', $this->project->members->pluck('id'))
            ->limit(10)
            ->get();
    }

    public function selectUser($email)
    {
        $this->shareForm['email'] = $email;
    }
    
    
}
