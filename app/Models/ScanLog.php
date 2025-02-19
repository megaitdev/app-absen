<?php

namespace App\Models;

use App\Models\mak_hrd\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScanLog extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $table = 'scan_logs';
    protected $guarded = ['id'];

    //     protected $with = 'units';

    //     public function units()
    //     {
    //         return $this->belongsTo(Unit::class, 'unit_id', 'id');
    //     }
}
