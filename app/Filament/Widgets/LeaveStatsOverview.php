<?php

namespace App\Filament\Widgets;

use App\Enums\LeaveStatus;
use App\Models\LeaveRequest;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LeaveStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $user = auth()->user();

        $query = LeaveRequest::query();

        if ($user->isManager()) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        $pending = (clone $query)->where('status', LeaveStatus::Pending)->count();
        $approved = (clone $query)->where('status', LeaveStatus::Approved)->count();
        $rejected = (clone $query)->where('status', LeaveStatus::Rejected)->count();
        $totalEmployees = User::where('role', 'employee')->count();

        return [
            Stat::make('Menunggu Persetujuan', $pending)
                ->description('Pengajuan pending')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
            Stat::make('Disetujui', $approved)
                ->description('Total disetujui')
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('success'),
            Stat::make('Ditolak', $rejected)
                ->description('Total ditolak')
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
            Stat::make('Total Karyawan', $totalEmployees)
                ->description('Karyawan aktif')
                ->descriptionIcon('heroicon-m-users')
                ->color('info'),
        ];
    }
}
