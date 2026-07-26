# Sistem Pengajuan Cuti

Aplikasi web untuk mengelola pengajuan cuti karyawan di lingkungan perusahaan, dibangun dengan **Laravel 11** dan **Filament 3**.

## Fitur Utama

### 3 Role Pengguna (RBAC)
- **Karyawan (Employee)** - Ajukan cuti, lihat riwayat, upload dokumen pendukung, batalkan pengajuan
- **Manager** - Setujui/tolak pengajuan cuti anggota tim, lihat laporan departemen
- **HR Admin** - Kelola karyawan, jenis cuti, departemen, saldo cuti, laporan keseluruhan

### Proses Persetujuan
- Pengajuan cuti melalui proses verifikasi oleh Manager
- Alur: Pending → Approved / Rejected
- Auto-cancel pengajuan pending > 7 hari (scheduled job)
- Restore saldo cuti jika ditolak atau dibatalkan

### Fitur Lainnya
- Upload dokumen pendukung (surat sakit, dll)
- Notifikasi email otomatis untuk perubahan status
- Dashboard statistik real-time
- Export data pengajuan
- Logging aktivitas (activity log)

## Tech Stack
- **Framework**: Laravel 11
- **Admin Panel**: Filament 3
- **Database**: MySQL
- **Autentikasi**: Laravel Breeze (built-in Filament)
- **RBAC**: Custom role-based (column `role` pada users)

## Arsitektur (Clean Architecture)
```
app/
├── Console/Commands/        # Scheduled commands
├── Enums/                   # LeaveStatus, UserRole
├── Events/                  # LeaveRequestCreated, Approved, Rejected
├── Exceptions/              # LeaveException
├── Filament/
│   ├── Resources/           # LeaveRequestResource, UserResource, dll
│   └── Widgets/             # StatsOverview, RecentLeaveRequests
├── Http/Controllers/        # Employee controllers
├── Listeners/               # AutoCancelExpiredRequests
├── Models/                  # User, Department, LeaveRequest, dll
├── Notifications/           # LeaveRequestNotification
├── Policies/                # Authorization policies
├── Providers/Filament/      # AdminPanelProvider
└── Services/                # Business logic services
    ├── LeaveRequestService
    ├── LeaveApprovalService
    ├── LeaveBalanceService
    └── ActivityLogService
```

## Instalasi

### Prerequisites
- PHP >= 8.2
- MySQL >= 5.7
- Composer
- Node.js & NPM

### 1. Clone Repository
```bash
git clone https://github.com/your-repo/leave-system.git
cd leave-system
```

### 2. Install Dependencies
```bash
composer install
npm install
npm run build
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Konfigurasi Database (MySQL)
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=leave_system
DB_USERNAME=root
DB_PASSWORD=
```

### 5. Migration & Seeding
```bash
php artisan migrate:fresh --seed
```

### 6. Storage Link
```bash
php artisan storage:link
```

### 7. Jalankan Aplikasi
```bash
php artisan serve
```

Akses di `http://localhost:8000`

## Akun Default (Hasil Seeding)

| Role | Email | Password |
|------|-------|----------|
| HR Admin | hr@company.com | password |
| Manager IT | manager.it@company.com | password |
| Manager Finance | manager.finance@company.com | password |
| Karyawan | dewi@company.com | password |

## Struktur URL

### Admin Panel (Filament)
- `/admin` - Dashboard admin
- `/admin/leave-requests` - Kelola pengajuan cuti
- `/admin/leave-types` - Kelola jenis cuti (HR Admin)
- `/admin/users` - Kelola karyawan (HR Admin)
- `/admin/departments` - Kelola departemen (HR Admin)
- `/admin/login` - Login admin/manager/HR

## Business Rules

1. **Satu Pengajuan Aktif**: Karyawan hanya bisa memiliki 1 pengajuan dengan status pending
2. **Auto-Cancel**: Pengajuan pending > 7 hari otomatis dibatalkan
3. **Saldo Cuti**: Saldo dikurangi saat submit, dikembalikan jika ditolak/dibatalkan
4. **Weekend Excluded**: Hari Sabtu & Minggu tidak dihitung sebagai hari cuti
5. **Status Flow**: PENDING → APPROVED / REJECTED / CANCELLED

## Command Penting

### Jalankan Scheduler (auto-cancel)
```bash
php artisan schedule:work
```

### Cancel Expired Secara Manual
```bash
php artisan leave:cancel-expired
```

## UML Documentation
Dokumentasi UML tersedia di `docs/uml/DIAGRAMS.md` dengan 7 diagram:
1. Use Case Diagram
2. Class Diagram
3. Sequence Diagram (Pengajuan Cuti)
4. Sequence Diagram (Persetujuan Cuti)
5. Activity Diagram
6. Component/Architecture Diagram
7. Data Flow Diagram (ERD)

## License
MIT License
