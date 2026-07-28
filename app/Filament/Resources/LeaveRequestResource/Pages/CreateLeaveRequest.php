<?php

namespace App\Filament\Resources\LeaveRequestResource\Pages;

use App\Filament\Resources\LeaveRequestResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['total_days'] = \Carbon\Carbon::parse($data['start_date'])
            ->diffInDays(\Carbon\Carbon::parse($data['end_date'])) + 1;
        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Pengajuan cuti berhasil diajukan';
    }
}
