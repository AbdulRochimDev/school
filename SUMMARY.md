# 📘 Project School Management – Summary & Guidelines

## 🎯 Tujuan
Membangun sistem manajemen sekolah berbasis Laravel yang **ringan**, **modular**, dan **compatible dengan Namecheap shared hosting**.  
Fitur inti meliputi: autentikasi berbasis role, PPDB, keuangan sederhana, absensi, tugas & nilai, hingga rapor.

---

## 🏗️ Arsitektur Modular
Modul utama:

1. **Auth & RBAC** – Users, Roles, Permissions, Policy & Gate  
2. **Master Data** – Students, Teachers, Classes, Subjects, Academic Years, Terms, Enrollments  
3. **Admissions (PPDB)** – Applications, Documents, Verification, Student provisioning  
4. **Assessment & Learning** – Assignments, Submissions (+files), Attendance Sessions & Records, Grade Items, Grades  
5. **Report & Analytics** – Report Cards, Report Card Items, Attendance → Grades → Report  
6. **Finance** – Invoices, Payments, Payment Verifications, Ledger (kas sederhana)  
7. **Utility** – Notifications, Settings, Activity Logs  

---

## 🔑 Role & Permission Matrix
- **super_admin** → semua akses  
- **admin** → kelola user & setting  
- **admin_akademik** → data akademik, rapor  
- **admin_keuangan** → pembayaran, laporan keuangan  
- **operator_ppdb** → verifikasi pendaftar baru  
- **guru** → absensi, tugas, nilai  
- **wali_kelas** → pantau siswa dalam kelasnya  
- **siswa** → tugas, absensi, lihat nilai  

---

## 🗄️ Database & ERD
- Skema: `docs/ERD.md`, `database/mysql/initial_schema.sql`  
- Tambahan absensi & tugas: `database/mysql/attendance_additions.sql`  
- Index & constraints: `docs/DATABASE-SCHEMA.sql`  

Entity penting: Users, Roles, Students, Teachers, Classes, Subjects, Enrollments, Assignments, Submissions, SubmissionFiles, AttendanceSessions, AttendanceRecords, GradeItems, Grades, ReportCards, Finance tables, Admissions (PPDB).

---

## ⚡ Alur Utama
1. **PPDB** → daftar → verifikasi admin → akun siswa aktif  
2. **Absensi & Tugas** → guru buka session / buat tugas → siswa hadir / submit → guru nilai → Grades terupdate  
3. **Nilai & Rapor** → GradeItems (tugas, absensi, ujian) → Grades → ReportCardItems → ReportCards  
4. **Pembayaran** → Invoice → siswa bayar → admin verifikasi → Ledger entry otomatis  
5. **Laporan** → Rapor siswa + laporan keuangan sederhana  

---

## 🛠️ Instalasi & Hosting
- Hosting: Namecheap Shared Hosting (cPanel)  
- PHP & Laravel sesuai env  
- Ikuti `GUIDE-LARAVEL-NAMECHEAP.md`  
- Contoh `.env`: `env/.env.namecheap.example`  

---

## 🔄 Automation & Testing
- **Watcher** otomatis `php artisan test` setiap perubahan  
- **Backup**: `scripts/backup.sh`  
- **Env validation**: `scripts/validate-env.php`  
- **Tests**: Unit (Models, Policies), Feature (Attendance, Submission, Payment, PPDB), Smoke (deploy).  

---

## 📑 API Surface
- Admin: user, kelas, keuangan  
- Guru: absensi, tugas, nilai  
- Siswa: submit tugas, lihat absensi/nilai  
- PPDB: daftar & verifikasi  
- Detail: `docs/API-SURFACE.md`, `routes/api_attendance.php`, `routes/api_assignments.php`  

---

## 🔔 Events
- AttendanceSessionClosed → update nilai kehadiran  
- SubmissionReceived → notifikasi guru  
- GradesUpdated → update rapor  
- PaymentVerified → LedgerEntryCreated  
- PPDBApplicationVerified → StudentProvisioned  

---

## 📌 Catatan
- Semua perubahan harus idempotent.  
- Semua modul wajib punya test.  
- Semua log → `storage/logs/install.log`.  
