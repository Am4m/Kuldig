<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Left Column - User Profile -->
                <div class="md:col-span-1">
                    @if($user->id === Auth::id())
                        @livewire('profile-editor')
                    @else
                        <!-- Original profile display for other users -->
                        <div class="bg-gray-800 shadow-xl border border-gray-700 sm:rounded-lg overflow-hidden transition-all duration-300 hover:shadow-blue-900/20 hover:shadow-lg">
                            <div class="p-6">
                                <div class="flex flex-col items-center text-center mb-6">
                                    @if($user->profile_photo_path)
                                        <div class="relative mb-4">
                                            <div class="absolute inset-0 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 blur opacity-30 animate-pulse"></div>
                                            <img class="relative h-52 w-52 rounded-full object-cover border-2 border-blue-500 p-1 bg-gray-900" 
                                                 src="{{ Storage::url($user->profile_photo_path) }}" 
                                                 alt="{{ $user->name }}">
                                        </div>
                                    @else
                                        <div class="relative mb-4">
                                            <div class="absolute inset-0 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 blur opacity-30 animate-pulse"></div>
                                            <div class="relative h-52 w-52 rounded-full bg-gradient-to-br from-blue-600 to-purple-700 flex items-center justify-center text-3xl font-bold text-white shadow-lg">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                        </div>
                                    @endif
                                    <h3 class="text-2xl font-bold text-white mb-1">{{ $user->name }}</h3>
                                    <p class="text-gray-400 text-sm px-3 py-1 bg-gray-700/50 rounded-full">{{$user->bio}}</p>
                                </div>

                                <div class="border-t border-gray-700 pt-5">        
                                    @if($user->location)
                                    <div class="flex items-center mb-3 group">
                                        <div class="p-2 rounded-full bg-gray-700/30 mr-3 group-hover:bg-blue-500/20 transition-colors duration-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                        </div>
                                        <span class="text-sm text-gray-300">{{ $user->location }}</span>
                                    </div>
                                    @endif
                                    
                                    <div class="flex items-center group">
                                        <div class="p-2 rounded-full bg-gray-700/30 mr-3 group-hover:bg-blue-500/20 transition-colors duration-300">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <span class="text-sm text-gray-300">{{ $user->email }}</span>
                                    </div>
                                        
                                    <div class="flex justify-around mt-6 pt-5 border-t border-gray-700">
                                        <div class="text-center group cursor-pointer hover:scale-105 transition-transform duration-300">
                                            <span class="block text-xl font-bold text-white group-hover:text-blue-400">{{ $user->followers_count ?? 0 }}</span>
                                            <span class="text-xs text-gray-400 group-hover:text-gray-300">Followers</span>
                                        </div>
                                        <div class="text-center group cursor-pointer hover:scale-105 transition-transform duration-300">
                                            <span class="block text-xl font-bold text-white group-hover:text-blue-400">{{ $user->following_count ?? 0 }}</span>
                                            <span class="text-xs text-gray-400 group-hover:text-gray-300">Following</span>
                                        </div>
                                        <div class="text-center group cursor-pointer hover:scale-105 transition-transform duration-300">
                                            <span class="block text-xl font-bold text-white group-hover:text-blue-400">{{ $user->projects_count ?? 0 }}</span>
                                            <span class="text-xs text-gray-400 group-hover:text-gray-300">Projects</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Right Column - Content -->
                <div class="md:col-span-3 space-y-8">
                    <!-- Projects Section -->
                    <div class="bg-gray-800 shadow-xl border border-gray-700 sm:rounded-lg overflow-hidden transition-all duration-300 hover:shadow-blue-900/20 hover:shadow-lg">
                        <div class="px-6 py-4 border-b border-gray-700 flex justify-between items-center bg-gradient-to-r from-gray-800 to-gray-900">
                            <h3 class="text-lg font-medium text-white flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                </svg>
                                Last Updated Projects
                            </h3>
                            <a href="{{ route('projects.view', ['name' => $user->name]) }}" class="text-sm text-blue-400 hover:text-blue-300 transition-colors duration-300 flex items-center group">
                                View all
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                        <div class="px-6 py-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                @php
                                    $projects = $user->projects()
                                        ->where('is_public', true)
                                        ->orderByDesc('updated_at')
                                        ->get();
                                @endphp
                                @if(isset($projects) && count($projects) > 0)
                                    @foreach($projects->take(2) as $project)
                                        <div class="bg-gray-700/30 rounded-lg p-4 hover:bg-gray-700/50 transition-colors duration-300 border border-gray-700 group hover:border-blue-500/30">
                                            <a href="#" class="text-blue-400 no-underline hover:underline text-xl font-bold">{{ $project->name }}</a>
                                            <p class="text-gray-400 text-sm mt-1 line-clamp-2">{{ $project->description }}</p>
                                            <div class="mt-3 flex justify-between items-center">
                                                <span class="text-xs text-gray-500">Updated {{ $project->updated_at->diffForHumans() }}</span>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-span-2 p-12 flex flex-col items-center justify-center text-center">
                                        <div class="h-16 w-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No projects found</h3>
                                        <p class="text-gray-500 dark:text-gray-400 max-w-sm mb-6">You don't have any projects that match your current filters. Try adjusting your search or create a new project.</p>
                                        
                                        @if($user->id === Auth::id())
                                            <button 
                                                wire:click="openCreateModal"
                                                class="inline-flex items-center px-5 py-2.5 text-sm font-medium rounded-lg text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-300 shadow-md hover:shadow-lg">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                                </svg>
                                                Create Your First Project
                                            </button>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>