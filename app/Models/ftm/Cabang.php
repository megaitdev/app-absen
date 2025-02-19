<?php

namespace App\Models\ftm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;
    protected $connection = 'mysql_ftm';
    protected $table = 'cabang';
    protected $guarded = ['id'];
}
