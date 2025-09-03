<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Models\mak_hrd\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;
    protected $guarded = ['id'];
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'nomor_wa_verified_at' => 'datetime',
            'password' => 'hashed',
            'role' => 'string',
            'is_supervisor' => 'boolean',
            'is_hrd' => 'boolean',
            'employees' => 'array',
        ];
    }

    public function employee()
    {
        return $this->belongsTo(\App\Models\mak_hrd\Employee::class, 'employee_id');
    }

    public function managedEmployees()
    {
        $employeesIDs = $this->employees ?? [];

        $employees = Employee::whereIn('id', $employeesIDs)
            ->with([
                'unit' => fn($query) => $query->select('hrd_units.id', 'hrd_units.unit'),
                'divisi' => fn($query) => $query->select('hrd_divisis.id', 'hrd_divisis.divisi'),
            ])
            ->get();

        return $employees->map(function ($employee) {
            return [
                'id' => $employee->id,
                'pin' => $employee->pin,
                'nip' => $employee->nip,
                'nama' => $employee->nama,
                'unit_id' => $employee->unit->id ?? null,
                'unit' => $employee->unit->unit ?? null,
                'divisi_id' => $employee->divisi->id ?? null,
                'divisi' => $employee->divisi->divisi ?? null,
            ];
        });
    }

    public function getManagedUnits()
    {
        return $this->managedEmployees()->pluck('unit', 'unit_id')->unique();
    }

    public function getManagedDivisis()
    {
        return $this->managedEmployees()->pluck('divisi', 'divisi_id')->unique();
    }
}
