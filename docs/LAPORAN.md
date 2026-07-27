# LAPORAN TUGAS AKHIR SEMESTER
## Mata Kuliah: Cloud Computing

---

# Cover

**Sistem Pengajuan Cuti Karyawan**
**Menggunakan Laravel 11 dan Filament 3**

Mata Kuliah: Cloud Computing
Perancangan UML, Implementasi Software Engineering, dan Deployment Aplikasi Berbasis Cloud

Kelompok: [Nama Kelompok]
Anggota:
1. [Nama 1] - [NIM]
2. [Nama 2] - [NIM]
3. [Nama 3] - [NIM]

Universitas [Nama Universitas]
Program Studi [Nama Prodi]
Tahun 2026

---

# Daftar Isi

1. Pendahuluan
   1.1 Latar Belakang
   1.2 Rumusan Masalah
   1.3 Tujuan
2. Studi Kasus
   2.1 Deskripsi Aplikasi
   2.2 Role Pengguna
3. Perancangan UML
   3.1 Use Case Diagram
   3.2 Class Diagram
   3.3 Sequence Diagram
   3.4 Activity Diagram
   3.5 Component Diagram
   3.6 ERD
4. Implementasi Software Engineering
   4.1 Arsitektur Aplikasi
   4.2 Struktur Project
   4.3 RBAC Implementation
   4.4 Error Handling
   4.5 Automated Testing
5. Arsitektur Cloud & Deployment
   5.1 Pilihan Cloud Service
   5.2 Diagram Infrastruktur
   5.3 Konsep Cloud yang Diterapkan
   5.4 Estimasi Biaya
6. Kendala & Solusi
7. Kesimpulan
8. Daftar Pustaka

---

# BAB 1: Pendahuluan

## 1.1 Latar Belakang

Manajemen pengajuan cuti merupakan aktivitas rutin yang dilakukan oleh setiap perusahaan. Proses pengajuan cuti secara konvensional masih banyak menggunakan formulir kertas yang memakan waktu dan rentan terhadap kehilangan data. Dalam era digitalisasi, diperlukan sistem yang dapat mengotomasi proses pengajuan cuti mulai dari pengajuan, persetujuan, hingga pencatatan saldo cuti.

Tugas Akhir Semester ini menguji kemampuan mahasiswa secara menyeluruh dalam tiga aspek: perancangan sistem menggunakan UML, implementasi software engineering, dan deployment aplikasi ke layanan cloud. Sebagai referensi, digunakan repository `ept-registration` yang menunjukkan standar kualitas yang diharapkan.

## 1.2 Rumusan Masalah

1. Bagaimana merancang sistem pengajuan cuti yang terintegrasi dengan 3 role pengguna?
2. Bagaimana mengimplementasikan approval workflow dengan RBAC?
3. Bagaimana menerapkan arsitektur clean architecture pada aplikasi Laravel?
4. Bagaimana mendeploy aplikasi ke layanan cloud dan menerapkan konsep cloud computing?

## 1.3 Tujuan

1. Menyusun dokumentasi UML lengkap (7 diagram) yang konsisten dengan kode
2. Mengimplementasikan aplikasi dengan arsitektur berlapis
3. Menerapkan RBAC, error handling, dan automated testing
4. Mendeploy ke layanan cloud (Railway) dan menerapkan managed database

---

# BAB 2: Studi Kasus

## 2.1 Deskripsi Aplikasi

**Sistem Pengajuan Cuti** adalah aplikasi web yang memungkinkan karyawan mengajukan cuti secara online, Manager menyetujui/menolak pengajuan, dan HR Admin mengelola seluruh data cuti. Aplikasi ini dibangun menggunakan Laravel 11 dan Filament 3 dengan database MySQL.

### Fitur Utama:
- Pengajuan cuti online dengan upload dokumen pendukung
- Approval workflow (Pending → Approved/Rejected)
- Notifikasi email otomatis
- Auto-cancel pengajuan pending > 7 hari
- Dashboard statistik real-time
- Activity logging untuk audit trail

## 2.2 Role Pengguna

| Role | Deskripsi | Hak Akses |
|------|-----------|-----------|
| Employee | Karyawan biasa | Submit cuti, lihat riwayat, batalkan pengajuan, upload dokumen |
| Manager | Manager departemen | Setujui/tolak pengajuan tim, lihat laporan departemen |
| HR Admin | Admin HR | Kelola semua data (karyawan, jenis cuti, departemen, saldo) |

---

# BAB 3: Perancangan UML

## 3.1 Use Case Diagram

Tiga aktor utama: Employee, Manager, HR Admin.

**Employee:** Login, Ajukan Cuti, Lihat Riwayat, Batalkan Pengajuan, Upload Dokumen, Lihat Sisa Cuti
**Manager:** Login, Lihat Pengajuan Tim, Setujui Cuti, Tolak Cuti, Lihat Laporan Tim
**HR Admin:** Login, Kelola Karyawan, Kelola Jenis Cuti, Kelola Departemen, Kelola Saldo Cuti, Lihat Semua Pengajuan, Export Data

*(Lihat diagram lengkap di docs/uml/DIAGRAMS.md - Diagram 1)*

## 3.2 Class Diagram

Model utama:
- **User** (id, name, email, password, role, department_id)
- **Department** (id, name, manager_id)
- **LeaveType** (id, name, days_allowed)
- **LeaveRequest** (id, user_id, leave_type_id, start_date, end_date, total_days, reason, status, attachment_path, approved_by, rejection_reason)
- **LeaveBalance** (id, user_id, leave_type_id, year, total_days, used_days, remaining_days)
- **ActivityLog** (id, user_id, logable_type, logable_id, action, old_values, new_values)

Service classes:
- LeaveRequestService (create, cancel, calculateWorkingDays)
- LeaveApprovalService (approve, reject, restoreBalance)
- LeaveBalanceService (initialize, reset)
- ActivityLogService (log)

*(Lihat diagram lengkap di docs/uml/DIAGRAMS.md - Diagram 2)*

## 3.3 Sequence Diagram

**Alur Utama: Pengajuan Cuti**
1. Employee mengisi form pengajuan (jenis cuti, tanggal, alasan)
2. Sistem memvalidasi tanggal dan cek saldo cuti
3. Sistem mengurangi saldo cuti
4. Sistem menyimpan pengajuan dengan status Pending
5. Sistem mengirim notifikasi ke Manager dan HR Admin
6. Sistem mencatat activity log

**Alur Kedua: Persetujuan Cuti**
1. Manager melihat daftar pengajuan pending
2. Manager memilih approve/reject
3. Sistem memvalidasi status masih Pending
4. Sistem update status (Approved/Rejected)
5. Jika rejected, sistem mengembalikan saldo cuti
6. Sistem mengirim notifikasi email ke karyawan
7. Sistem mencatat activity log

*(Lihat diagram lengkap di docs/uml/DIAGRAMS.md - Diagram 3 & 4)*

## 3.4 Activity Diagram

Alur end-to-end: Login → Pilih Role → Employee: Submit Cuti → Manager: Review → Approve/Reject → Notifikasi → Selesai

*(Lihat diagram lengkap di docs/uml/DIAGRAMS.md - Diagram 5)*

## 3.5 Component Diagram

Lapisan arsitektur:
- **Presentation**: Filament Admin Panel, Landing Page
- **Application/Service**: LeaveRequestService, LeaveApprovalService, LeaveBalanceService
- **Domain**: Models (User, LeaveRequest, LeaveBalance, dll), Enums, Events
- **Infrastructure**: MySQL Database, File Storage, Email (SMTP), Queue Worker

*(Lihat diagram lengkap di docs/uml/DIAGRAMS.md - Diagram 6)*

## 3.6 ERD

6 tabel utama dengan relasi:
- users → departments (Many-to-One)
- users → leave_requests (One-to-Many)
- leave_types → leave_requests (One-to-Many)
- users → leave_balances (One-to-Many)
- leave_requests → activity_logs (Polymorphic)

*(Lihat diagram lengkap di docs/uml/DIAGRAMS.md - Diagram 7)*

---

# BAB 4: Implementasi Software Engineering

## 4.1 Arsitektur Aplikasi

Aplikasi menggunakan **Clean Architecture** dengan pemisahan yang jelas:

```
app/
├── Console/Commands/    → Scheduled commands
├── Enums/               → LeaveStatus, UserRole
├── Events/              → Domain events
├── Exceptions/          → Custom error handling
├── Filament/            → Presentation layer
├── Listeners/           → Event handlers
├── Models/              → Data models
├── Notifications/       → Email notifications
├── Providers/           → Service providers
└── Services/            → Business logic
```

## 4.2 Struktur Project

- **Framework**: Laravel 11
- **Admin Panel**: Filament 3
- **Database**: MySQL 8.0
- **PHP**: 8.2
- **Autentikasi**: Filament built-in auth
- **Queue**: Database driver

## 4.3 RBAC Implementation

RBAC diimplementasikan menggunakan column `role` pada tabel `users` dengan enum value: `employee`, `manager`, `hr_admin`.

```php
// User Model
public function isAdmin(): bool { return $this->role === 'hr_admin'; }
public function isManager(): bool { return $this->role === 'manager'; }
public function isEmployee(): bool { return $this->role === 'employee'; }
```

Setiap Filament Resource memiliki `canViewAny()` dan `getEloquentQuery()` untuk membatasi akses berdasarkan role.

## 4.4 Error Handling

Custom exception `LeaveException` dengan factory methods:
- `LeaveException::insufficientBalance()` - Saldo cuti tidak cukup
- `LeaveException::alreadyPending()` - Masih ada pengajuan pending
- `LeaveException::cannotCancel()` - Tidak bisa membatalkan
- `LeaveException::dateRangeInvalid()` - Range tanggal tidak valid

## 4.5 Automated Testing

9 tests pada `tests/Feature/LeaveRequestTest.php`:
1. Employee can submit leave request
2. Employee cannot submit with insufficient balance
3. Manager can approve leave request
4. Manager can reject leave request
5. Employee can cancel pending request
6. Cannot submit two pending requests
7. Working days exclude weekends

---

# BAB 5: Arsitektur Cloud & Deployment

## 5.1 Pilihan Cloud Service: Railway

Railway dipilih sebagai platform deployment karena:

1. **PaaS (Platform-as-a-Service)**: Tidak perlu manage server
2. **Managed Database**: MySQL disediakan sebagai service
3. **Auto Deploy**: Push ke GitHub → otomatis deploy
4. **Environment Management**: Secrets management bawaan
5. **Free Tier**: $5 credit/bulan, cukup untuk aplikasi skala kecil

## 5.2 Diagram Infrastruktur

```
[Browser] → HTTPS → [Railway PHP Container] → SQL → [Railway MySQL]
                                         ↕
                              [File Storage (attachments)]
                                         ↓
                              [SMTP Email Server]
```

*(Lihat diagram lengkap di docs/uml/DIAGRAMS.md - Diagram 8)*

## 5.3 Konsep Cloud yang Diterapkan

### 1. Managed Database Service
MySQL 8.0 dikelola oleh Railway, bukan diinstall manual. Keuntungan: otomatis backup, scaling, monitoring.

### 2. Environment/Secrets Management
Variabel sensitif (APP_KEY, DB_PASSWORD, MAIL credentials) disimpan sebagai environment variables, bukan dihardcode di kode.

### 3. Auto Deploy (CI/CD)
Push ke branch `main` → Railway otomatis build dan deploy menggunakan Nixpacks.

### 4. Horizontal Scalability
Railway mendukung penambahan container instances pada plan berbayar.

## 5.4 Estimasi Biaya

| Komponen | Biaya/Bulan |
|----------|-------------|
| Compute (512MB RAM, 1 vCPU) | ~$5.00 |
| MySQL (1 GB storage) | ~$1.00 |
| Bandwidth (100 GB) | $0 (included) |
| **Total** | **~$6.00/bulan** |

Dengan Hobby Plan ($5 credit gratis), biaya riil sekitar $1/bulan.

---

# BAB 6: Kendala & Solusi

| Kendala | Solusi |
|---------|--------|
| Spatie Permission tidak kompatibel dengan Laravel 11 | Implementasi RBAC manual menggunakan column enum |
| Filament 3.2 tidak support `query()` closure pada table | Menggunakan `getEloquentQuery()` override |
| AutoCancelExpiredRequests listener tidak efisien | Rewrite menggunakan DB transaction + lockForUpdate |
| Notifikasi email tidak terkirim | Connect event dispatch ke notification class |
| Railway build failure | Fallback ke Docker deployment untuk presentasi |

---

# BAB 7: Kesimpulan

Sistem Pengajuan Cuti telah berhasil diimplementasikan dengan:

1. **7 diagram UML** yang lengkap dan konsisten dengan kode
2. **Arsitektur clean architecture** dengan pemisahan layer yang jelas
3. **RBAC 3 role** (Employee, Manager, HR Admin) dengan hak akses berbeda
4. **Approval workflow** dengan notifikasi email otomatis
5. **Automated testing** (9 tests passing)
6. **Cloud deployment** menggunakan Railway dengan managed database

Aplikasi ini memenuhi semua kriteria kompleksitas minimum yang ditetapkan dalam modul tugas akhir semester.

---

# BAB 8: Daftar Pustaka

1. Laravel Documentation. https://laravel.com/docs
2. Filament Documentation. https://filamentphp.com/docs
3. Railway Documentation. https://docs.railway.app
4. Laravel 11 Release Notes. https://laravel.com/docs/11.x/releases
5. Mermaid.js Documentation. https://mermaid.js.org

---

# Lampiran

- **A.** Source Code Repository: https://github.com/lana07-kei/leave-system
- **B.** UML Diagrams: docs/uml/DIAGRAMS.md
- **C.** Test Results: 9/9 tests passing
- **D.** Deployment Config: Dockerfile, docker-compose.yml, nixpacks.toml
