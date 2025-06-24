<?php

namespace App\Models;

use App\Traits\HasWorkflowApproval;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerifikasiAbsen extends Model
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
}
