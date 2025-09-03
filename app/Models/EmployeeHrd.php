<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeHrd extends Model
{
    use HasFactory;
    protected $connection = 'mysql_hrd';
    protected $table = 'employees';
    protected $guarded = ['id'];
}
