<?php
namespace App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource;
use App\Services\LeaveBalanceService;
use Filament\Resources\Pages\CreateRecord;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;
    protected function afterCreate(): void
    {
        app(LeaveBalanceService::class)->initializeBalance($this->record);
    }
}
