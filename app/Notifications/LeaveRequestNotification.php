<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeaveRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public LeaveRequest $leaveRequest,
        public string $type
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return match ($this->type) {
            'submitted' => (new MailMessage)
                ->subject('Pengajuan Cuti Baru')
                ->line('Pengajuan cuti baru telah diajukan oleh ' . $this->leaveRequest->user->name)
                ->line('Jenis: ' . $this->leaveRequest->leaveType->name)
                ->line('Periode: ' . $this->leaveRequest->start_date->format('d/m/Y') . ' - ' . $this->leaveRequest->end_date->format('d/m/Y'))
                ->line('Jumlah hari: ' . $this->leaveRequest->total_days . ' hari')
                ->action('Lihat Pengajuan', url('/admin/leave-requests/' . $this->leaveRequest->id)),
            'approved' => (new MailMessage)
                ->subject('Pengajuan Cuti Disetujui')
                ->line('Pengajuan cuti Anda telah disetujui.')
                ->line('Jenis: ' . $this->leaveRequest->leaveType->name)
                ->line('Periode: ' . $this->leaveRequest->start_date->format('d/m/Y') . ' - ' . $this->leaveRequest->end_date->format('d/m/Y')),
            'rejected' => (new MailMessage)
                ->subject('Pengajuan Cuti Ditolak')
                ->line('Pengajuan cuti Anda telah ditolak.')
                ->line('Alasan: ' . ($this->leaveRequest->rejection_reason ?? '-')),
            default => (new MailMessage)
                ->subject('Update Status Cuti')
                ->line('Status pengajuan cuti Anda telah diperbarui.'),
        };
    }
}
