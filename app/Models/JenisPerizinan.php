<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPerizinan extends Model
{
    use HasFactory;

    protected $table = 'jenis_perizinans';

    protected $fillable = [
        'nama',
        'deskripsi',
        'memotong_kuota',
        'level_persetujuan_dibutuhkan'
    ];

    protected $casts = [
        'memotong_kuota' => 'boolean'
    ];

    /**
     * Relationship to Perizinan
     */
    public function perizinans()
    {
        return $this->hasMany(Perizinan::class, 'jenis');
    }

    /**
     * Scope untuk jenis yang memotong kuota
     */
    public function scopeMemotongKuota($query)
    {
        return $query->where('memotong_kuota', true);
    }

    /**
     * Get jenis perizinan untuk dropdown
     */
    public static function getForDropdown()
    {
        return self::orderBy('nama')->pluck('nama', 'id');
    }
}
