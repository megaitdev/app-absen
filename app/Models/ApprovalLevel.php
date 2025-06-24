<?php

namespace App\Models;

use App\Models\mak_hrd\Unit;
use App\Models\mak_hrd\Divisi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApprovalLevel extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];
    
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relationship ke User (supervisor)
     */
    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_user_id');
    }

    /**
     * Relationship ke Unit
     */
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    /**
     * Relationship ke Divisi
     */
    public function divisi()
    {
        return $this->belongsTo(Divisi::class, 'divisi_id');
    }

    /**
     * Scope untuk approval level yang aktif
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk approval berdasarkan unit
     */
    public function scopeByUnit($query, $unitId)
    {
        return $query->where('unit_id', $unitId)->where('approval_type', 'unit');
    }

    /**
     * Scope untuk approval berdasarkan divisi
     */
    public function scopeByDivisi($query, $divisiId)
    {
        return $query->where('divisi_id', $divisiId)->where('approval_type', 'divisi');
    }

    /**
     * Get supervisor untuk employee berdasarkan unit/divisi
     */
    public static function getSupervisorForEmployee($employeeId)
    {
        $employee = \App\Models\mak_hrd\Employee::with(['unit', 'divisi'])->find($employeeId);
        
        if (!$employee) {
            return null;
        }

        // Cari supervisor berdasarkan unit terlebih dahulu
        if ($employee->unit) {
            $approvalLevel = self::active()
                ->byUnit($employee->unit->id)
                ->first();
                
            if ($approvalLevel) {
                return $approvalLevel->supervisor;
            }
        }

        // Jika tidak ada supervisor unit, cari berdasarkan divisi
        if ($employee->divisi) {
            $approvalLevel = self::active()
                ->byDivisi($employee->divisi->id)
                ->first();
                
            if ($approvalLevel) {
                return $approvalLevel->supervisor;
            }
        }

        return null;
    }
}
