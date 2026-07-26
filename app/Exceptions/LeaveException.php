<?php

namespace App\Exceptions;

use Exception;

class LeaveException extends Exception
{
    public static function insufficientBalance(string $typeName): self
    {
        return new self("Saldo cuti {$typeName} tidak mencukupi.");
    }

    public static function dateRangeInvalid(): self
    {
        return new self("Tanggal akhir harus setelah atau sama dengan tanggal mulai.");
    }

    public static function alreadyPending(): self
    {
        return new self("Anda sudah memiliki pengajuan cuti yang sedang diproses.");
    }

    public static function cannotCancel(): self
    {
        return new self("Pengajuan cuti tidak dapat dibatalkan karena sudah diproses.");
    }

    public static function weekendNotIncluded(): self
    {
        return new self("Hari Sabtu dan Minggu tidak dihitung sebagai hari cuti.");
    }
}
