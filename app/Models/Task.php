<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    protected $fillable = ['title', 'description', 'due_date', 'column_id', 'position', 'created_by'];
    
    public function column()
    {
        return $this->belongsTo(Column::class);
    }
    
    public function labels()
    {
        return $this->belongsToMany(Label::class);
    }
    
    public function assignees()
    {
        return $this->belongsToMany(User::class);
    }
    
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    
    public function comments()
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }
}
