<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeLaporanHarian extends Model
{
    use HasFactory;
    protected $connection = 'mysql_lh';
    protected $table = 'emp';
    protected $guarded = ['id'];
}
