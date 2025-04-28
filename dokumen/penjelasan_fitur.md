
# Dokumentasi Fitur Sistem Manajemen Tugas

## Deskripsi Umum
Sistem ini adalah aplikasi manajemen tugas internal yang digunakan oleh tim IT, HR, dan Operasional untuk mencatat, melacak, dan melaporkan progres tugas-tugas yang berhubungan dengan proyek tertentu.

---

## Jenis Pengguna (User Roles)

| Role    | Deskripsi |
|---------|-----------|
| Admin   | Mengelola semua data, user, tugas, dan proyek, serta melihat laporan |
| Manager | Mengelola proyek, tugas, serta melihat laporan |
| Staff   | Melihat tugas sendiri dan memperbarui status tugas |

---

## Fitur Utama

| Fitur                | Role Akses       | Deskripsi |
|----------------------|------------------|-----------|
| Login                | Semua            | Masuk ke sistem menggunakan akun masing-masing |
| Dashboard            | Semua            | Menampilkan informasi ringkas sesuai role |
| Manage Project       | Admin, Manager   | Tambah, edit, hapus project |
| Manage Task          | Admin, Manager   | Tambah, edit, hapus task (tugas), serta menetapkan ke user |
| Update Task Status   | Staff            | Mengubah status task (tugas) yang ditugaskan ke dirinya |
| Manage User          | Admin            | Tambah, edit, hapus users |
| Manage Position      | Admin            | Tambah, edit, hapus position |
| Report               | Admin, Manager   | Melihat ringkasan progres tugas berdasarkan proyek dan user |

---

## Entitas Utama

1. Users  
Data pengguna sistem. Masing-masing memiliki peran yang mengatur hak akses fitur.

2. Positions  
Data posisi/jabatan kerja.

3. Projects  
Mewakili proyek kerja yang dapat memiliki banyak tugas.

3. Tasks  
Tugas-tugas yang harus dikerjakan user, dan termasuk dalam satu proyek. Memiliki status dan deadline.

---

## Manajemen Hak Akses

Sistem menggunakan plugin **Filament Shield** untuk menerapkan Role-Based Access Control (RBAC) berbasis `spatie/laravel-permission`.

### Tabel Otomatis:
- `roles`
- `permissions`
- `model_has_roles`
- `role_has_permissions`
- `model_has_permissions`

---

## Laporan

Sistem menampilkan laporan proyek beserta jumlah tugas berdasarkan status yang dapat difilter berdasarkan proyek dan tugas yang diikuti oleh user

Status tugas yang didukung:
- To Do
- In Progress
- Done

---

## Catatan Teknis
- Framework: Laravel 10
- Admin Panel: Filament v3
- Role & Permission: Filament Shield
