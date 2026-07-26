<?php

namespace App\Services;

use App\Models\LeaveBalance;
use App\Models\LeaveType;
use App\Models\User;

class LeaveBalanceService
{
    public function initializeBalance(User $user, int $year = null): void
    {
        $year = $year ?? now()->year;
        $leaveTypes = LeaveType::where('is_active', true)->get();

        foreach ($leaveTypes as $leaveType) {
            LeaveBalance::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'leave_type_id' => $leaveType->id,
                    'year' => $year,
                ],
                [
                    'total_days' => $leaveType->days_allowed,
                    'remaining_days' => $leaveType->days_allowed,
                ]
            );
        }
    }

    public function getBalance(User $user, int $leaveTypeId, int $year = null): ?LeaveBalance
    {
        return LeaveBalance::where('user_id', $user->id)
            ->where('leave_type_id', $leaveTypeId)
            ->where('year', $year ?? now()->year)
            ->first();
    }

    public function resetAllBalances(int $year): void
    {
        $leaveTypes = LeaveType::where('is_active', true)->get();
        $users = User::where('role', 'employee')->get();

        foreach ($users as $user) {
            foreach ($leaveTypes as $leaveType) {
                LeaveBalance::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'leave_type_id' => $leaveType->id,
                        'year' => $year,
                    ],
                    [
                        'total_days' => $leaveType->days_allowed,
                        'used_days' => 0,
                        'remaining_days' => $leaveType->days_allowed,
                    ]
                );
            }
        }
    }
}
