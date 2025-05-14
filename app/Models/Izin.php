<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Izin extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic');
    }
    public function jenisIzin()
    {
        return $this->belongsTo(JenisIzin::class, 'jenis_izin');
    }
}
