<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
    ];

    
    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'project_member')
            ->withPivot('is_pinned')
            ->withTimestamps();
    }

    public function isPinned()
    {
        if (!auth()->check() || !$this->members) {
            return false;
        }
        
        $userMembership = $this->members->where('id', auth()->id())->first();
        
        return $userMembership && $userMembership->pivot->is_pinned;
    }

    public function columns()
    {
        return $this->hasMany(Column::class)->orderBy('position');
    }

    public function labels()
    {
        return $this->hasMany(Label::class);
    }

    public function activities()
    {
        return $this->hasMany(ProjectActivity::class)->latest();
    }

}