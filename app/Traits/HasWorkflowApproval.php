<?php

namespace App\Traits;

use App\Models\ApprovalLevel;
use App\Models\User;
use App\Models\WorkflowHistory;
use App\Notifications\WorkflowApprovalNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

trait HasWorkflowApproval
{
    /**
     * Boot the trait
     */
    public static function bootHasWorkflowApproval()
    {
        // Auto set submitted_by dan submitted_at saat create
        static::creating(function ($model) {
            if (Auth::check()) {
                $model->submitted_by = Auth::id();
                $model->submitted_at = now();
                $model->status = 'pending';
            }
        });

        // Create workflow history saat model dibuat
        static::created(function ($model) {
            if (Auth::check()) {
                WorkflowHistory::createEntry(
                    $model,
                    'submitted',
                    Auth::id(),
                    'Pengajuan dibuat'
                );

                // Send notification to supervisor
                $model->sendNotificationToSupervisor('submitted');
            }
        });
    }

    /**
     * Relationship ke workflow histories
     */
    public function workflowHistories()
    {
        return $this->morphMany(WorkflowHistory::class, 'workflowable')
            ->orderBy('created_at', 'desc');
    }

    /**
     * Relationship ke user yang mengajukan
     */
    public function submittedBy()
    {
        return $this->belongsTo(User::class, 'submitted_by');
    }

    /**
     * Relationship ke supervisor yang approve
     */
    public function approvedBySupervisor()
    {
        return $this->belongsTo(User::class, 'approved_by_supervisor');
    }

    /**
     * Relationship ke HRD yang approve
     */
    public function approvedByHrd()
    {
        return $this->belongsTo(User::class, 'approved_by_hrd');
    }

    /**
     * Relationship ke employee
     */
    public function employee()
    {
        return $this->belongsTo(\App\Models\mak_hrd\Employee::class, 'employee_id');
    }

    /**
     * Check if can be approved by supervisor
     */
    public function canBeApprovedBySupervisor($userId = null)
    {
        $userId = $userId ?? Auth::id();

        if ($this->status !== 'pending') {
            return false;
        }

        $supervisor = ApprovalLevel::getSupervisorForEmployee($this->employee_id);

        return $supervisor && $supervisor->id === $userId;
    }

    /**
     * Check if can be approved by HRD
     */
    public function canBeApprovedByHrd($userId = null)
    {
        $userId = $userId ?? Auth::id();

        if ($this->status !== 'approved_supervisor') {
            return false;
        }

        $user = User::find($userId);

        return $user && $user->is_hrd;
    }

    /**
     * Approve by supervisor
     */
    public function approveBySupervisor($notes = null, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        if (!$this->canBeApprovedBySupervisor($userId)) {
            throw new \Exception('Tidak dapat disetujui oleh supervisor');
        }

        $this->update([
            'status' => 'approved_supervisor',
            'approved_by_supervisor' => $userId,
            'approved_supervisor_at' => now(),
            'supervisor_notes' => $notes,
        ]);

        WorkflowHistory::createEntry(
            $this,
            'approved_supervisor',
            $userId,
            $notes
        );

        // Send notification to HRD
        $this->sendNotificationToHrd('approved_supervisor', $notes);

        return $this;
    }

    /**
     * Approve by HRD
     */
    public function approveByHrd($notes = null, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        if (!$this->canBeApprovedByHrd($userId)) {
            throw new \Exception('Tidak dapat disetujui oleh HRD');
        }

        $this->update([
            'status' => 'approved_hrd',
            'approved_by_hrd' => $userId,
            'approved_hrd_at' => now(),
            'hrd_notes' => $notes,
        ]);

        WorkflowHistory::createEntry(
            $this,
            'approved_hrd',
            $userId,
            $notes
        );

        // Send notification to submitter and employee
        $this->sendNotificationToSubmitter('approved_hrd', $notes);

        return $this;
    }

    /**
     * Reject the request
     */
    public function reject($reason, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        if (!in_array($this->status, ['pending', 'approved_supervisor'])) {
            throw new \Exception('Tidak dapat ditolak');
        }

        $this->update([
            'status' => 'rejected',
            'rejection_reason' => $reason,
        ]);

        WorkflowHistory::createEntry(
            $this,
            'rejected',
            $userId,
            $reason
        );

        // Send notification to submitter
        $this->sendNotificationToSubmitter('rejected', $reason);

        return $this;
    }

    /**
     * Cancel the request
     */
    public function cancel($reason = null, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        if (!in_array($this->status, ['pending', 'approved_supervisor'])) {
            throw new \Exception('Tidak dapat dibatalkan');
        }

        $this->update([
            'status' => 'cancelled',
            'rejection_reason' => $reason,
        ]);

        WorkflowHistory::createEntry(
            $this,
            'cancelled',
            $userId,
            $reason
        );

        return $this;
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute()
    {
        $labels = [
            'pending' => 'Menunggu Persetujuan',
            'approved_supervisor' => 'Disetujui Atasan',
            'approved_hrd' => 'Disetujui HRD',
            'rejected' => 'Ditolak',
            'cancelled' => 'Dibatalkan',
        ];

        return $labels[$this->status] ?? $this->status;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        $classes = [
            'pending' => 'badge-warning',
            'approved_supervisor' => 'badge-info',
            'approved_hrd' => 'badge-success',
            'rejected' => 'badge-danger',
            'cancelled' => 'badge-secondary',
        ];

        return $classes[$this->status] ?? 'badge-secondary';
    }

    /**
     * Scope untuk status tertentu
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk pending approval
     */
    public function scopePendingApproval($query)
    {
        return $query->whereIn('status', ['pending', 'approved_supervisor']);
    }

    /**
     * Scope untuk approved
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved_hrd');
    }

    /**
     * Send notification to supervisor
     */
    protected function sendNotificationToSupervisor($action, $notes = null)
    {
        $supervisor = ApprovalLevel::getSupervisorForEmployee($this->employee_id);

        if ($supervisor) {
            $supervisor->notify(new WorkflowApprovalNotification(
                $this,
                $action,
                Auth::user(),
                $notes
            ));
        }
    }

    /**
     * Send notification to HRD
     */
    protected function sendNotificationToHrd($action, $notes = null)
    {
        $hrdUsers = User::where('is_hrd', true)->get();

        Notification::send($hrdUsers, new WorkflowApprovalNotification(
            $this,
            $action,
            Auth::user(),
            $notes
        ));
    }

    /**
     * Send notification to submitter
     */
    protected function sendNotificationToSubmitter($action, $notes = null)
    {
        if ($this->submittedBy) {
            $this->submittedBy->notify(new WorkflowApprovalNotification(
                $this,
                $action,
                Auth::user(),
                $notes
            ));
        }
    }
}
