<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Perizinan extends Model
{
    use HasFactory;

    protected $table = 'perizinans';

    protected $fillable = [
        'employee_id',
        'submitted_by_user_id',
        'jenis',
        'tanggal_mulai',
        'tanggal_selesai',
        'jumlah_hari',
        'durasi',
        'alasan',
        'lampiran',
        'status',
        'level_persetujuan_saat_ini',
        'riwayat_persetujuan',
        'ditolak_oleh_user_id',
        'komentar_penolakan',
        'tanggal_ditolak',
        'tanggal_disetujui_hr'
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'jumlah_hari' => 'decimal:2',
        'durasi' => 'decimal:0',
        'riwayat_persetujuan' => 'array',
        'tanggal_ditolak' => 'datetime',
        'tanggal_disetujui_hr' => 'datetime'
    ];

    /**
     * Relationship to Employee
     */
    public function employee()
    {
        return $this->belongsTo(\App\Models\mak_hrd\Employee::class, 'employee_id');
    }

    /**
     * Relationship to User (pengaju)
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by_user_id');
    }

    /**
     * Relationship to JenisPerizinan
     */
    public function jenisPerizinan()
    {
        return $this->belongsTo(JenisPerizinan::class, 'jenis');
    }

    /**
     * Relationship to User (yang menolak)
     */
    public function ditolakOleh()
    {
        return $this->belongsTo(User::class, 'ditolak_oleh_user_id');
    }

    /**
     * Scope untuk filter berdasarkan status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter berdasarkan jenis
     */
    public function scopeJenis($query, $jenis)
    {
        return $query->where('jenis', $jenis);
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $colors = [
            'pending' => 'warning',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            'dibatalkan' => 'secondary'
        ];

        return $colors[$this->status] ?? 'secondary';
    }
}
