<?php

namespace App\Services;

use App\Enums\LeaveStatus;
use App\Events\LeaveRequestApproved;
use App\Events\LeaveRequestRejected;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeaveApprovalService
{
    public function approve(LeaveRequest $leaveRequest, User $approver, ?string $notes = null): LeaveRequest
    {
        if (! $leaveRequest->isPending()) {
            return $leaveRequest->fresh();
        }

        DB::transaction(function () use ($leaveRequest, $approver, $notes) {
            $oldValues = $leaveRequest->toArray();

            $leaveRequest->update([
                'status' => LeaveStatus::Approved,
                'approved_by' => $approver->id,
                'approved_at' => now(),
            ]);

            ActivityLogService::log(
                $approver->id,
                LeaveRequest::class,
                $leaveRequest->id,
                'approved',
                $oldValues,
                $leaveRequest->toArray()
            );

            event(new LeaveRequestApproved($leaveRequest));

            $leaveRequest->user->notify(
                new \App\Notifications\LeaveRequestNotification($leaveRequest, 'approved')
            );
        });

        return $leaveRequest->fresh();
    }

    public function reject(LeaveRequest $leaveRequest, User $approver, string $reason): LeaveRequest
    {
        if (! $leaveRequest->isPending()) {
            return $leaveRequest->fresh();
        }

        DB::transaction(function () use ($leaveRequest, $approver, $reason) {
            $oldValues = $leaveRequest->toArray();

            $leaveRequest->update([
                'status' => LeaveStatus::Rejected,
                'approved_by' => $approver->id,
                'approved_at' => now(),
                'rejection_reason' => $reason,
            ]);

            $this->restoreBalance($leaveRequest);

            ActivityLogService::log(
                $approver->id,
                LeaveRequest::class,
                $leaveRequest->id,
                'rejected',
                $oldValues,
                $leaveRequest->toArray()
            );

            event(new LeaveRequestRejected($leaveRequest));

            $leaveRequest->user->notify(
                new \App\Notifications\LeaveRequestNotification($leaveRequest, 'rejected')
            );
        });

        return $leaveRequest->fresh();
    }

    private function restoreBalance(LeaveRequest $leaveRequest): void
    {
        $balance = \App\Models\LeaveBalance::where('user_id', $leaveRequest->user_id)
            ->where('leave_type_id', $leaveRequest->leave_type_id)
            ->where('year', $leaveRequest->start_date->year)
            ->lockForUpdate()
            ->first();

        if ($balance) {
            $balance->update([
                'used_days' => max(0, $balance->used_days - $leaveRequest->total_days),
                'remaining_days' => $balance->remaining_days + $leaveRequest->total_days,
            ]);
        }
    }
}
