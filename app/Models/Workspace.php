<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Workspace extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'user_id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function clients()
    {
        return $this->belongsToMany(Client::class);
    }

    public function getresult()
    {
        return substr($this->title, 0, 100);
    }

    public function getlink()
    {
        return str('/workspaces');
    }
    public function projects()
    {
        return $this->hasMany(Project::class);
    }
    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
    public function meetings()
    {
        return $this->hasMany(Meeting::class);
    }

    public function todos()
    {
        return $this->hasMany(Todo::class);
    }

    public function leave_requests()
    {
        return $this->hasMany(LeaveRequest::class);
    }
    public function payslips()
    {
        return $this->hasMany(Payslip::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function contract_types()
    {
        return $this->hasMany(ContractType::class)->orWhereNull('workspace_id');
    }

    public function payment_methods()
    {
        return $this->hasMany(PaymentMethod::class)->orWhereNull('workspace_id');
    }
    public function allowances()
    {
        return $this->hasMany(Allowance::class);
    }
    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }
    public function timesheets()
    {
        return $this->hasMany(TimeTracker::class);
    }
}
