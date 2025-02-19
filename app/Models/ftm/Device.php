<?php

namespace App\Models\ftm;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;
    protected $connection = 'mysql_ftm';
    protected $table = 'device';
    protected $primaryKey = 'devid_auto';
    protected $guarded = ['devid_auto'];
    public $timestamps = false;
}
