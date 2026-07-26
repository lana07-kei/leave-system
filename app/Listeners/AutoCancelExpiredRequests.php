<?php

namespace App\Listeners;

use App\Models\LeaveBalance;
use App\Models\LeaveType;

class AutoCancelExpiredRequests
{
    public function handle(object $event): void
    {
        $pendingRequests = \App\Models\LeaveRequest::where('status', \App\Enums\LeaveStatus::Pending)
            ->where('created_at', '<=', now()->subDays(7))
            ->get();

        foreach ($pendingRequests as $request) {
            $balance = LeaveBalance::where('user_id', $request->user_id)
                ->where('leave_type_id', $request->leave_type_id)
                ->where('year', $request->start_date->year)
                ->first();

            if ($balance) {
                $balance->update([
                    'used_days' => max(0, $balance->used_days - $request->total_days),
                    'remaining_days' => $balance->remaining_days + $request->total_days,
                ]);
            }

            $request->update(['status' => \App\Enums\LeaveStatus::Cancelled]);
        }
    }
}
