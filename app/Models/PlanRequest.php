<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'plan_id',
        'client_id',
        'is_approved',
    ];

    public function plan(){
        return $this->belongsTo(Plan::class);
    }
    public function client(){
        return $this->belongsTo(Client::class);
    }
}
