<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class ProfileEditor extends Component
{
    use WithFileUploads;

    public $user;
    public $name;
    public $bio;
    public $location;
    public $profilePhoto;
    public $newProfilePhoto;
    public $isEditing = false;
    
    public function mount()
    {
        $this->user = auth()->user();
        $this->loadUserData();
    }

    public function loadUserData()
    {
        $this->name = $this->user->name;
        $this->bio = $this->user->bio;
        $this->location = $this->user->location;
        $this->profilePhoto = $this->user->profile_photo_path;
    }

    public function startEditing()
    {
        $this->isEditing = true;
    }

    public function cancelEditing()
    {
        $this->isEditing = false;
        $this->loadUserData();
        $this->newProfilePhoto = null;
    }

    public function saveProfile()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'newProfilePhoto' => 'nullable|image|max:1024',
        ]);

        if ($this->newProfilePhoto) {
            // Delete old profile photo if exists
            if ($this->user->profile_photo_path) {
                Storage::delete($this->user->profile_photo_path);
            }
            // Store new profile photo
            $photoPath = $this->newProfilePhoto->store('profile-photos', 'public');
            $this->user->profile_photo_path = $photoPath;
        }

        $this->user->name = $this->name;
        $this->user->bio = $this->bio;
        $this->user->location = $this->location;
        $this->user->save();

        $this->isEditing = false;
        $this->newProfilePhoto = null;
        
        session()->flash('message', 'Profile updated successfully!');
    }

    public function render()
    {
        return view('livewire.profile-editor');
    }
}