<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    protected $fillable = ['name', 'project_id', 'position', 'created_by'];
    
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
    
    public function tasks()
    {
        return $this->hasMany(Task::class)->orderBy('position');
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
