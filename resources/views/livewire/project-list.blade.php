<div class="py-8">
   @if (session()->has('message'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 3000)" 
             x-show="show"
             class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Top Card With Filters and Search -->
        <div class="bg-gray-800 shadow-xl shadow-blue-900/10 sm:rounded-lg mb-8 border border-gray-700 transition-all duration-300 hover:shadow-lg hover:shadow-blue-900/20">
            <div class="px-6 py-5 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-900">
                <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        @if($user->id === Auth::id())
                            <div>
                                <select wire:change="$set('filter', $event.target.value)" class="bg-gray-800 text-sm font-medium text-gray-300 border border-gray-600 rounded-lg pr-10 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-400 transition duration-200 ease-in-out">
                                    <option value="all" @selected($filter === 'all')>All</option>
                                    <option value="public" @selected($filter === 'public')>Public</option>
                                    <option value="private" @selected($filter === 'private')>Private</option>
                                </select>
                            </div>
                        @endif
                        <div class="flex rounded-xl bg-gray-800 shadow-lg p-1 max-w-fit">
                            <button wire:click="sort('name')" 
                                class="relative px-2.5 py-2.5 text-sm font-medium transition-all duration-300 rounded-lg flex items-center justify-center min-w-[7.5rem] gap-2
                                    {{ $sortBy === 'name' 
                                        ? 'text-white shadow-md bg-gradient-to-br from-blue-500 to-indigo-600' 
                                        : 'text-gray-200 hover:bg-gray-700' }}">
                                <span class="font-medium">Name</span>
                                @if($sortBy === 'name')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform duration-200 {{ $sortDirection === 'asc' ? 'rotate-0' : 'rotate-180' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </button>
                            
                            <button wire:click="sort('updated_at')"
                                class="relative px-4 py-2.5 text-sm font-medium transition-all duration-300 rounded-lg flex items-center justify-center min-w-[9rem] gap-2
                                    {{ $sortBy === 'updated_at' 
                                        ? 'text-white shadow-md bg-gradient-to-br from-blue-500 to-indigo-600' 
                                        : 'text-gray-200 hover:bg-gray-700' }}">
                                <span class="font-medium">Last Update</span>
                                @if($sortBy === 'updated_at')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform duration-200 {{ $sortDirection === 'asc' ? 'rotate-0' : 'rotate-180' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </button>
                            
                            <button wire:click="sort('created_at')"
                                class="relative px-4 py-2.5 text-sm font-medium transition-all duration-300 rounded-lg flex items-center justify-center min-w-[9rem] gap-2
                                    {{ $sortBy === 'created_at' 
                                        ? 'text-white shadow-md bg-gradient-to-br from-blue-500 to-indigo-600' 
                                        : 'text-gray-200 hover:bg-gray-700' }}">
                                <span class="font-medium">Created Date</span>
                                @if($sortBy === 'created_at')
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 transform transition-transform duration-200 {{ $sortDirection === 'asc' ? 'rotate-0' : 'rotate-180' }}" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" />
                                    </svg>
                                @endif
                            </button>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <div class="relative w-full sm:w-64 group">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 group-focus-within:text-blue-500 transition-colors duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text"
                                wire:model.live.300ms="search" 
                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-600 rounded-lg bg-gray-700 placeholder-gray-400 text-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300 shadow-sm"
                                placeholder="Find a project...">
                        </div>
                        
                        @if($user->id === Auth::id())
                            <button 
                                wire:click="openCreateModal"
                                class="inline-flex items-center px-4 py-2.5 text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 shadow-md hover:shadow-lg">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                New Project
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Projects List -->
        <div class="bg-gray-800 shadow-xl shadow-blue-900/10 sm:rounded-lg border border-gray-700 transition-all duration-300 hover:shadow-lg hover:shadow-blue-900/20">
            <div class="px-6 py-4 border-b border-gray-700 bg-gradient-to-r from-gray-800 to-gray-900 flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                </svg>
                <h3 class="text-lg font-medium text-white">{{ count($projects) }} projects</h3>
            </div>
            
            <ul class="divide-y divide-gray-700">
            @forelse ($projects as $project)
                @continue(!$project->is_public && $project->user_id === auth()->id())
                <li class="p-6 hover:bg-gray-700/70 transition-colors duration-300 group">
                    <div class="flex items-start justify-between">
                        <div class="flex-1">
                            <div class="flex items-center flex-wrap gap-2">
                                <h4 class="text-lg font-medium text-blue-400 group-hover:text-blue-300 transition-colors duration-300">
                                    <a 
                                    href="{{ route('project.view', [$project->owner->name, $project->name]) }}" 
                                    class="flex items-center">
                                        {{ $project->name }}
                                    </a>
                                </h4>
                                <span class="px-2.5 py-0.5 text-xs font-medium bg-gray-700 text-gray-200 rounded-full flex items-center shadow-inner">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $project->is_public ? 'bg-green-500' : 'bg-blue-500' }} mr-1.5"></span>
                                    {{ $project->is_public ? 'Public' : 'Private' }}
                                </span>
                            </div>
                            <div class="mt-4 flex flex-wrap items-center text-xs gap-4">
                                <span class="flex items-center text-gray-400 group-hover:text-gray-300 transition-colors duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="w-3 mr-1.5 fill-current text-zinc-500">
                                        <path d="M224 256A128 128 0 1 0 224 0a128 128 0 1 0 0 256zm-45.7 48C79.8 304 0 383.8 0 482.3C0 498.7 13.3 512 29.7 512l388.6 0c16.4 0 29.7-13.3 29.7-29.7C448 383.8 368.2 304 269.7 304l-91.4 0z"/>
                                    </svg>
                                    {{ $project->owner->name }}
                                </span>
                                <span class="flex items-center text-gray-400 group-hover:text-gray-300 transition-colors duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" class="mr-1.5 w-4 h-4 fill-current text-zinc-500">
                                        <path d="M152 64c0 8.8-7.2 16-16 16s-16-7.2-16-16V32c0-17.7-14.3-32-32-32S56 14.3 56 32V64H48C21.5 64 0 85.5 0 112V464c0 26.5 21.5 48 48 48H400c26.5 0 48-21.5 48-48V112c0-26.5-21.5-48-48-48h-8V32c0-17.7-14.3-32-32-32s-32 14.3-32 32V64c0 8.8-7.2 16-16 16s-16-7.2-16-16V32H152V64zM400 192v272H48V192H400z"/>
                                    </svg>
                                    Created on {{ $project->created_at->format('j F, Y') }}

                                </span>
                                <span class="flex items-center text-gray-400 group-hover:text-gray-300 transition-colors duration-300">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 mr-1 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Updated {{ $project->updated_at->diffForHumans() }}
                                </span>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 ml-4">
                           @if($user->id === Auth::id())
                                @php
                                    $member = $project->members->firstWhere('id', auth()->id());
                                    $isPinned = $member?->pivot?->is_pinned ?? false;
                                @endphp
                                <button 
                                    wire:click="togglePin({{ $project->id }})" 
                                    class="p-2 rounded-full {{ $isPinned ? 'bg-yellow-900/30 text-yellow-500' : 'text-gray-400 hover:text-gray-300 hover:bg-gray-700' }}"
                                    title="{{ $isPinned ? 'Unpin' : 'Pin' }}"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 384 512" class="w-4 h-4 fill-current rotate-45"><!--!Font Awesome Free 6.7.2 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2025 Fonticons, Inc.--><path d="M32 32C32 14.3 46.3 0 64 0L320 0c17.7 0 32 14.3 32 32s-14.3 32-32 32l-29.5 0 11.4 148.2c36.7 19.9 65.7 53.2 79.5 94.7l1 3c3.3 9.8 1.6 20.5-4.4 28.8s-15.7 13.3-26 13.3L32 352c-10.3 0-19.9-4.9-26-13.3s-7.7-19.1-4.4-28.8l1-3c13.8-41.5 42.8-74.8 79.5-94.7L93.5 64 64 64C46.3 64 32 49.7 32 32zM160 384l64 0 0 96c0 17.7-14.3 32-32 32s-32-14.3-32-32l0-96z"/></svg>
                                </button>
                                
                                <!-- Kebab Menu -->
                               <div x-data="{ open: false }" class="relative" @click.outside="open = false">
                                    <button @click="open = !open" class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-300 hover:bg-gray-700 cursor-pointer transition-all duration-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    
                                    <!-- Dropdown menu -->
                                    <div x-show="open" x-cloak
                                        x-transition:enter="transition ease-out duration-100" 
                                        x-transition:enter-start="transform opacity-0 scale-95" 
                                        x-transition:enter-end="transform opacity-100 scale-100" 
                                        x-transition:leave="transition ease-in duration-75" 
                                        x-transition:leave-start="transform opacity-100 scale-100" 
                                        x-transition:leave-end="transform opacity-0 scale-95" 
                                        class="absolute right-0 mt-2 w-48 bg-gray-800 rounded-md shadow-lg py-1 ring-1 ring-black ring-opacity-5 z-50">
                                        
                                        <button wire:click="openEditModal({{ $project->id }})" class="w-full text-left block px-4 py-2 text-sm text-gray-200 hover:bg-gray-700 transition-colors duration-200">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit Project
                                            </div>
                                        </button>
                                        
                                        <button wire:click="confirmDelete({{ $project->id }})" class="w-full text-left block px-4 py-2 text-sm text-red-400 hover:bg-red-900/20 transition-colors duration-200">
                                            <div class="flex items-center">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2 text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete Project
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </li>
            @empty
                <li class="p-12 flex flex-col items-center justify-center text-center">
                    <div class="h-16 w-16 bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-2">No projects found</h3>
                    <p class="text-gray-400 max-w-sm mb-6">You don't have any projects that match your current filters. Try adjusting your search or create a new project.</p>
                    
                    @if($user->id === Auth::id())
                        <button 
                            wire:click="openCreateModal"
                            class="inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 shadow-md hover:shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Create Your First Project
                        </button>
                    @endif
                </li>
            @endforelse            
            </ul>
        </div>
    </div>
    
    <!-- Create/Edit Project Modal -->
    @if($showModal)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50">
            <div class="bg-gray-800 rounded-lg shadow-xl transform transition-all max-w-lg w-full" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100" 
                x-transition:leave-end="opacity-0 scale-95">
                
                <div class="border-b border-gray-700 px-6 py-4 bg-gradient-to-r from-gray-800 to-gray-900">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-gray-100">
                            {{ $modalMode === 'create' ? 'Create New Project' : 'Edit Project' }}
                        </h3>
                        <button wire:click="closeModal" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <form wire:submit="saveProject">
                    <div class="px-6 py-4">
                        <div class="space-y-4">
                            <!-- Project Name -->
                            <div>
                                <label for="projectName" class="block text-sm font-medium text-gray-300">Project Name</label>
                                <input type="text" id="projectName" wire:model="projectName" 
                                    class="mt-1 block w-full border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-gray-700 text-white transition-all duration-200">
                                @error('projectName') 
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Project Description -->
                            <div>
                                <label for="projectDescription" class="block text-sm font-medium text-gray-300">Description (Optional)</label>
                                <textarea id="projectDescription" wire:model="projectDescription" rows="4" 
                                    class="mt-1 block w-full border-gray-600 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-gray-700 text-white transition-all duration-200"></textarea>
                                @error('projectDescription') 
                                    <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="w-full space-y-2">
                                <!-- Public Option -->
                                <label 
                                    class="{{ $isPublic ? 'border-blue-500' : 'border-transparent' }} relative flex cursor-pointer rounded-lg border-2 hover:bg-gray-700 p-4 transition"
                                    wire:click="$set('isPublic', true)">
                                    
                                    <div class="flex items-start gap-4 w-full">
                                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-700 text-gray-300">
                                            <!-- Eye Icon -->
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S3.732 16.057 2.458 12z" />
                                            </svg>
                                        </div>

                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-semibold text-gray-100">Public</span>
                                            </div>
                                            <p class="text-sm text-gray-400">Anyone on the internet can see this project.</p>
                                        </div>
                                    </div>
                                </label>

                                <!-- Private Option -->
                                <label 
                                    class="{{ $isPublic ? 'border-transparent' : 'border-blue-500' }} relative flex cursor-pointer rounded-lg border-2 hover:bg-gray-700 p-4 transition"
                                    wire:click="$set('isPublic', false)">
                                    
                                    <div class="flex items-start gap-4 w-full">
                                        <div class="flex items-center justify-center w-10 h-10 rounded-full bg-gray-700 text-gray-300">
                                            <!-- Lock Icon -->
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                            </svg>
                                        </div>

                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <span class="font-semibold text-gray-100">Private</span>
                                            </div>
                                            <p class="text-sm text-gray-400">Only invited members can access this project.</p>
                                        </div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gray-900/30 px-6 py-4 flex justify-end space-x-3 border-t border-gray-700">
                        <button type="button" wire:click="closeModal" class="inline-flex items-center px-4 py-2 border border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-200 bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                            Cancel
                        </button>
                        <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300">
                            {{ $modalMode === 'create' ? 'Create Project' : 'Save Changes' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
    
    <!-- Delete Confirmation Modal -->
    @if($showDeleteConfirmation)
        <div class="fixed inset-0 bg-gray-600 bg-opacity-75 flex items-center justify-center p-4 z-50">
            <div x-show="open" class="bg-gray-800 rounded-lg shadow-xl transform transition-all max-w-md w-full" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200" 
                x-transition:leave-start="opacity-100 scale-100" 
                x-transition:leave-end="opacity-0 scale-95">
                
                <div class="bg-gray-800 px-6 py-4 rounded-t-lg border-b border-gray-700">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-medium text-red-400 flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Delete Project
                        </h3>
                        <button wire:click="cancelDelete" class="text-gray-400 hover:text-gray-500 focus:outline-none">
                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div class="px-6 py-4">
                    <p class="text-sm text-gray-300">
                        Are you sure you want to delete the project <span class="font-semibold">{{ $projectToDelete->name }}</span>? This action cannot be undone, and all associated data will be permanently removed.
                    </p>
                </div>
                
                <div class="bg-gray-900/30 px-6 py-4 flex justify-end space-x-3 rounded-b-lg border-t border-gray-700">
                    <button type="button" wire:click="cancelDelete" class="inline-flex items-center px-4 py-2 border border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-200 bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                        Cancel
                    </button>
                    <button type="button" wire:click="deleteProject" class="inline-flex items-center px-4 py-2 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>