<?php

namespace App\Enums;

enum UserRole: string
{
    case Employee = 'employee';
    case Manager = 'manager';
    case HrAdmin = 'hr_admin';

    public function label(): string
    {
        return match ($this) {
            self::Employee => 'Karyawan',
            self::Manager => 'Manager',
            self::HrAdmin => 'HR Admin',
        };
    }
}
