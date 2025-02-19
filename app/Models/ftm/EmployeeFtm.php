<?php

namespace App\Models\ftm;

use App\Models\mak_hrd\Employee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeFtm extends Model
{
    use HasFactory;

    protected $connection = 'mysql_ftm';
    protected $table = 'emp';
    // protected $guarded = ['emp_id_auto'];
    protected $primaryKey = 'emp_id_auto';
    public $timestamps = false;
    protected $fillable = [
        'alias',
        'nik',
        'pin',
        'cab_id_auto',
        'dept_id_auto',
        'lastupdate_date',
    ];

    protected $dates = [
        'lastupdate_date'
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class, 'cab_id_auto', 'cab_id_auto');
    }
    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'dept_id_auto', 'dept_id_auto');
    }

    public function is_sync()
    {
        return $this->hasOne(Employee::class, 'nip', 'nik')->where('is_deleted', 0);
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'nip', 'nik')->select('id');
    }


    public static function isNikExistInEmployee($nik)
    {
        return Employee::where('nip', $nik)->exists();
    }
}
