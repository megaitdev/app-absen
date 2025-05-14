<?php

namespace App\Models;

use App\Models\mak_hrd\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $guarded = ['id'];
    protected $casts = [
        'scan_masuk_murni' => 'datetime',
        'scan_keluar_murni' => 'datetime',
        'scan_masuk_efektif' => 'datetime',
        'scan_keluar_efektif' => 'datetime',

    ];

    protected function serializeDate($date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'pin', 'pin');
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
    }

    public function shiftDate()
    {
        $shift = $this->belongsTo(Shift::class, 'shift_id', 'id')->first();
        if ($shift) {
            $shift->jam_masuk = $this->date . ' ' . $shift->jam_masuk;
            $shift->jam_keluar = $this->date . ' ' . $shift->jam_keluar;
            $shift->jam_mulai_istirahat = $this->date . ' ' . $shift->jam_mulai_istirahat;
            $shift->jam_selesai_istirahat = $this->date . ' ' . $shift->jam_selesai_istirahat;
        }
        return $shift;
    }


    public function scanMasuk()
    {
        return $this->scan_masuk_murni ? $this->scan_masuk_murni->format('H:i') : null;
    }
    public function scanKeluar()
    {
        return $this->scan_keluar_murni ? $this->scan_keluar_murni->format('H:i') : null;
    }

    public function durasiMurni()
    {
        return $this->jam_kerja_murni ? sprintf('%01d:%02d', intdiv($this->jam_kerja_murni, 60), $this->jam_kerja_murni % 60) : 0;
    }
    public function durasiEfektif()
    {
        return $this->jam_kerja_efektif ? sprintf('%01d:%02d', intdiv($this->jam_kerja_efektif, 60), $this->jam_kerja_efektif % 60) : 0;
    }

    public function jamHilangMurni()
    {
        return $this->jam_hilang_murni ? sprintf('%01d:%02d', intdiv($this->jam_hilang_murni, 60), $this->jam_hilang_murni % 60) : 0;
    }
    public function jamHilangEfektif()
    {
        return $this->jam_hilang_efektif ? sprintf('%01d:%02d', intdiv($this->jam_hilang_efektif, 60), $this->jam_hilang_efektif % 60) : 0;
    }
    public function potongan()
    {
        return $this->potongan ? sprintf('%01d:%02d', intdiv($this->potongan, 60), $this->potongan % 60) : 0;
    }
    public function lemburMurni()
    {
        return $this->lembur_murni ? sprintf('%01d:%02d', intdiv($this->lembur_murni, 60), $this->lembur_murni % 60) : 0;
    }
    public function lemburEfektif()
    {
        return $this->lembur_efektif ? sprintf('%01d:%02d', intdiv($this->lembur_efektif, 60), $this->lembur_efektif % 60) : 0;
    }
    public function lemburAkumulasi()
    {
        return $this->lembur_akumulasi ? sprintf('%01d:%02d', intdiv($this->lembur_akumulasi, 60), $this->lembur_akumulasi % 60) : 0;
    }

    public function hasVerifikasi()
    {
        return $this->hasOne(VerifikasiAbsen::class, 'date', 'date')->where('employee_id', $this->employee_id)->first();
    }
    public function verifikasi()
    {
        return $this->belongsTo(VerifikasiAbsen::class, 'date', 'date');
    }
    public function cuti()
    {
        return $this->belongsTo(Cuti::class, 'date', 'date');
    }
    public function lembur()
    {
        return $this->belongsTo(Lembur::class, 'date', 'date');
    }
    public function izin()
    {
        return $this->belongsTo(Izin::class, 'date', 'date');
    }
}
