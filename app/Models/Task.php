<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'status_id',
        'project_id',
        'start_date',
        'due_date',
        'description',
        'user_id',
        'workspace_id',
        'created_by',
                'task_department',
                'feedback'

    ];

    public function project()
    {

        return $this->belongsTo(Project::class, 'project_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }
    public function clients()
    {
        return $this->project->client;
    }
    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function getresult()
    {
        return substr($this->title, 0, 100);
    }

    public function getlink()
    {
        return str('/tasks/information/' . $this->id);
    }

    public function workspace()
    {
        return $this->belongsTo(Workspace::class);
    }
}
