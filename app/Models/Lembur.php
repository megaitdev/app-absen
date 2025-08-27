<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lembur extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $guarded = ['id'];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_supervisor_at' => 'datetime',
        'approved_hrd_at' => 'datetime',
        'lampiran' => 'array', // Cast JSON to array for multiple files
    ];

    protected $fillable = [
        'employee_id',
        'date',
        'mulai_lembur',
        'selesai_lembur',
        'keterangan',
        'lampiran',
        'lembur',
        'status',
        'submitted_by',
        'submitted_at',
        'approved_by_supervisor',
        'approved_supervisor_at',
        'approved_by_hrd',
        'approved_hrd_at',
        'group_id',
        'is_team_lead',
        'data_scan',
        'pic'
    ];

    /**
     * Relationship to user who submitted (PIC)
     */
    public function pic()
    {
        return $this->belongsTo(User::class, 'pic');
    }

    /**
     * Get status badge HTML
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'pending' => '<span class="badge badge-warning">Menunggu Persetujuan</span>',
            'approved_supervisor' => '<span class="badge badge-info">Disetujui Supervisor</span>',
            'approved' => '<span class="badge badge-success">Disetujui</span>',
            'rejected' => '<span class="badge badge-danger">Ditolak</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge badge-secondary">Tidak Diketahui</span>';
    }

    /**
     * Get formatted duration
     */
    public function getDurationAttribute()
    {
        if (!$this->mulai_lembur || !$this->selesai_lembur) {
            return '-';
        }

        $start = \Carbon\Carbon::parse($this->mulai_lembur);
        $end = \Carbon\Carbon::parse($this->selesai_lembur);

        $diff = $end->diff($start);

        return $diff->format('%h jam %i menit');
    }

    /**
     * Scope for group overtime
     */
    public function scopeByGroup($query, $groupId)
    {
        return $query->where('group_id', $groupId);
    }

    /**
     * Scope for team lead overtime
     */
    public function scopeTeamLead($query)
    {
        return $query->where('is_team_lead', true);
    }

    /**
     * Get team members for this overtime request
     */
    public function getTeamMembersAttribute()
    {
        if (!$this->group_id) {
            return collect([$this]);
        }

        return static::where('group_id', $this->group_id)
            ->with('employee')
            ->get();
    }
}
