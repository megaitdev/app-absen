<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WorkflowApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $workflowItem;
    protected $action;
    protected $actionBy;
    protected $notes;

    /**
     * Create a new notification instance.
     */
    public function __construct($workflowItem, $action, $actionBy, $notes = null)
    {
        $this->workflowItem = $workflowItem;
        $this->action = $action;
        $this->actionBy = $actionBy;
        $this->notes = $notes;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via($notifiable)
    {
        $channels = ['mail'];
        
        // Add WhatsApp channel if user has verified WhatsApp number
        if ($notifiable->nomor_wa_verified_at && $notifiable->nomor_wa) {
            $channels[] = 'whatsapp'; // Custom channel
        }
        
        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $subject = $this->getEmailSubject();
        $greeting = "Halo {$notifiable->nama},";
        
        $mailMessage = (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line($this->getEmailContent())
            ->line($this->getWorkflowDetails());

        // Add action button based on the action
        if (in_array($this->action, ['submitted', 'approved_supervisor'])) {
            $mailMessage->action('Lihat Detail', route('workflow.dashboard'));
        }

        if ($this->notes) {
            $mailMessage->line("Catatan: {$this->notes}");
        }

        $mailMessage->line('Terima kasih telah menggunakan aplikasi absensi kami.');

        return $mailMessage;
    }

    /**
     * Get WhatsApp message content
     */
    public function toWhatsApp($notifiable)
    {
        $message = $this->getEmailSubject() . "\n\n";
        $message .= $this->getEmailContent() . "\n\n";
        $message .= $this->getWorkflowDetails();
        
        if ($this->notes) {
            $message .= "\n\nCatatan: {$this->notes}";
        }
        
        $message .= "\n\nSilakan buka aplikasi untuk melihat detail.";
        
        return $message;
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray($notifiable)
    {
        return [
            'workflow_type' => class_basename($this->workflowItem),
            'workflow_id' => $this->workflowItem->id,
            'action' => $this->action,
            'action_by' => $this->actionBy->nama,
            'employee_name' => $this->workflowItem->employee->nama ?? 'N/A',
            'date' => $this->workflowItem->date,
            'notes' => $this->notes,
        ];
    }

    /**
     * Get email subject based on action
     */
    private function getEmailSubject()
    {
        $type = $this->getWorkflowTypeName();
        
        switch ($this->action) {
            case 'submitted':
                return "Pengajuan {$type} Baru";
            case 'approved_supervisor':
                return "Pengajuan {$type} Disetujui Atasan";
            case 'approved_hrd':
                return "Pengajuan {$type} Disetujui HRD";
            case 'rejected':
                return "Pengajuan {$type} Ditolak";
            case 'cancelled':
                return "Pengajuan {$type} Dibatalkan";
            default:
                return "Update Pengajuan {$type}";
        }
    }

    /**
     * Get email content based on action
     */
    private function getEmailContent()
    {
        $type = $this->getWorkflowTypeName();
        $employeeName = $this->workflowItem->employee->nama ?? 'N/A';
        $actionByName = $this->actionBy->nama;
        
        switch ($this->action) {
            case 'submitted':
                return "Pengajuan {$type} untuk karyawan {$employeeName} telah diajukan oleh {$actionByName} dan menunggu persetujuan Anda.";
            case 'approved_supervisor':
                return "Pengajuan {$type} untuk karyawan {$employeeName} telah disetujui oleh atasan ({$actionByName}) dan menunggu persetujuan HRD.";
            case 'approved_hrd':
                return "Pengajuan {$type} untuk karyawan {$employeeName} telah disetujui oleh HRD ({$actionByName}).";
            case 'rejected':
                return "Pengajuan {$type} untuk karyawan {$employeeName} telah ditolak oleh {$actionByName}.";
            case 'cancelled':
                return "Pengajuan {$type} untuk karyawan {$employeeName} telah dibatalkan oleh {$actionByName}.";
            default:
                return "Pengajuan {$type} untuk karyawan {$employeeName} telah diupdate oleh {$actionByName}.";
        }
    }

    /**
     * Get workflow details
     */
    private function getWorkflowDetails()
    {
        $details = "Detail Pengajuan:\n";
        $details .= "- Karyawan: " . ($this->workflowItem->employee->nama ?? 'N/A') . "\n";
        $details .= "- Tanggal: " . \Carbon\Carbon::parse($this->workflowItem->date)->format('d/m/Y') . "\n";
        
        // Add specific details based on workflow type
        if ($this->workflowItem instanceof \App\Models\Cuti) {
            $details .= "- Jenis Cuti: " . ($this->workflowItem->jenisCuti->cuti ?? 'N/A') . "\n";
        } elseif ($this->workflowItem instanceof \App\Models\Izin) {
            $details .= "- Jenis Izin: " . ($this->workflowItem->jenisIzin->izin ?? 'N/A') . "\n";
            if (!$this->workflowItem->is_full_day) {
                $details .= "- Waktu: {$this->workflowItem->mulai_izin} - {$this->workflowItem->selesai_izin}\n";
            }
        } elseif ($this->workflowItem instanceof \App\Models\Lembur) {
            $details .= "- Jenis Lembur: " . ucfirst($this->workflowItem->lembur) . "\n";
            $details .= "- Waktu: {$this->workflowItem->mulai_lembur} - {$this->workflowItem->selesai_lembur}\n";
        } elseif ($this->workflowItem instanceof \App\Models\VerifikasiAbsen) {
            $details .= "- Jenis: " . ($this->workflowItem->jenis ?? 'N/A') . "\n";
            if ($this->workflowItem->jam_masuk) {
                $details .= "- Jam Masuk: {$this->workflowItem->jam_masuk}\n";
            }
            if ($this->workflowItem->jam_keluar) {
                $details .= "- Jam Keluar: {$this->workflowItem->jam_keluar}\n";
            }
        }
        
        if ($this->workflowItem->keterangan) {
            $details .= "- Keterangan: {$this->workflowItem->keterangan}\n";
        }
        
        return $details;
    }

    /**
     * Get workflow type name
     */
    private function getWorkflowTypeName()
    {
        $className = class_basename($this->workflowItem);
        
        switch ($className) {
            case 'Cuti':
                return 'Cuti';
            case 'Izin':
                return 'Izin';
            case 'Lembur':
                return 'Lembur';
            case 'VerifikasiAbsen':
                return 'Verifikasi Absen';
            default:
                return $className;
        }
    }
}
