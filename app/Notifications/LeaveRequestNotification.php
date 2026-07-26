<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use App\Models\User;
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
        $mail = (new MailMessage)
            ->subject(match ($this->type) {
                'submitted' => 'Pengajuan Cuti Baru - ' . $this->leaveRequest->user->name,
                'approved' => 'Pengajuan Cuti Disetujui',
                'rejected' => 'Pengajuan Cuti Ditolak',
                default => 'Update Status Cuti',
            })
            ->view('emails.leave-notification', [
                'leaveRequest' => $this->leaveRequest,
                'type' => $this->type,
                'manager' => $notifiable,
            ]);

        return $mail;
    }
}
