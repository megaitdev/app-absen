<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WorkflowHistory extends Model
{
    use HasFactory;
    
    protected $guarded = ['id'];
    
    protected $casts = [
        'metadata' => 'array',
    ];

    /**
     * Relationship ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Polymorphic relationship ke model yang di-workflow
     */
    public function workflowable()
    {
        return $this->morphTo();
    }

    /**
     * Scope untuk action tertentu
     */
    public function scopeByAction($query, $action)
    {
        return $query->where('action', $action);
    }

    /**
     * Scope untuk model tertentu
     */
    public function scopeForModel($query, $modelType, $modelId)
    {
        return $query->where('workflowable_type', $modelType)
                    ->where('workflowable_id', $modelId);
    }

    /**
     * Create workflow history entry
     */
    public static function createEntry($model, $action, $userId, $notes = null, $metadata = null)
    {
        return self::create([
            'workflowable_type' => get_class($model),
            'workflowable_id' => $model->id,
            'action' => $action,
            'user_id' => $userId,
            'notes' => $notes,
            'metadata' => $metadata,
        ]);
    }

    /**
     * Get action label
     */
    public function getActionLabelAttribute()
    {
        $labels = [
            'submitted' => 'Diajukan',
            'approved_supervisor' => 'Disetujui Atasan',
            'approved_hrd' => 'Disetujui HRD',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
        ];

        return $labels[$this->action] ?? $this->action;
    }
}
