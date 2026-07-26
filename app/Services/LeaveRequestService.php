<?php

namespace App\Services;

use App\Enums\LeaveStatus;
use App\Events\LeaveRequestCreated;
use App\Exceptions\LeaveException;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class LeaveRequestService
{
    public function createLeaveRequest(User $user, array $data): LeaveRequest
    {
        $this->validateDates($data['start_date'], $data['end_date']);
        $this->validateNoPendingRequest($user->id);

        $totalDays = $this->calculateWorkingDays($data['start_date'], $data['end_date']);

        return DB::transaction(function () use ($user, $data, $totalDays) {
            $balance = LeaveBalance::where('user_id', $user->id)
                ->where('leave_type_id', $data['leave_type_id'])
                ->where('year', now()->year)
                ->lockForUpdate()
                ->first();

            if (! $balance || $balance->remaining_days < $totalDays) {
                throw LeaveException::insufficientBalance(
                    $balance?->leaveType?->name ?? 'cuti'
                );
            }

            $balance->update([
                'used_days' => $balance->used_days + $totalDays,
                'remaining_days' => $balance->remaining_days - $totalDays,
            ]);

            $attachmentPath = null;
            if (isset($data['attachment_path']) && $data['attachment_path']) {
                $attachmentPath = $data['attachment_path'];
            }

            $leaveRequest = LeaveRequest::create([
                'user_id' => $user->id,
                'leave_type_id' => $data['leave_type_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'total_days' => $totalDays,
                'reason' => $data['reason'],
                'status' => LeaveStatus::Pending,
                'attachment_path' => $attachmentPath,
            ]);

            ActivityLogService::log(
                $user->id,
                LeaveRequest::class,
                $leaveRequest->id,
                'created',
                null,
                $leaveRequest->toArray()
            );

            event(new LeaveRequestCreated($leaveRequest));

            return $leaveRequest;
        });
    }

    public function cancelLeaveRequest(User $user, LeaveRequest $leaveRequest): void
    {
        if ($leaveRequest->user_id !== $user->id) {
            throw LeaveException::cannotCancel();
        }

        if (! $leaveRequest->canBeCancelled()) {
            throw LeaveException::cannotCancel();
        }

        DB::transaction(function () use ($user, $leaveRequest) {
            $balance = LeaveBalance::where('user_id', $leaveRequest->user_id)
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

            $oldValues = $leaveRequest->toArray();
            $leaveRequest->update(['status' => LeaveStatus::Cancelled]);

            ActivityLogService::log(
                $user->id,
                LeaveRequest::class,
                $leaveRequest->id,
                'cancelled',
                $oldValues,
                $leaveRequest->toArray()
            );
        });
    }

    private function validateDates(string $startDate, string $endDate): void
    {
        if (strtotime($endDate) < strtotime($startDate)) {
            throw LeaveException::dateRangeInvalid();
        }
    }

    private function validateNoPendingRequest(int $userId): void
    {
        $hasPending = LeaveRequest::where('user_id', $userId)
            ->where('status', LeaveStatus::Pending)
            ->exists();

        if ($hasPending) {
            throw LeaveException::alreadyPending();
        }
    }

    public function calculateWorkingDays(string $startDate, string $endDate): int
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);
        $end->modify('+1 day');
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

        $days = 0;
        foreach ($period as $date) {
            $dayOfWeek = (int) $date->format('N');
            if ($dayOfWeek < 6) {
                $days++;
            }
        }

        return $days;
    }
}
