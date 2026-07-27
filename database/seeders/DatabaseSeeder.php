<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\LeaveBalance;
use App\Models\LeaveRequest;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (User::where('email', 'hr@company.com')->exists()) {
            echo "Database already seeded, skipping.\n";
            return;
        }

        // Create Departments
        $hrDept = Department::create(['name' => 'Human Resources', 'description' => 'Bagian SDM']);
        $itDept = Department::create(['name' => 'Information Technology', 'description' => 'Bagian IT']);
        $financeDept = Department::create(['name' => 'Finance', 'description' => 'Bagian Keuangan']);
        $marketingDept = Department::create(['name' => 'Marketing', 'description' => 'Bagian Pemasaran']);

        // Create Leave Types
        $annual = LeaveType::create(['name' => 'Cuti Tahunan', 'description' => 'Cuti tahunan yang diberikan kepada karyawan', 'days_allowed' => 12]);
        $sick = LeaveType::create(['name' => 'Cuti Sakit', 'description' => 'Cuti karena sakit', 'days_allowed' => 12]);
        $personal = LeaveType::create(['name' => 'Cuti Pribadi', 'description' => 'Cuti untuk kepentingan pribadi', 'days_allowed' => 3]);
        $maternity = LeaveType::create(['name' => 'Cuti Melahirkan', 'description' => 'Cuti untuk karyawan yang melahirkan', 'days_allowed' => 90]);

        // Create HR Admin
        $hrAdmin = User::create([
            'name' => 'Rina Sari',
            'email' => 'hr@company.com',
            'password' => Hash::make('password'),
            'role' => 'hr_admin',
            'department_id' => $hrDept->id,
            'position' => 'HR Manager',
            'phone' => '081234567890',
        ]);
        $hrDept->update(['manager_id' => $hrAdmin->id]);

        // Create Managers
        $itManager = User::create([
            'name' => 'Budi Santoso',
            'email' => 'manager.it@company.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'department_id' => $itDept->id,
            'position' => 'IT Manager',
            'phone' => '081234567891',
        ]);
        $itDept->update(['manager_id' => $itManager->id]);

        $financeManager = User::create([
            'name' => 'Siti Rahmawati',
            'email' => 'manager.finance@company.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'department_id' => $financeDept->id,
            'position' => 'Finance Manager',
            'phone' => '081234567892',
        ]);
        $financeDept->update(['manager_id' => $financeManager->id]);

        $marketingManager = User::create([
            'name' => 'Andi Wijaya',
            'email' => 'manager.marketing@company.com',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'department_id' => $marketingDept->id,
            'position' => 'Marketing Manager',
            'phone' => '081234567893',
        ]);
        $marketingDept->update(['manager_id' => $marketingManager->id]);

        // Create Employees
        $employees = [
            ['name' => 'Dewi Lestari', 'email' => 'dewi@company.com', 'department_id' => $itDept->id, 'position' => 'Software Developer'],
            ['name' => 'Rizky Pratama', 'email' => 'rizky@company.com', 'department_id' => $itDept->id, 'position' => 'System Administrator'],
            ['name' => 'Maya Putri', 'email' => 'maya@company.com', 'department_id' => $financeDept->id, 'position' => 'Staff Accounting'],
            ['name' => 'Fajar Nugroho', 'email' => 'fajar@company.com', 'department_id' => $financeDept->id, 'position' => 'Staff Finance'],
            ['name' => 'Anisa Permata', 'email' => 'anisa@company.com', 'department_id' => $marketingDept->id, 'position' => 'Marketing Executive'],
            ['name' => 'Dimas Aditya', 'email' => 'dimas@company.com', 'department_id' => $marketingDept->id, 'position' => 'Content Creator'],
            ['name' => 'Putri Wulandari', 'email' => 'putri@company.com', 'department_id' => $hrDept->id, 'position' => 'Staff HRD'],
            ['name' => 'Arif Rahman', 'email' => 'arif@company.com', 'department_id' => $itDept->id, 'position' => 'QA Engineer'],
        ];

        $allUsers = [$hrAdmin, $itManager, $financeManager, $marketingManager];

        foreach ($employees as $emp) {
            $user = User::create([
                ...$emp,
                'password' => Hash::make('password'),
                'role' => 'employee',
                'phone' => '0812' . rand(10000000, 99999999),
            ]);
            $allUsers[] = $user;
        }

        // Initialize Leave Balances
        $year = (int) now()->year;
        foreach ($allUsers as $user) {
            if ($user->role === 'employee') {
                foreach ([$annual, $sick, $personal, $maternity] as $type) {
                    LeaveBalance::create([
                        'user_id' => $user->id,
                        'leave_type_id' => $type->id,
                        'year' => $year,
                        'total_days' => $type->days_allowed,
                        'used_days' => 0,
                        'remaining_days' => $type->days_allowed,
                    ]);
                }
            }
        }

        // Create Sample Leave Requests
        $employee1 = User::where('email', 'dewi@company.com')->first();
        $employee2 = User::where('email', 'maya@company.com')->first();
        $employee3 = User::where('email', 'anisa@company.com')->first();

        LeaveRequest::create([
            'user_id' => $employee1->id,
            'leave_type_id' => $annual->id,
            'start_date' => now()->addDays(5),
            'end_date' => now()->addDays(7),
            'total_days' => 3,
            'reason' => 'Liburan keluarga',
            'status' => 'pending',
        ]);

        LeaveRequest::create([
            'user_id' => $employee2->id,
            'leave_type_id' => $sick->id,
            'start_date' => now()->subDays(5),
            'end_date' => now()->subDays(3),
            'total_days' => 3,
            'reason' => 'Sakit demam',
            'status' => 'approved',
            'approved_by' => $financeManager->id,
            'approved_at' => now()->subDays(4),
        ]);

        LeaveRequest::create([
            'user_id' => $employee3->id,
            'leave_type_id' => $personal->id,
            'start_date' => now()->addDays(10),
            'end_date' => now()->addDays(10),
            'total_days' => 1,
            'reason' => 'Keperluan pribadi',
            'status' => 'rejected',
            'approved_by' => $marketingManager->id,
            'approved_at' => now()->subDays(1),
            'rejection_reason' => 'Masih banyak campaign yang harus diselesaikan',
        ]);

        echo "Seed data created successfully!\n";
    }
}
