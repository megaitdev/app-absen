<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // protected $fillable = [
    //     'nama',
    //     'username',
    //     'email',
    //     'nomor_wa',
    //     'password',
    // ];
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'nomor_wa_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
            'is_supervisor' => 'boolean',
            'is_hrd' => 'boolean',
            'supervised_units' => 'array',
            'supervised_divisis' => 'array',
        ];
    }

    /**
     * Relationship ke Employee
     */
    public function employee()
    {
        return $this->belongsTo(\App\Models\mak_hrd\Employee::class, 'employee_id');
    }

    /**
     * Relationship ke approval levels sebagai supervisor
     */
    public function approvalLevels()
    {
        return $this->hasMany(\App\Models\ApprovalLevel::class, 'supervisor_user_id');
    }

    /**
     * Check if user can approve for specific employee
     */
    public function canApproveForEmployee($employeeId)
    {
        if ($this->is_hrd) {
            return true;
        }

        if (!$this->is_supervisor) {
            return false;
        }

        $employee = \App\Models\mak_hrd\Employee::with(['unit', 'divisi'])->find($employeeId);

        if (!$employee) {
            return false;
        }

        // Check if supervisor for this employee's unit
        if ($employee->unit && in_array($employee->unit->id, $this->supervised_units ?? [])) {
            return true;
        }

        // Check if supervisor for this employee's divisi
        if ($employee->divisi && in_array($employee->divisi->id, $this->supervised_divisis ?? [])) {
            return true;
        }

        return false;
    }
}
