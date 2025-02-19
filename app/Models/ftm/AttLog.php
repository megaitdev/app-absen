<?php

namespace App\Models\ftm;

use App\Models\mak_hrd\Divisi;
use App\Models\mak_hrd\Employee;
use App\Models\mak_hrd\Posisi;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttLog extends Model
{
    use HasFactory;
    protected $connection = 'mysql_ftm';
    protected $table = 'att_log';
    protected $primaryKey = 'att_id';
    protected $guarded = ['att_id'];

    protected $dates = ['scan_date'];
    public $timestamps = false;

    function employee()
    {
        return $this->belongsTo(EmployeeFtm::class, 'pin', 'pin')->select('alias', 'pin', 'nik');
    }

    public function cabang()
    {
        return $this->hasOneThrough(
            Cabang::class,
            EmployeeFtm::class,
            'pin',
            'cab_id_auto',
            'pin',
            'cab_id_auto'
        );
    }

    public function departemen()
    {
        return $this->hasOneThrough(
            Departemen::class,
            EmployeeFtm::class,
            'pin',
            'dept_id_auto',
            'pin',
            'dept_id_auto'
        );
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'sn', 'sn')->select('sn', 'device_name');
    }
}
