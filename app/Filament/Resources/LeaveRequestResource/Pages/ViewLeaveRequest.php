<?php

namespace App\Filament\Resources\LeaveRequestResource\Pages;

use App\Filament\Resources\LeaveRequestResource;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewLeaveRequest extends ViewRecord
{
    protected static string $resource = LeaveRequestResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detail Pengajuan')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('Karyawan')
                            ->icon('heroicon-m-user'),
                        Infolists\Components\TextEntry::make('user.department.name')
                            ->label('Departemen')
                            ->icon('heroicon-m-building-office'),
                        Infolists\Components\TextEntry::make('leaveType.name')
                            ->label('Jenis Cuti'),
                        Infolists\Components\TextEntry::make('start_date')
                            ->label('Tanggal Mulai')
                            ->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('end_date')
                            ->label('Tanggal Akhir')
                            ->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('total_days')
                            ->label('Jumlah Hari'),
                        Infolists\Components\TextEntry::make('reason')
                            ->label('Alasan')
                            ->columnSpanFull(),
                        Infolists\Components\TextEntry::make('status')
                            ->label('Status')
                            ->badge()
                            ->formatStateUsing(fn ($state) => $state->label())
                            ->color(fn ($state) => $state->color()),
                        Infolists\Components\TextEntry::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->columnSpanFull()
                            ->visible(fn ($record) => $record->rejection_reason !== null),
                        Infolists\Components\TextEntry::make('approver.name')
                            ->label('Disetujui Oleh')
                            ->visible(fn ($record) => $record->approved_by !== null),
                        Infolists\Components\TextEntry::make('approved_at')
                            ->label('Tanggal Persetujuan')
                            ->dateTime('d/m/Y H:i')
                            ->visible(fn ($record) => $record->approved_at !== null),
                    ])->columns(2),
            ]);
    }
}
