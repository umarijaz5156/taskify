<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaskUser extends Model
{
    use HasFactory;

    public $table = 'task_user';

    protected $fillable = [
        'task_id', 'user_id'
    ];
}
