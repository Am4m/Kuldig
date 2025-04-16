<div class="h-full flex flex-col bg-gray-900 text-gray-100">
    @if (session()->has('message'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 3000)" 
             x-show="show"
             class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif
    {{-- Project Header --}}
    <div class="p-5 bg-gray-800 shadow-md flex justify-between items-center border-b border-gray-700">
        <div>
            <h1 class="text-2xl font-bold text-white">{{ $project->name }}</h1>
            <p class="text-gray-400 mt-1">{{ $project->description }}</p>
        </div>
        <div class="flex space-x-3">
            @if ($this->canManageProject())
                <button
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200 shadow-lg"
                    wire:click="openShareModal">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path d="M15 8a3 3 0 10-2.977-2.63l-4.94 2.47a3 3 0 100 4.319l4.94 2.47a3 3 0 10.895-1.789l-4.94-2.47a3.027 3.027 0 000-.74l4.94-2.47C13.456 7.68 14.19 8 15 8z" />
                    </svg>
                    Share
                </button>
                <button
                    class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg flex items-center transition-colors duration-200 shadow-lg"
                    wire:click="showColumnForm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Column
                </button>
            @endif
        </div>
    </div>

    {{-- Project Board --}}
    <div class="flex-1 overflow-x-auto p-5 bg-gray-900">
        <div class="flex space-x-5 h-full min-h-[calc(100vh-120px)]">
            @forelse ($columns as $column)
                <div class="bg-gray-800 rounded-xl w-80 flex-shrink-0 flex flex-col h-full max-h-full shadow-lg border border-gray-700">
                    {{-- Column Header --}}
                    <div class="p-4 bg-gray-750 rounded-t-xl flex justify-between items-center border-b border-gray-700">
                        <h3 class="font-medium text-gray-100 text-lg">{{ $column->name }}</h3>
                        <div class="flex items-center">
                            @if ($this->canManageProject())
                                <button
                                    class="text-gray-400 hover:text-indigo-400 transition-colors duration-200"
                                    wire:click="editColumn({{ $column->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                                    </svg>
                                </button>
                                <button
                                    class="ml-3 text-gray-400 hover:text-red-500 transition-colors duration-200"
                                    wire:click="confirmDeleteColumn({{ $column->id }})"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                                        <path fill-rule="evenodd" d="M9 3a1 1 0 00-1 1v1H5a1 1 0 000 2h1v11a2 2 0 002 2h8a2 2 0 002-2V7h1a1 1 0 100-2h-3V4a1 1 0 00-1-1H9zm1 5a1 1 0 012 0v9a1 1 0 11-2 0V8zm4 0a1 1 0 012 0v9a1 1 0 11-2 0V8z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endif
                            @if ($this->canEditTasks())
                                <button
                                    class="ml-3 text-gray-400 hover:text-purple-400 transition-colors duration-200"
                                    wire:click="showTaskForm({{ $column->id }})">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Tasks Container --}}
                    <div
                    class="overflow-hidden p-3 flex-1 overflow-y-auto bg-gray-800 task-column"
                    x-data="{
                        columnId: {{ $column->id }},
                        canEdit: @json($this->canEditTasks()), // Pass the permission from Livewire to Alpine
                        initSortable() {
                            // Only initialize Sortable if user has edit permissions
                            if (!this.canEdit) return;

                            const taskList = this.$el;
                            taskList.setAttribute('data-column-id', this.columnId);

                            new Sortable(taskList, {
                                group: {
                                    name: 'shared-tasks',
                                    pull: this.canEdit ? 'clone' : false,
                                    put: this.canEdit
                                },
                                animation: 150,
                                ghostClass: 'bg-blue-100',
                                dragClass: 'opacity-75',
                                handle: '.task-card',
                                filter: '.no-drag', // Add this class to elements that shouldn't be draggable
                                onStart: (evt) => {
                                    // Additional check on drag start
                                    if (!this.canEdit) {
                                        evt.sortable.cancel();
                                    }
                                },
                                onEnd: (evt) => {
                                    if (!this.canEdit) return;

                                    const taskId = parseInt(evt.item.getAttribute('data-task-id'));
                                    const originalColumnId = parseInt(evt.from.getAttribute('data-column-id'));
                                    const newColumnId = parseInt(evt.to.getAttribute('data-column-id'));

                                    const taskIds = Array.from(evt.to.querySelectorAll('[data-task-id]'))
                                        .map(el => parseInt(el.getAttribute('data-task-id')));

                                    if (originalColumnId !== newColumnId || evt.oldIndex !== evt.newIndex) {
                                        @this.updateTaskOrder(taskIds, newColumnId, taskId, originalColumnId);
                                    }
                                }
                            });
                        }
                    }"
                    x-init="initSortable()"
                    data-column-id="{{ $column->id }}">
                        @forelse ($column->tasks as $task)
                            <div
                                wire:click="openTaskDetail({{ $task->id }})"
                                class="cursor-pointer bg-gray-750 p-4 rounded-lg shadow-md mb-3 task-card border border-gray-700 hover:border-indigo-500 transition-all duration-200"
                                data-task-id="{{ $task->id }}">
                                <div
                                    class="hover:bg-gray-700/70 transition-colors duration-300 group"
                                >
                                    <h4 class="font-medium text-white">{{ $task->title }}</h4>
                                    @if ($task->due_date)
                                        <div class="text-xs mt-3 text-gray-400 flex items-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                            {{ \Carbon\Carbon::parse($task->due_date)->format('M d, Y') }}
                                        </div>
                                    @endif

                                    <div class="flex flex-wrap items-center gap-2 mt-3">
                                        @foreach ($task->labels as $label)
                                            <span class="px-2 py-1 text-xs rounded-full transition-all duration-200" style="background-color: {{ $label->color }}; color: {{ $this->getContrastColor($label->color) }}">
                                                {{ $label->name }}
                                            </span>
                                        @endforeach
                                    </div>
                                    @if ($task->assignees->count() > 0)
                                        <div class="flex mt-3 -space-x-2">
                                            @foreach ($task->assignees->take(3) as $assignee)
                                                <img
                                                    src="{{ $assignee->profile_photo_url }}"
                                                    alt="{{ $assignee->name }}"
                                                    class="w-7 h-7 rounded-full border-2 border-gray-750"
                                                    title="{{ $assignee->name }}">
                                            @endforeach

                                            @if ($task->assignees->count() > 3)
                                                <div class="w-7 h-7 rounded-full bg-indigo-600 flex items-center justify-center text-xs font-medium border-2 border-gray-750">
                                                    +{{ $task->assignees->count() - 3 }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-gray-500 py-8 px-4 rounded-lg bg-gray-850 border border-dashed border-gray-700">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                </svg>
                                <p>No tasks in this column.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            @empty
                <div class="w-full flex items-center justify-center h-64">
                    <div class="text-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2" />
                        </svg>
                        <p class="text-gray-400 mb-5 text-lg">No columns yet.</p>
                        @if ($this->canManageProject())
                            <button
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-5 py-3 rounded-lg shadow-lg transition-colors duration-200"
                                wire:click="showColumnForm">
                                Create your first column
                            </button>
                        @endif
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    @if($showDeleteTaskModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all max-w-md w-full">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-medium text-red-600 dark:text-red-400 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Delete Task
                </h3>
            </div>

            <div class="px-6 py-4">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Are you sure you want to delete the task <span class="font-semibold">{{ $taskToDelete->title }}</span>? This action cannot be undone.
                </p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-900/30 px-6 py-4 flex justify-end space-x-3 rounded-b-lg border-t dark:border-gray-700">
                <button
                    wire:click="cancelDeleteTask"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Cancel
                </button>
                <button
                    wire:click="deleteTask({{ $taskToDelete->id }})"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                    Delete
                </button>
            </div>
        </div>
    </div>
    @endif

    @if($showDeleteColumnModal)
    <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl transform transition-all max-w-md w-full">
            <div class="px-6 py-4 border-b dark:border-gray-700">
                <h3 class="text-lg font-medium text-red-600 dark:text-red-400 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Delete Column
                </h3>
            </div>

            <div class="px-6 py-4">
                <p class="text-sm text-gray-700 dark:text-gray-300">
                    Are you sure you want to delete the Column <span class="font-semibold">{{ $columnToDelete->title }}</span>? This action cannot be undone.
                </p>
            </div>

            <div class="bg-gray-50 dark:bg-gray-900/30 px-6 py-4 flex justify-end space-x-3 rounded-b-lg border-t dark:border-gray-700">
                <button
                    wire:click="cancelDeleteColumn"
                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                >
                    Cancel
                </button>
                <button
                    wire:click="deleteColumn({{ $columnToDelete->id }})"
                    class="px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                >
                    Delete
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Task Modal --}}
    @if ($showTaskModal)
        <div class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 backdrop-blur-sm" x-data>
            <div class="bg-gray-850 rounded-2xl w-full max-w-md mx-4 max-h-screen overflow-y-auto shadow-2xl border border-gray-700"
                 @click.away="$wire.closeTaskModal()">
                <div class="p-5 border-b border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white">{{ $editingTask ? 'Edit Task' : 'Create Task' }}</h3>
                        <button class="text-gray-400 hover:text-gray-200 transition-colors duration-200" wire:click="closeTaskModal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-5">
                    <form wire:submit.prevent="saveTask">
                        <div class="mb-5">
                            <label for="title" class="block text-sm font-medium text-gray-300 mb-2">Title</label>
                            <input
                                type="text"
                                id="title"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition-colors duration-200"
                                wire:model="taskForm.title"
                                required>
                            @error('taskForm.title') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-5">
                            <label for="description" class="block text-sm font-medium text-gray-300 mb-2">Description</label>
                            <textarea
                                id="description"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition-colors duration-200"
                                rows="4"
                                wire:model="taskForm.description"></textarea>
                            @error('taskForm.description') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-5">
                            <label for="due_date" class="block text-sm font-medium text-gray-300 mb-2">Due Date</label>
                            <input
                                type="date"
                                id="due_date"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition-colors duration-200"
                                wire:model="taskForm.due_date">
                            @error('taskForm.due_date') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button
                                type="button"
                                class="px-4 py-2 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600 transition-colors duration-200"
                                wire:click="closeTaskModal">
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md transition-all duration-200">
                                {{ $editingTask ? 'Update Task' : 'Create Task' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Column Modal --}}
    @if ($showColumnModal)
        <div class="fixed inset-0 bg-black bg-opacity-80 flex items-center justify-center z-50 backdrop-blur-sm" x-data>
            <div class="bg-gray-850 rounded-2xl w-full max-w-md mx-4 shadow-2xl border border-gray-700" @click.away="$wire.closeColumnModal()">
                <div class="p-5 border-b border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-xl font-bold text-white">{{ $editingColumn ? 'Edit Column' : 'Create Column' }}</h3>
                        <button class="text-gray-400 hover:text-gray-200 transition-colors duration-200" wire:click="closeColumnModal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-5">
                    <form wire:submit.prevent="saveColumn">
                        <div class="mb-5">
                            <label for="name" class="block text-sm font-medium text-gray-300 mb-2">Column Name</label>
                            <input
                                type="text"
                                id="name"
                                class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition-colors duration-200"
                                wire:model="columnForm.name"
                                required>
                            @error('columnForm.name') <span class="text-red-400 text-sm mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button
                                type="button"
                                class="px-4 py-2 bg-gray-700 text-gray-200 rounded-lg hover:bg-gray-600 transition-colors duration-200"
                                wire:click="closeColumnModal">
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md transition-all duration-200">
                                {{ $editingColumn ? 'Update Column' : 'Create Column' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Task Detail Sidebar --}}
    @if ($selectedTask)
        <div class="fixed inset-0 z-40 overflow-hidden" x-data>
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute inset-0 bg-gray-900 bg-opacity-80 transition-opacity backdrop-blur-sm"
                     @click="$wire.closeTaskDetail()"></div>
                <section class="absolute inset-y-0 right-0 pl-10 max-w-full flex">
                    <div class="relative w-screen max-w-md">
                        <div class="h-full flex flex-col bg-gray-850 shadow-2xl border-l border-gray-700">
                            <div class="min-h-0 flex-1 flex flex-col overflow-y-auto">
                                <div class="p-5 border-b border-gray-700 flex justify-between items-center">
                                    <h2 class="text-xl font-bold text-white">Task Details</h2>
                                    <button
                                        class="text-gray-400 hover:text-gray-200 transition-colors duration-200"
                                        wire:click="closeTaskDetail">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>

                                {{-- Loading State --}}
                                @if ($isLoadingTask)
                                    <div class="flex items-center justify-center h-64">
                                        <div class="animate-spin rounded-full h-12 w-12 border-t-2 border-b-2 border-indigo-500"></div>
                                    </div>
                                @else
                                    <div class="flex-1 p-5">
                                        {{-- Task Title --}}
                                        <div class="mb-6">
                                            <h3 class="text-2xl font-bold text-white">{{ $selectedTask->title }}</h3>
                                            <div class="flex items-center text-sm text-gray-400 mt-2">
                                                <span class="flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" />
                                                    </svg>
                                                    {{ $selectedTask->column->name }}
                                                </span>
                                                @if ($this->canEditTasks())
                                                    <button
                                                        class="ml-4 text-indigo-400 hover:text-indigo-300 transition-colors duration-200 flex items-center"
                                                        wire:click="editTask({{ $selectedTask->id }})"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                        </svg>
                                                        Edit
                                                    </button>
                                                    <button
                                                        wire:click="confirmDeleteTask({{ $selectedTask->id }})"
                                                        class="ml-4 text-red-400 hover:text-red-300 transition-colors duration-200 flex items-center"
                                                    >
                                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                        Delete
                                                    </button>
                                                @endif
                                            </div>
                                        </div>

                                        {{-- Due Date --}}
                                        @if ($selectedTask->due_date)
                                            <div class="mb-5 bg-gray-800 p-4 rounded-lg border border-gray-700">
                                                <h4 class="text-sm font-medium text-gray-300 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                    </svg>
                                                    Due Date
                                                </h4>
                                                <div class="mt-2 text-base text-white">
                                                    {{ \Carbon\Carbon::parse($selectedTask->due_date)->format('M d, Y') }}
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Labels --}}
                                        @if ($selectedTask->labels->count() > 0)
                                            <div class="mb-5">
                                                <h4 class="text-sm font-medium text-gray-300 mb-2 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                                                    </svg>
                                                    Labels
                                                </h4>
                                                <div class="flex flex-wrap gap-2">
                                                    @foreach ($selectedTask->labels as $label)
                                                        <span class="px-3 py-1 text-sm rounded-full" style="background-color: {{ $label->color }}; color: {{ $this->getContrastColor($label->color) }}">
                                                            {{ $label->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Assignees --}}
                                        <div class="mb-5 bg-gray-800 p-4 rounded-lg border border-gray-700">
                                            <div class="flex justify-between items-center">
                                                <h4 class="text-sm font-medium text-gray-300 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                                    </svg>
                                                    Assignees
                                                </h4>
                                                @if ($this->canEditTasks())
                                                    <div class="relative" x-data="{ open: false }">
                                                        <button
                                                            class="text-indigo-400 hover:text-indigo-300 transition-colors duration-200 text-sm flex items-center"
                                                            @click="open = !open; $wire.openEmailsPopup()">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                                            </svg>
                                                            Add
                                                        </button>

                                                        <div
                                                            x-show="open"
                                                            @click.away="open = false"
                                                            class="absolute right-0 mt-2 w-64 bg-gray-800 shadow-lg rounded-lg border border-gray-700 z-50"
                                                            style="display: none;">
                                                            <div class="p-3">
                                                                <input
                                                                    type="text"
                                                                    placeholder="Search users..."
                                                                    class="w-full bg-gray-700 border-gray-600 rounded-md text-sm text-white focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50"
                                                                    wire:model.debounce.300ms="userSearch">
                                                            </div>
                                                            <div class="max-h-48 overflow-y-auto">
                                                                @foreach ($users as $user)
                                                                    <button
                                                                        class="w-full text-left px-3 py-2 hover:bg-gray-700 flex items-center transition-colors duration-200"
                                                                        wire:click="addAssigneeToTask({{ $user->id }}); $dispatch('close-popup')"
                                                                        @click="open = false">
                                                                        <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-6 h-6 rounded-full mr-2 border border-gray-600">
                                                                        <div>
                                                                        <div>
                                                                                <div class="text-gray-200">{{ $user->name }}</div>
                                                                                <div class="text-gray-400 text-xs">{{ $user->email }}</div>
                                                                            </div>
                                                                        </button>
                                                                    @endforeach

                                                                    @if (count($users) === 0)
                                                                        <div class="px-3 py-2 text-gray-400 text-sm">
                                                                            No users found
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                <div class="mt-3">
                                                    @forelse ($selectedTask->assignees as $assignee)
                                                        <div class="flex items-center justify-between mb-2 last:mb-0 py-2 px-3 bg-gray-750 rounded-lg">
                                                            <div class="flex items-center">
                                                                <img
                                                                    src="{{ $assignee->profile_photo_url }}"
                                                                    alt="{{ $assignee->name }}"
                                                                    class="w-8 h-8 rounded-full mr-3 border border-gray-700">
                                                                <div>
                                                                    <div class="text-white">{{ $assignee->name }}</div>
                                                                    <div class="text-gray-400 text-sm">{{ $assignee->email }}</div>
                                                                </div>
                                                            </div>

                                                            @if ($this->canEditTasks())
                                                                <button
                                                                    class="text-gray-400 hover:text-red-400 transition-colors duration-200"
                                                                    wire:click="removeAssigneeFromTask({{ $assignee->id }})">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                    </svg>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @empty
                                                        <div class="text-gray-400 text-sm py-2">
                                                            No assignees yet
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>

                                            {{-- Description --}}
                                            <div class="mt-5">
                                                <h4 class="text-sm font-medium text-gray-300 mb-3 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
                                                    </svg>
                                                    Description
                                                </h4>
                                                <div class="bg-gray-800 p-4 rounded-lg border border-gray-700 prose prose-sm prose-invert max-w-none">
                                                    @if ($selectedTask->description)
                                                        {!! nl2br(e($selectedTask->description)) !!}
                                                    @else
                                                        <p class="text-gray-400">No description provided</p>
                                                    @endif
                                                </div>
                                            </div>

                                            {{-- Comments Section --}}
                                            <div class="mt-8">
                                                <h4 class="text-sm font-medium text-gray-300 mb-4 flex items-center">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                                                    </svg>
                                                    Comments
                                                </h4>

                                                {{-- Add Comment --}}
                                                @if ($this->canManageProject())
                                                    <div class="mb-5">
                                                        <form wire:submit.prevent="addComment">
                                                            <div class="mb-3">
                                                                <textarea
                                                                    class="w-full rounded-lg bg-gray-800 border-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-500 focus:ring-opacity-50 transition-colors duration-200"
                                                                    rows="3"
                                                                    placeholder="Write a comment..."
                                                                    wire:model="commentText"
                                                                    required></textarea>
                                                            </div>
                                                            <div class="flex justify-end">
                                                                <button
                                                                    type="submit"
                                                                    class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 shadow-md transition-all duration-200 flex items-center text-sm">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                                    </svg>
                                                                    Add Comment
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                @endif

                                                {{-- Comments List --}}
                                                <div class="space-y-4">
                                                    @forelse ($selectedTask->comments as $comment)
                                                        <div class="bg-gray-800 rounded-lg p-4 border border-gray-700">
                                                            <div class="flex items-start">
                                                                <img
                                                                    src="{{ $comment->user->profile_photo_url }}"
                                                                    alt="{{ $comment->user->name }}"
                                                                    class="w-9 h-9 rounded-full mr-3 border border-gray-700">
                                                                <div class="flex-1">
                                                                    <div class="flex justify-between items-center mb-1">
                                                                        <div class="font-medium text-white">{{ $comment->user->name }}</div>
                                                                        <div class="text-xs text-gray-400">
                                                                            {{ $comment->created_at->diffForHumans() }}
                                                                        </div>
                                                                    </div>
                                                                    <div class="text-gray-200 text-sm">
                                                                        {!! nl2br(e($comment->body)) !!}
                                                                    </div>

                                                                    @if ($this->canManageProject($comment))
                                                                        <div class="mt-2 flex justify-end">
                                                                            <button
                                                                                class="text-gray-400 hover:text-red-400 text-xs flex items-center transition-colors duration-200"
                                                                                wire:click="deleteComment({{ $comment->id }})">
                                                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                                                </svg>
                                                                                Delete
                                                                            </button>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @empty
                                                        <div class="text-center text-gray-400 py-8">
                                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 mx-auto mb-2 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                                            </svg>
                                                            <p>No comments yet</p>
                                                        </div>
                                                    @endforelse
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    @endif

   {{-- Share Project Modal --}}
    @if ($showShareModal)
        <div class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50" x-data>
            <div class="bg-gray-800 rounded-lg w-full max-w-md mx-4" @click.away="$wire.closeShareModal()">
                <div class="p-4 border-b border-gray-700">
                    <div class="flex justify-between items-center">
                        <h3 class="text-lg font-medium text-white">Share Project</h3>
                        <button class="text-gray-400 hover:text-gray-300" wire:click="closeShareModal">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    @if (session()->has('message'))
                        <div class="bg-green-900 border-l-4 border-green-500 text-green-100 p-3 mb-4">
                            {{ session('message') }}
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="bg-red-900 border-l-4 border-red-500 text-red-100 p-3 mb-4">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form wire:submit.prevent="shareProject">
                        <div class="mb-4" x-data="{ open: false, users: [] }">
                            <label for="email" class="block text-sm font-medium text-gray-300 mb-1">Email Address</label>
                            <div class="relative">
                                <input
                                    type="text"
                                    id="email"
                                    class="w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                                    wire:model.live="shareForm.email"
                                    @focus="open = true; $wire.searchUsers($event.target.value)"
                                    @click="open = true; $wire.searchUsers($event.target.value)"
                                    required>
                                    
                                <div 
                                    x-show="open" 
                                    class="absolute z-10 mt-1 w-full bg-gray-700 border border-gray-600 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                    @foreach ($availableUsers as $user)
                                        <button
                                            type="button"
                                            class="block w-full text-left px-4 py-2 hover:bg-gray-600 focus:bg-gray-600 transition-colors duration-200"
                                            wire:click="selectUser('{{ $user->email }}')"
                                            @click="$wire.shareForm.email = '{{ $user->email }}'; open = false">
                                            <div class="flex items-center">
                                                <img src="{{ $user->profile_photo_url }}" alt="{{ $user->name }}" class="w-6 h-6 rounded-full mr-2">
                                                <div>
                                                    <div class="text-sm font-medium text-gray-200">{{ $user->name }}</div>
                                                    <div class="text-xs text-gray-400">{{ $user->email }}</div>
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                    
                                    @if (count($availableUsers) === 0 && !empty($shareForm['email']))
                                        <div class="px-4 py-2 text-sm text-gray-400">
                                            No matching users found
                                        </div>
                                    @endif
                                    
                                    @if (empty($shareForm['email']))
                                        <div class="px-4 py-2 text-sm text-gray-400">
                                            Type to search users
                                        </div>
                                    @endif
                                </div>
                            </div>
                            @error('shareForm.email') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="role" class="block text-sm font-medium text-gray-300 mb-1">Role</label>
                            <select
                                id="role"
                                class="w-full rounded-md bg-gray-700 border-gray-600 text-white shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50"
                                wire:model="shareForm.role">
                                <option value="admin">Admin</option>
                                <option value="member">Member</option>
                                <option value="viewer">Viewer</option>
                            </select>
                            @error('shareForm.role') <span class="text-red-400 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <div class="mt-6 flex justify-end space-x-3">
                            <button
                                type="button"
                                class="px-4 py-2 bg-gray-600 text-gray-200 rounded-md hover:bg-gray-500"
                                wire:click="closeShareModal">
                                Cancel
                            </button>
                            <button
                                type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-500">
                                Share
                            </button>
                        </div>
                    </form>

                    <div class="mt-6 border-t border-gray-700 pt-4">
                        <h4 class="text-sm font-medium text-gray-300 mb-2">Current Members</h4>
                        <div class="space-y-2">
                            @forelse ($project->members as $member)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <img src="{{ $member->profile_photo_url }}" alt="{{ $member->name }}" class="w-6 h-6 rounded-full mr-2">
                                        <div>
                                            <div class="text-sm font-medium text-gray-200">{{ $member->name }}</div>
                                            <div class="text-xs text-gray-400">{{ $member->pivot->role }}</div>
                                        </div>
                                    </div>
                                    @if ($member->id !== $project->owner->id)
                                        <button
                                            class="text-red-400 hover:text-red-300"
                                            wire:click="removeMember({{ $member->id }})"
                                            wire:confirm="Are you sure you want to remove {{ $member->name }} from the project?">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    @else
                                        <span class="text-xs bg-blue-900 text-blue-200 px-2 py-1 rounded-full">Owner</span>
                                    @endif
                                </div>
                            @empty
                                <p class="text-sm text-gray-400">No additional members.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
    <script>
        // Add a helper function to extract contrast color
        document.addEventListener('livewire:initialized', () => {
            @this.getContrastColor = function(hexColor) {
                // Remove # if present
                if (hexColor.startsWith('#')) {
                    hexColor = hexColor.slice(1);
                }

                // Convert to RGB
                let r = parseInt(hexColor.substr(0, 2), 16);
                let g = parseInt(hexColor.substr(2, 2), 16);
                let b = parseInt(hexColor.substr(4, 2), 16);

                // Calculate luminance
                let luminance = (0.299 * r + 0.587 * g + 0.114 * b) / 255;

                // Return black or white based on luminance
                return luminance > 0.5 ? '#000000' : '#FFFFFF';
            };
        });

        document.addEventListener('task-order-updated', event => {
            window.dispatchEvent(new CustomEvent('notify', {
                detail: {
                    message: event.detail.message || 'Task moved successfully',
                    type: 'success'
                }
            }));
        });

        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') {
                Livewire.dispatch('close-popups');
            }
        });

        window.addEventListener('close-popup', () => {
            const openPopups = document.querySelectorAll('[x-data]');
            openPopups.forEach(popup => {
                if (popup.__x && typeof popup.__x.getUnobservedData === 'function') {
                    const data = popup.__x.getUnobservedData();
                    if (data.open !== undefined) {
                        data.open = false;
                    }
                }
            });
        });
    </script>
</div>
