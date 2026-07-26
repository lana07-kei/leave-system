<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LeaveRequestResource\Pages;
use App\Models\LeaveRequest;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class LeaveRequestResource extends Resource
{
    protected static ?string $model = LeaveRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Pengajuan Cuti';

    protected static ?string $modelLabel = 'Pengajuan Cuti';

    protected static ?string $pluralModelLabel = 'Pengajuan Cuti';

    public static function canViewAny(): bool
    {
        return ! auth()->user()->isEmployee();
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.department.name')
                    ->label('Departemen')
                    ->searchable(),
                Tables\Columns\TextColumn::make('leaveType.name')
                    ->label('Jenis Cuti')
                    ->sortable(),
                Tables\Columns\TextColumn::make('start_date')
                    ->label('Dari')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('end_date')
                    ->label('Sampai')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_days')
                    ->label('Hari')
                    ->sortable(),
                Tables\Columns\TextColumn::make('reason')
                    ->label('Alasan')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state->label())
                    ->color(fn ($state) => $state->color()),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Diajukan')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'cancelled' => 'Dibatalkan',
                    ]),
                Tables\Filters\SelectFilter::make('leaveType')
                    ->relationship('leaveType', 'name')
                    ->label('Jenis Cuti'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Pengajuan Cuti')
                    ->modalSubmitActionLabel('Setujui')
                    ->visible(fn (LeaveRequest $record) => $record->isPending() && ! auth()->user()->isEmployee())
                    ->action(fn (LeaveRequest $record) => app(\App\Services\LeaveApprovalService::class)->approve($record, auth()->user())),
                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Pengajuan Cuti')
                    ->form([
                        Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->modalSubmitActionLabel('Tolak')
                    ->visible(fn (LeaveRequest $record) => $record->isPending() && ! auth()->user()->isEmployee())
                    ->action(function (LeaveRequest $record, array $data) {
                        app(\App\Services\LeaveApprovalService::class)
                            ->reject($record, auth()->user(), $data['rejection_reason']);
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLeaveRequests::route('/'),
            'view' => Pages\ViewLeaveRequest::route('/{record}'),
        ];
    }
}
