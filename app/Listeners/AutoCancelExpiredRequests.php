<?php

namespace App\Listeners;

use App\Enums\LeaveStatus;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;

class AutoCancelExpiredRequests
{
    public function handle(object $event): void
    {
        $pendingRequests = LeaveRequest::where('status', LeaveStatus::Pending)
            ->where('created_at', '<=', now()->subDays(7))
            ->get();

        foreach ($pendingRequests as $request) {
            \Illuminate\Support\Facades\DB::transaction(function () use ($request) {
                $balance = LeaveBalance::where('user_id', $request->user_id)
                    ->where('leave_type_id', $request->leave_type_id)
                    ->where('year', $request->start_date->year)
                    ->lockForUpdate()
                    ->first();

                if ($balance) {
                    $balance->update([
                        'used_days' => max(0, $balance->used_days - $request->total_days),
                        'remaining_days' => $balance->remaining_days + $request->total_days,
                    ]);
                }

                $request->update(['status' => LeaveStatus::Cancelled]);

                \App\Services\ActivityLogService::log(
                    $request->user_id,
                    LeaveRequest::class,
                    $request->id,
                    'cancelled_by_system',
                    $request->toArray(),
                    $request->fresh()->toArray()
                );
            });
        }
    }
}
