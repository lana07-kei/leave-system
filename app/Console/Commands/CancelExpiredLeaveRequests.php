<?php

namespace App\Console\Commands;

use App\Enums\LeaveStatus;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use Illuminate\Console\Command;

class CancelExpiredLeaveRequests extends Command
{
    protected $signature = 'leave:cancel-expired';
    protected $description = 'Auto-cancel pending leave requests older than 7 days';

    public function handle(): int
    {
        $expired = LeaveRequest::where('status', LeaveStatus::Pending)
            ->where('created_at', '<=', now()->subDays(7))
            ->get();

        $count = 0;

        foreach ($expired as $request) {
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

            $request->update(['status' => LeaveStatus::Cancelled]);
            $count++;
        }

        $this->info("{$count} pengajuan cuti expired telah dibatalkan.");

        return Command::SUCCESS;
    }
}
