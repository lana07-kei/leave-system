<?php

namespace App\Filament\Widgets;

use App\Enums\LeaveStatus;
use App\Models\LeaveRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class RecentLeaveRequests extends TableWidget
{
    protected static ?int $sort = 1;

    protected static ?string $heading = 'Pengajuan Terbaru';

    public function table(Table $table): Table
    {
        $user = auth()->user();
        $query = LeaveRequest::with(['user', 'leaveType']);

        if ($user->isManager()) {
            $query->whereHas('user', function ($q) use ($user) {
                $q->where('department_id', $user->department_id);
            });
        }

        return $table
            ->query($query)
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('leaveType.name')
                    ->label('Jenis'),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Mulai')
                    ->date('d/m/Y'),
                Tables\Columns\TextColumn::make('total_days')
                    ->label('Hari'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => $state->color()),
            ])
            ->defaultSort('created_at', 'desc')
            ->paginated([5]);
    }
}
