<?php

namespace App\Listeners;

use App\Events\LeaveRequestCreated;
use App\Models\User;
use App\Notifications\LeaveRequestNotification;

class NotifyManagerOnSubmission
{
    public function handle(LeaveRequestCreated $event): void
    {
        $leaveRequest = $event->leaveRequest;
        $employee = $leaveRequest->user;

        if ($employee->department && $employee->department->manager) {
            $employee->department->manager->notify(
                new LeaveRequestNotification($leaveRequest, 'submitted')
            );
        }

        User::where('role', 'hr_admin')->each(function ($hrAdmin) use ($leaveRequest) {
            $hrAdmin->notify(
                new LeaveRequestNotification($leaveRequest, 'submitted')
            );
        });
    }
}
