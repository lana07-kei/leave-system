# SLIDE PRESENTASI - Tugas Akhir Semester
## Sistem Pengajuan Cuti Karyawan

---

# Slide 1: Cover

**Sistem Pengajuan Cuti Karyawan**
Menggunakan Laravel 11 dan Filament 3

Mata Kuliah: Cloud Computing
Kelompok: [Nama Kelompok]
Anggota:
- [Nama 1] - [NIM]
- [Nama 2] - [NIM]
- [Nama 3] - [NIM]

---

# Slide 2: Daftar Isi

1. Studi Kasus & Masalah
2. Arsitektur Aplikasi
3. Demo Aplikasi
4. UML Diagrams
5. Arsitektur Cloud
6. Konsep Cloud yang Diterapkan
7. Estimasi Biaya
8. Kendala & Solusi

---

# Slide 3: Studi Kasus

### Masalah
- Pengajuan cuti manual (kertas) → lambat, rentan hilang
- Tidak ada tracking status real-time
- Sulit mengelola saldo cuti secara akurat

### Solusi
- Sistem web untuk pengajuan cuti online
- Approval workflow otomatis (Manager → HR Admin)
- Dashboard statistik dan notifikasi email

---

# Slide 4: Role Pengguna

| Role | Fitur |
|------|-------|
| **Employee** | Submit cuti, lihat riwayat, upload dokumen, batalkan |
| **Manager** | Setujui/tolak pengajuan tim, laporan departemen |
| **HR Admin** | Kelola semua data, laporan keseluruhan |

**3 Role dengan RBAC** = Hak akses berbeda per role

---

# Slide 5: Arsitektur Aplikasi

```
┌─────────────────────────────────┐
│     Presentation Layer          │
│  Filament Admin + Landing Page  │
├─────────────────────────────────┤
│     Application/Service Layer   │
│  LeaveRequestService            │
│  LeaveApprovalService           │
│  LeaveBalanceService            │
├─────────────────────────────────┤
│     Domain Layer                │
│  Models, Enums, Events          │
│  Exceptions, Listeners          │
├─────────────────────────────────┤
│     Infrastructure Layer        │
│  MySQL, File Storage, Email     │
│  Queue Worker, Scheduler        │
└─────────────────────────────────┘
```

Clean Architecture: Separation of Concerns

---

# Slide 6: Tech Stack

| Komponen | Teknologi |
|----------|-----------|
| Framework | Laravel 11 |
| Admin Panel | Filament 3 |
| Database | MySQL 8.0 |
| PHP | 8.2 |
| Queue | Database Driver |
| Scheduler | Laravel Task Scheduler |
| Hosting | Railway (PaaS) |

---

# Slide 7: Demo Alur Aplikasi

### Alur Pengajuan Cuti:
1. **Employee** login → Ajukan Cuti
2. Pilih jenis cuti, tanggal, upload dokumen
3. Sistem kurangi saldo, status: Pending
4. **Manager** dapatkan notifikasi email
5. Manager approve/reject
6. **Employee** dapatkan notifikasi hasil
7. Jika rejected → saldo dikembalikan

### Auto-Cancel:
- Pengajuan pending > 7 hari → otomatis dibatalkan
- Jalankan via Laravel Scheduler (cron)

---

# Slide 8: UML - Use Case & Class

### Use Case (3 Aktor):
- Employee: Submit, View, Cancel, Upload
- Manager: View Team, Approve, Reject
- HR Admin: Manage All, Reports, Export

### Class (6 Model):
- User, Department, LeaveType
- LeaveRequest, LeaveBalance, ActivityLog

### Service (4 Class):
- LeaveRequestService, LeaveApprovalService
- LeaveBalanceService, ActivityLogService

---

# Slide 9: UML - Sequence & Activity

### Sequence Diagram (Pengajuan):
Employee → Form → Service → Validate → DB → Notification → Manager

### Sequence Diagram (Approval):
Manager → Review → Service → Update Status → DB → Notification → Employee

### Activity Diagram:
Login → Pilih Role → Submit → Review → Approve/Reject → Notifikasi → Selesai

*(Lihat diagram lengkap di docs/uml/DIAGRAMS.md)*

---

# Slide 10: Arsitektur Cloud

```
[Browser] ──HTTPS──→ [Railway PHP Container]
                           │
                     ┌─────┴─────┐
                     │           │
                [MySQL]    [File Storage]
                (Managed)   (Attachments)
                     │
                [SMTP Email]
                (Notifications)
```

Platform: **Railway (PaaS)**

---

# Slide 11: Konsep Cloud yang Diterapkan

### 1. Managed Database Service
- MySQL 8.0 managed oleh Railway
- Bukan install manual di server

### 2. Environment/Secrets Management
- APP_KEY, DB_PASSWORD, MAIL_* disimpan sebagai env vars
- Bukan dihardcode di kode

### 3. Auto Deploy (CI/CD)
- Push ke GitHub → otomatis build & deploy
- Menggunakan Nixpacks

### 4. Scalability
- Horizontal scaling tersedia pada plan berbayar

---

# Slide 12: Estimasi Biaya

| Komponen | Biaya/Bulan |
|----------|-------------|
| Compute (512MB RAM) | $5.00 |
| MySQL (1GB storage) | $1.00 |
| Bandwidth (100GB) | $0 |
| **Total** | **~$6.00** |

**Railway Hobby Plan**: $5 credit gratis/bulan
**Biaya riil**: ~$1/bulan

Alternatif lain yang dipertimbangkan:
- AWS EC2+EBS: lebih mahal, lebih kompleks
- Render: gratis tapi terbatas
- VPS: perlu manage manual

---

# Slide 13: Testing & Quality

### Automated Tests (9 tests)
- Submit leave request ✅
- Insufficient balance rejection ✅
- Approve/Reject workflow ✅
- Cancel request ✅
- Weekend exclusion ✅
- Double pending prevention ✅

### Code Quality
- Custom exception handling
- Activity logging (audit trail)
- DB transactions + lockForUpdate
- RBAC enforcement

---

# Slide 14: Kendala & Solusi

| Kendala | Solusi |
|---------|--------|
| Spatie Permission incompatible Laravel 11 | RBAC manual via enum column |
| Filament `query()` closure error | `getEloquentQuery()` override |
| Notification not sending | Connect events → listeners → notifications |
| Railway build failure | Docker deployment as fallback |

---

# Slide 15: Kesimpulan

### Yang Sudah Dikerjakan:
✅ 7 diagram UML lengkap & konsisten
✅ Arsitektur clean architecture
✅ RBAC 3 role + approval workflow
✅ File upload + notifikasi email
✅ Auto-cancel scheduled job
✅ 9 automated tests passing
✅ Landing page + email templates
✅ Deployment config (Docker + Railway)

### Repository:
https://github.com/lana07-kei/leave-system

### Terima Kasih!
