<?php

namespace App\Models\ftm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    use HasFactory;
    protected $connection = 'mysql_ftm';
    protected $table = 'dept';
    protected $guarded = ['id'];
}
