<?php

namespace App\Models;

use App\Traits\HasWorkflowApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cuti extends Model
{
    use HasFactory, HasWorkflowApproval;

    protected $connection = 'mysql';
    protected $guarded = ['id'];

    protected $casts = [
        'submitted_at' => 'datetime',
        'approved_supervisor_at' => 'datetime',
        'approved_hrd_at' => 'datetime',
    ];

    public function pic()
    {
        return $this->belongsTo(User::class, 'pic');
    }

    public function jenisCuti()
    {
        return $this->belongsTo(JenisCuti::class, 'jenis_cuti');
    }
}
