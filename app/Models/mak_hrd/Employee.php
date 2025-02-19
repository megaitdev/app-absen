<?php

namespace App\Models\mak_hrd;

use App\Models\mak_hrd\Unit;
use App\Models\ScanLog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;
    protected $connection = 'mysql_hrd';
    protected $table = 'employees';
    protected $guarded = ['id'];


    public function posisi()
    {
        return $this->hasOne(Posisi::class, 'employee_id', 'id')->where('status', 1);
    }
    public function unit()
    {
        return $this->hasOneThrough(
            Unit::class,
            Posisi::class,
            'employee_id',
            'id',
            'id',
            'unit_id'
        )->where('posisis.status', 1);
    }

    public function divisi()
    {
        return $this->hasOneThrough(
            Divisi::class,
            Posisi::class,
            'employee_id',
            'id',
            'id',
            'divisi_id'
        )->where('posisis.status', 1);
    }

    public function pangkat()
    {
        return $this->hasOne(Pangkat::class, 'id', 'pangkat_id');
    }


    public function scan_logs(): HasMany
    {
        return $this->hasMany(ScanLog::class, 'pin', 'pin');
    }
}
