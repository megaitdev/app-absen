<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PosisiHrd extends Model
{
    use HasFactory;
    protected $connection = 'mysql_hrd';
    protected $table = 'posisis';
    protected $guarded = ['id'];
}
