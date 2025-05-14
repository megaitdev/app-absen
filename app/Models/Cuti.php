<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $guarded = ['id'];

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic');
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_cuti');
    }
}
