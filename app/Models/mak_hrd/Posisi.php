<?php

namespace App\Models\mak_hrd;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posisi extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'hrd_posisis';
    protected $guarded = ['id'];
}
