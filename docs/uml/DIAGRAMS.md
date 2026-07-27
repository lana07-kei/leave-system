# UML Documentation - Sistem Pengajuan Cuti

## 1. Use Case Diagram

```mermaid
graph LR
    subgraph Actors
        E((Karyawan))
        M((Manager))
        HR((HR Admin))
    end

    subgraph "Authentication"
        UC_LOGIN[Login]
        UC_LOGOUT[Logout]
    end

    subgraph "Karyawan Features"
        UC_SUBMIT[Ajukan Cuti]
        UC_VIEW_OWN[Lihat Riwayat Cuti]
        UC_CANCEL[Batalkan Pengajuan]
        UC_UPLOAD[Upload Dokumen Pendukung]
        UC_VIEW_BALANCE[Lihat Sisa Cuti]
    end

    subgraph "Manager Features"
        UC_VIEW_TEAM[Lihat Pengajuan Tim]
        UC_APPROVE[Setujui Cuti]
        UC_REJECT[Tolak Cuti]
        UC_VIEW_REPORT[Lihat Laporan Tim]
    end

    subgraph "HR Admin Features"
        UC_MANAGE_USER[Kelola Data Karyawan]
        UC_MANAGE_LEAVE_TYPE[Kelola Jenis Cuti]
        UC_MANAGE_ALL[Lihat Semua Pengajuan]
        UC_MANAGE_BALANCE[Kelola Saldo Cuti]
        UC_VIEW_ALL_REPORT[Lihat Laporan Keseluruhan]
        UC_EXPORT[Export Data]
    end

    E --> UC_LOGIN
    E --> UC_SUBMIT
    E --> UC_VIEW_OWN
    E --> UC_CANCEL
    E --> UC_UPLOAD
    E --> UC_VIEW_BALANCE

    M --> UC_LOGIN
    M --> UC_VIEW_TEAM
    M --> UC_APPROVE
    M --> UC_REJECT
    M --> UC_VIEW_REPORT

    HR --> UC_LOGIN
    HR --> UC_MANAGE_USER
    HR --> UC_MANAGE_LEAVE_TYPE
    HR --> UC_MANAGE_ALL
    HR --> UC_MANAGE_BALANCE
    HR --> UC_VIEW_ALL_REPORT
    HR --> UC_EXPORT

    UC_SUBMIT -.->|include| UC_UPLOAD
    UC_APPROVE -.->|extend| UC_REJECT
```

## 2. Class Diagram

```mermaid
classDiagram
    class User {
        -int id
        -string name
        -string email
        -string password
        -string role
        -int department_id
        -string position
        -string phone
        -timestamp created_at
        -timestamp updated_at
        +isAdmin() bool
        +isManager() bool
        +isEmployee() bool
        +department() Department
        +leaveRequests() Collection
    }

    class Department {
        -int id
        -string name
        -string description
        -int manager_id
        +users() Collection
        +manager() User
    }

    class LeaveType {
        -int id
        -string name
        -string description
        -int days_allowed
        -bool is_active
        +leaveRequests() Collection
        +leaveBalances() Collection
    }

    class LeaveRequest {
        -int id
        -int user_id
        -int leave_type_id
        -date start_date
        -date end_date
        -int total_days
        -string reason
        -string status
        -string attachment_path
        -int approved_by
        -string rejection_reason
        -timestamp approved_at
        -timestamp created_at
        -timestamp updated_at
        +user() User
        +leaveType() LeaveType
        +approver() User
        +canBeCancelled() bool
        +isPending() bool
    }

    class LeaveBalance {
        -int id
        -int user_id
        -int leave_type_id
        -int year
        -int total_days
        -int used_days
        -int remaining_days
        +user() User
        +leaveType() LeaveType
        +updateUsedDays() void
    }

    class ActivityLog {
        -int id
        -int user_id
        -string logable_type
        -int logable_id
        -string action
        -jsonold_values
        -json new_values
        -timestamp created_at
        +user() User
    }

    User "1" --> "*" LeaveRequest : has
    User "1" --> "*" LeaveBalance : has
    User "1" --> "1" Department : belongs_to
    User "1" --> "*" ActivityLog : creates
    Department "1" --> "*" User : has
    Department "1" --> "0..1" User : managed_by
    LeaveType "1" --> "*" LeaveRequest : classifies
    LeaveType "1" --> "*" LeaveBalance : tracks
    LeaveRequest "1" --> "0..1" User : approved_by
    LeaveRequest --> ActivityLog : logs
```

## 3. Sequence Diagram - Pengajuan Cuti (Alur Utama)

```mermaid
sequenceDiagram
    actor K as Karyawan
    participant Web as Web/Filament
    participant C as Controller
    participant S as LeaveService
    participant DB as Database
    participant E as Event Dispatcher
    participant N as Notification

    K->>Web: Isi form pengajuan cuti
    Web->>C: store(StoreLeaveRequest)
    C->>S: createLeaveRequest($validated)

    S->>DB: BEGIN TRANSACTION
    S->>DB: Check LeaveBalance (sisa cuti > 0)
    alt Sisa cuti cukup
        S->>DB: Create LeaveRequest (status: pending)
        S->>DB: Update LeaveBalance (used_days + N)
        S->>DB: COMMIT
        S->>E: dispatch(LeaveRequestCreated)
        E->>N: Send notification to Manager
        N-->>K: Email: "Pengajuan cuti berhasil dikirim"
        S-->>C: LeaveRequest
        C-->>K: Redirect ke detail pengajuan
    else Sisa cuti tidak cukup
        S->>DB: ROLLBACK
        S-->>C: throw LeaveException
        C-->>K: Error: "Saldo cuti tidak mencukupi"
    end
```

## 4. Sequence Diagram - Persetujuan Cuti (Alur Kedua)

```mermaid
sequenceDiagram
    actor MG as Manager
    participant Panel as Filament Panel
    participant S as LeaveApprovalService
    participant DB as Database
    participant E as Event Dispatcher
    participant N as Notification
    actor K as Karyawan

    MG->>Panel: Buka daftar pengajuan pending
    Panel-->>MG: Tampilkan daftar pengajuan
    MG->>Panel: Klik "Setujui" / "Tolak"

    alt Menyetujui
        MG->>Panel: Konfirmasi approve + catatan
        Panel->>S: approve($leaveRequest, $approver)
        S->>DB: BEGIN TRANSACTION
        S->>DB: Update status = approved
        S->>DB: Set approved_by, approved_at
        S->>DB: Log activity
        S->>DB: COMMIT
        S->>E: dispatch(LeaveRequestApproved)
        E->>N: Send ApprovedNotification to Karyawan
        N-->>K: Email: "Pengajuan cuti disetujui"
        S-->>Panel: void
        Panel-->>MG: Success toast
    else Menolak
        MG->>Panel: Isi alasan penolakan
        Panel->>S: reject($leaveRequest, $reason, $approver)
        S->>DB: BEGIN TRANSACTION
        S->>DB: Update status = rejected
        S->>DB: Set rejection_reason, approved_by
        S->>DB: Restore LeaveBalance (used_days - N)
        S->>DB: COMMIT
        S->>E: dispatch(LeaveRequestRejected)
        E->>N: Send RejectedNotification to Karyawan
        N-->>K: Email: "Pengajuan cuti ditolak"
        S-->>Panel: void
        Panel-->>MG: Success toast
    end
```

## 5. Activity Diagram

```mermaid
flowchart TD
    start((●)) --> login[Login ke Sistem]
    login --> checkRole{Cek Role}

    checkRole -->|Karyawan| empDash[Dashboard Karyawan]
    checkRole -->|Manager| mgrDash[Dashboard Manager]
    checkRole -->|HR Admin| hrDash[Dashboard HR Admin]

    subgraph "Alur Karyawan"
        empDash --> viewBalance[Lihat Sisa Cuti]
        viewBalance --> checkBalance{Sisa Cuti > 0?}
        checkBalance -->|Tidak| noBalance[Error: Saldo Tidak Cukup]
        checkBalance -->|Ya| fillForm[Isi Form Pengajuan Cuti]
        fillForm --> upload[Upload Dokumen Pendukung]
        upload --> submit[Submit Pengajuan]
        submit --> pending[Status: Menunggu Persetujuan]
        pending --> notif_mgr[Notifikasi ke Manager]
    end

    subgraph "Alur Manager"
        mgrDash --> viewRequests[Lihat Pengajuan Tim]
        viewRequests --> review[Review Pengajuan]
        review --> decide{Keputusan}
        decide -->|Approve| approve[Setujui Cuti]
        approve --> notif_emp_approve[Notifikasi: Disetujui]
        decide -->|Reject| reject[Tolak Cuti]
        reject --> restore[Restore Saldo Cuti]
        restore --> notif_emp_reject[Notifikasi: Ditolak]
    end

    subgraph "Alur HR Admin"
        hrDash --> manageUser[Kelola Karyawan]
        hrDash --> manageType[Kelola Jenis Cuti]
        hrDash --> viewAll[Lihat Semua Pengajuan]
        hrDash --> viewReport[Lihat Laporan]
        hrDash --> exportData[Export Data]
    end

    subgraph "Scheduled Job"
        cronJob[Cron: Auto-Cancel Expired] --> checkExpired{Cek Pending > 7 Hari?}
        checkExpired -->|Ya| autoCancel[Auto Cancel + Restore Saldo]
        checkExpired -->|Tidak| skip[Skip]
    end

    noBalance --> fillForm
    notif_mgr --> review
    notif_emp_approve --> end1((⊙))
    notif_emp_reject --> end2((⊙))
    autoCancel --> end3((⊙))
    skip --> end4((⊙))
    exportData --> end5((⊙))
```

## 6. Component/Architecture Diagram

```mermaid
graph TD
    subgraph Presentation["Presentation Layer"]
        AdminPanel["Filament Admin Panel"]
        EmployeePanel["Filament Employee Panel"]
        WebRoutes["Web Routes"]
    end

    subgraph Application["Application Layer"]
        Controllers["Controllers"]
        FilamentRes["Filament Resources"]
        Policies["Policies"]
        EventsListeners["Events & Listeners"]
        Notifications["Notifications"]
    end

    subgraph Domain["Domain Layer (Services)"]
        LeaveService["Leave Request Service"]
        LeaveApprovalService["Leave Approval Service"]
        LeaveBalanceService["Leave Balance Service"]
        FileStorageService["File Storage Service"]
        ExportService["Export Service"]
    end

    subgraph Infrastructure["Infrastructure Layer"]
        EloquentModels["Eloquent Models"]
        Migrations["Database Migrations (MySQL)"]
        Queue["Queue Driver"]
        Scheduler["Task Scheduler"]
    end

    AdminPanel --> Controllers
    AdminPanel --> FilamentRes
    EmployeePanel --> FilamentRes
    WebRoutes --> Controllers

    Controllers --> LeaveService
    Controllers --> LeaveApprovalService
    FilamentRes --> LeaveService
    FilamentRes --> LeaveApprovalService
    FilamentRes --> LeaveBalanceService
    EventsListeners --> LeaveService
    EventsListeners --> Notifications

    LeaveService --> EloquentModels
    LeaveApprovalService --> EloquentModels
    LeaveBalanceService --> EloquentModels
    FileStorageService --> EloquentModels
    ExportService --> EloquentModels

    Scheduler --> LeaveService

    style Presentation fill:#E3F2FD,stroke:#1565C0
    style Application fill:#E8F5E9,stroke:#2E7D32
    style Domain fill:#FFF3E0,stroke:#E65100
    style Infrastructure fill:#F3E5F5,stroke:#6A1B9A
```

## 7. Data Flow Diagram (ERD)

```mermaid
erDiagram
    users {
        int id PK
        string name
        string email UK
        string password
        string role
        int department_id FK
        string position
        string phone
        timestamp created_at
        timestamp updated_at
    }

    departments {
        int id PK
        string name
        string description
        int manager_id FK
        timestamp created_at
        timestamp updated_at
    }

    leave_types {
        int id PK
        string name
        string description
        int days_allowed
        bool is_active
        timestamp created_at
        timestamp updated_at
    }

    leave_requests {
        int id PK
        int user_id FK
        int leave_type_id FK
        date start_date
        date end_date
        int total_days
        string reason
        string status
        string attachment_path
        int approved_by FK
        string rejection_reason
        timestamp approved_at
        timestamp created_at
        timestamp updated_at
    }

    leave_balances {
        int id PK
        int user_id FK
        int leave_type_id FK
        int year
        int total_days
        int used_days
        int remaining_days
        timestamp created_at
        timestamp updated_at
    }

    activity_logs {
        int id PK
        int user_id FK
        string logable_type
        int logable_id
        string action
        json old_values
        json new_values
        timestamp created_at
    }

    users ||--o{ leave_requests : "mengajukan"
    users ||--o{ leave_balances : "memiliki"
    users ||--o{ activity_logs : "membuat"
    users ||--o{ leave_requests : "menyetujui"
    departments ||--o{ users : "berisi"
    users ||--o| departments : "dikelola"
    leave_types ||--o{ leave_requests : "klasifikasi"
    leave_types ||--o{ leave_balances : "tracking"
    leave_requests ||--o{ activity_logs : "logging"
```

---

## 8. Deployment Architecture Diagram (Cloud)

```mermaid
graph TB
    subgraph "User Layer"
        Browser["Browser<br/>(User)"]
    end

    subgraph "Cloud: Railway (PaaS)"
        subgraph "Compute Service"
            App["PHP 8.2 Container<br/>Laravel 11 + Filament 3"]
            Scheduler["Laravel Scheduler<br/>(cron: leave:cancel-expired)"]
            QueueWorker["Queue Worker<br/>(notifications)"]
        end

        subgraph "Managed Database"
            MySQL["MySQL 8.0<br/>(Managed Service)"]
        end

        subgraph "Object Storage"
            Storage["File Storage<br/>(attachments)"]
        end

        subgraph "Environment"
            Env["Environment Variables<br/>(APP_KEY, DB_URL, MAIL_*)"]
        end
    end

    subgraph "External Services"
        Mail["SMTP Mail Server<br/>(Gmail/SMTP)"]
        GitHub["GitHub<br/>(Source Code + CI/CD)"]
    end

    Browser -->|"HTTPS"| App
    App -->|"SQL"| MySQL
    App -->|"File I/O"| Storage
    Scheduler -->|"Cron Job"| App
    QueueWorker -->|"Process Jobs"| App
    App -->|"Email"| Mail
    GitHub -->|"git push"| App
    Env -.->|"Configure"| App

    style App fill:#6366f1,color:#fff
    style MySQL fill:#2563eb,color:#fff
    style Storage fill:#059669,color:#fff
    style Scheduler fill:#d97706,color:#fff
    style QueueWorker fill:#d97706,color:#fff
```

---

## 9. Cloud Service Justification

### Pilihan: Railway (PaaS)

**Mengapa Railway:**

| Kriteria | Railway | AWS (EC2+EBS) | Render | VPS (DigitalOcean) |
|----------|---------|---------------|--------|---------------------|
| Kemudahan Deploy | Sangat mudah (git push) | Sulit (manual config) | Mudah | Sedang |
| Managed Database | Ya (MySQL built-in) | Ya (RDS, mahal) | Ya (MySQL) | Tidak (manual) |
| Biaya Free Tier | $5 credit/bulan | 12 bulan gratis | Gratis (terbatas) | $200 credit |
| Auto Deploy | Ya (otomatis) | Perlu CI/CD setup | Ya | Perlu setup |
| Environment Mgmt | Built-in | Secrets Manager | Built-in | Manual |
| Laravel Support | Nixpacks (auto-detect) | Manual | Buildpack | Manual |

**Railway dipilih karena:**
1. **PaaS (Platform-as-a-Service)**: Tidak perlu manage server, fokus ke kode
2. **Managed Database**: MySQL disediakan tanpa install manual
3. **Auto Deploy**: Push ke GitHub → otomatis deploy
4. **Environment Variables**: Secrets management bawaan
5. **Cocok untuk tugas kuliah**: Cepat setup, mudah dijelaskan

### Konsep Cloud yang Diterapkan

1. **Managed Database Service**: MySQL 8.0 managed oleh Railway, bukan install manual di server
2. **Environment/Secrets Management**: APP_KEY, DB_PASSWORD, MAIL credentials disimpan sebagai environment variables, bukan dihardcode
3. **Auto Deploy (CI/CD)**: Push ke branch `main` → otomatis build & deploy
4. **Scalability**: Railway mendukung horizontal scaling (tambah container) pada plan berbayar

---

## 10. Estimasi Biaya Operasional Bulanan (Railway)

### Plan: Hobby ($5/month credit)

| Komponen | Spesifikasi | Biaya/Bulan |
|----------|-------------|-------------|
| **Compute** | 512 MB RAM, 1 vCPU | ~$5.00 |
| **MySQL** | 1 GB storage, shared CPU | ~$1.00 |
| **Bandwidth** | 100 GB included | $0 (included) |
| **Storage (files)** | 1 GB | $0 (included) |
| **Total** | | **~$6.00/bulan** |

> Dengan credit $5/bulan dari Railway Hobby Plan, estimasi biaya riil sekitar **$1.00/bulan**.

### Alternatif: Free Tier
- Railway memberikan **$5 credit gratis** setiap bulan
- Untuk aplikasi skala kecil (UAS), **gratis** selama tidak melebihi credit
- Cukup untuk 1 container + 1 MySQL instance

---

## 11. CI/CD Pipeline

```mermaid
graph LR
    A["Developer<br/>git push"] --> B["GitHub<br/>Repository"]
    B --> C["Railway<br/>Webhook Trigger"]
    C --> D["Build Phase<br/>(Nixpacks)"]
    D --> E["Install Dependencies<br/>(composer install)"]
    E --> F["Cache Config<br/>(config:cache, route:cache)"]
    F --> G["Deploy Phase<br/>(Start Command)"]
    G --> H["Migrate DB<br/>(migrate --force)"]
    H --> I["Health Check<br/>(/admin/login)"]
    I --> J["Live Application<br/>(URL publik)"]

    style A fill:#6366f1,color:#fff
    style J fill:#059669,color:#fff
```
