<?php

namespace App\Models\mak_hrd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;
    protected $connection = 'mysql_hrd';
    protected $guarded = ['id'];

    public function report_employees()
    {
        return $this->hasOneThrough(
            Employee::class,
            Posisi::class,
            'unit_id', // Foreign key on the positions table...
            'id', // Foreign key on the employees table...
            'id', // Local key on the units table...
            'employee_id' // Local key on the positions table...
        )->where('status', 1);
    }
}
