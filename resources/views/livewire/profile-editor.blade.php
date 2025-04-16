<div>
    <!-- Flash Message -->
    @if (session()->has('message'))
        <div x-data="{ show: true }" 
             x-init="setTimeout(() => show = false, 3000)" 
             x-show="show"
             class="fixed top-4 right-4 bg-green-500 text-white px-4 py-2 rounded shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif

    <!-- Profile Card -->
    <div class="bg-gray-800 shadow-lg border border-gray-700 sm:rounded-lg overflow-hidden">
        <div class="p-6">
            <div class="flex flex-col items-center text-center mb-6">
                <!-- Profile Photo -->
                <div class="relative group">
                    @if($isEditing)
                        <div class="mb-4">
                            @if($newProfilePhoto)
                                <img class="h-48 w-48 rounded-full object-cover border-2 border-blue-500" 
                                    src="{{ $newProfilePhoto->temporaryUrl() }}" 
                                    alt="{{ $user->name }}">
                            @elseif($user->profile_photo_path)
                                <img class="h-48 w-48 rounded-full object-cover border-2 border-blue-500" 
                                    src="{{ Storage::url($user->profile_photo_path) }}" 
                                    alt="{{ $user->name}}">
                            @else
                                <div class="h-48 w-48 rounded-full bg-gradient-to-br from-blue-600 to-purple-700 flex items-center justify-center text-2xl font-bold text-white shadow-md">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                            @endif
                            
                            <label for="photo-upload" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded-full opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                            </label>
                            <input id="photo-upload" type="file" wire:model="newProfilePhoto" class="hidden" accept="image/*">
                        </div>
                    @else
                        @if($user->profile_photo_path)
                            <img class="h-48 w-48 rounded-full object-cover border-2 border-blue-500 mb-4" 
                                src="{{ Storage::url($user->profile_photo_path) }}" 
                                alt="{{ $user->name }}">
                        @else
                            <div class="h-48 w-48 rounded-full bg-gradient-to-br from-blue-600 to-purple-700 flex items-center justify-center mb-4 text-2xl font-bold text-white shadow-md">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                        @endif
                    @endif
                </div>
                
              
                <h3 class="text-xl font-bold text-white">{{ $user->name }}</h3>
                
                <div>
                    @if ($isEditing)
                        <div class="mb-3">
                            <h2 class="text-white font-semibold mb-2 text-left">Bio</h2>
                            <textarea wire:model="bio"
                                    rows="4"
                                    class="w-full resize-y bg-gray-700 border border-gray-600 rounded-md px-3 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm"
                                    placeholder="Tell us about yourself..."></textarea>
                        </div>
                    @else
                        <div class="flex justify-between items-start">
                            <p class="text-gray-400 text-sm whitespace-pre-wrap">{{ $user->bio ?: 'No bio provided.' }}</p>
                        </div>
                    @endif
                </div> 
                <!-- Edit/Save/Cancel Buttons -->
                @if($user->id === auth()->id())
                    <div class="mt-4">
                        @if($isEditing)
                            <div class="flex space-x-2">
                                <button wire:click="saveProfile" 
                                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white tracking-widest hover:bg-blue-500 active:bg-blue-700 focus:outline-none focus:border-blue-700 focus:ring focus:ring-blue-300 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    Save
                                </button>
                                <button wire:click="cancelEditing" 
                                        class="inline-flex items-center px-4 py-2 bg-gray-700 border border-gray-600 rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-600 active:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 transition">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancel
                                </button>
                            </div>
                        @else
                            <button wire:click="startEditing" 
                                    class="inline-flex items-center px-4 py-2 bg-gray-700 border border-gray-600 rounded-md font-semibold text-xs text-white tracking-widest hover:bg-gray-600 active:bg-gray-700 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 transition">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                                Edit Profile
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            <div class="border-t border-gray-700 pt-4"> 
                <!-- Location Field -->
                <div class="flex items-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    @if($isEditing)
                        <input type="text" wire:model="location" placeholder="Location" 
                               class="w-full bg-gray-700 border border-gray-600 rounded-md px-3 py-1 text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent text-sm">
                    @else
                        <span class="text-sm text-gray-300">{{ $user->location ?: 'Add location' }}</span>
                    @endif
                </div>
                
                <!-- Email (not editable) -->
                <div class="flex items-center pb-20">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span class="text-sm text-gray-300">{{ $user->email }}</span>
                </div>                
            </div>
        </div>
    </div>
</div>