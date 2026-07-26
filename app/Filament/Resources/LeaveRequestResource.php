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

    protected static ?string $navigationGroup = 'Menu Utama';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Pengajuan Cuti';

    protected static ?string $pluralModelLabel = 'Pengajuan Cuti';

    public static function form(Form $form): Form
    {
        $user = auth()->user();

        return $form
            ->schema([
                Forms\Components\Section::make('Detail Pengajuan')
                    ->schema([
                        Forms\Components\Select::make('leave_type_id')
                            ->label('Jenis Cuti')
                            ->relationship('leaveType', 'name')
                            ->required(),
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y'),
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Tanggal Akhir')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->afterOrEqual('start_date'),
                        Forms\Components\Textarea::make('reason')
                            ->label('Alasan')
                            ->required()
                            ->rows(3),
                        Forms\Components\FileUpload::make('attachment_path')
                            ->label('Dokumen Pendukung')
                            ->disk('public')
                            ->directory('attachments')
                            ->maxSize(2048)
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'application/pdf'])
                            ->nullable(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $user = auth()->user();
        $query = parent::getEloquentQuery();

        if ($user->isEmployee()) {
            $query->where('user_id', $user->id);
        } elseif ($user->isManager()) {
            $query->whereHas('user', fn ($q) => $q->where('department_id', $user->department_id));
        }

        return $query;
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
                    ->visible(fn (LeaveRequest $record) => $record->isPending() && (auth()->user()->isAdmin() || auth()->user()->isManager()))
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
                    ->visible(fn (LeaveRequest $record) => $record->isPending() && (auth()->user()->isAdmin() || auth()->user()->isManager()))
                    ->action(function (LeaveRequest $record, array $data) {
                        app(\App\Services\LeaveApprovalService::class)
                            ->reject($record, auth()->user(), $data['rejection_reason']);
                    }),
                Tables\Actions\Action::make('cancel')
                    ->label('Batalkan')
                    ->icon('heroicon-o-x-mark')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Batalkan Pengajuan')
                    ->modalSubmitActionLabel('Ya, Batalkan')
                    ->visible(fn (LeaveRequest $record) => $record->isPending() && $record->user_id === auth()->id())
                    ->action(function (LeaveRequest $record) {
                        app(\App\Services\LeaveRequestService::class)
                            ->cancelLeaveRequest(auth()->user(), $record);
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
            'create' => Pages\CreateLeaveRequest::route('/create'),
            'view' => Pages\ViewLeaveRequest::route('/{record}'),
        ];
    }
}
