# Task Management System

## Deskripsi
Sistem manajemen tugas internal ini dibangun untuk membantu tim IT, HR, dan Operasional dalam mencatat, melacak, dan melaporkan progres tugas proyek. Sistem ini berbasis web menggunakan **Laravel 10** dan **Filament v3**.

Fitur utama:
- Manajemen Proyek & Tugas
- Laporan Progres berdasarkan status tugas
- Role-Based Access Control (RBAC) menggunakan Filament Shield
- Admin Panel untuk pengelolaan user, posisi, proyek, dan tugas

---

## Fitur Utama

| Fitur                | Role Akses       | Deskripsi |
|----------------------|------------------|-----------|
| Login                | Semua            | Masuk ke sistem menggunakan akun masing-masing |
| Dashboard            | Semua            | Menampilkan informasi ringkas sesuai role |
| Manage Project       | Admin, Manager   | Tambah, edit, hapus proyek |
| Manage Task          | Admin, Manager   | Tambah, edit, hapus tugas, tetapkan ke user |
| Update Task Status   | Staff            | Update status tugas sendiri |
| Manage User          | Admin            | Tambah, edit, hapus user |
| Manage Position      | Admin            | Tambah, edit, hapus posisi jabatan |
| Report               | Admin, Manager   | Lihat ringkasan progres tugas per proyek dan user |

---

## Struktur Database (ERD)

Entity yang digunakan dalam sistem:

- **Users** (pengguna sistem, terkait ke posisi dan role)
- **Positions** (jabatan kerja)
- **Projects** (proyek kerja, memiliki banyak tugas)
- **Tasks** (tugas-tugas yang terkait dengan proyek dan user)
- **Roles & Permissions** (untuk RBAC berbasis `spatie/laravel-permission`)

> Lihat file: [`/dokumen/ERD.pdf`](./dokumen/ERD.pdf)

---

## Alur Bisnis
Sistem ini ini akan berjalan sebagai berikut:
- User akan diarahkan untuk login
- Ketika login sistem akan memeriksa user role dan permission, dan sistem akan meredirect ke halaman dashboard
- Admin akan memiliki akses ke menu Report, Task, Project, Users, dan Positions
- Manager akan memiliki akses ke menu Report, Task, dan Project
- Staff hanya akan memiliki akses ke menu Task

> Lihat file: [`/dokumen/flowdiagram.pdf`](./dokumen/flowdiagram.pdf)

---

## Alur Sistem (Flowchart) (Penjelasan teknis dari Alur Bisnis)

Sistem ini mengikuti flow:
- Proses login dan validasi credential
- Role menentukan akses fitur
- Akses menu sesuai hak masing-masing

> Lihat file: [`/dokumen/flowchart.pdf`](./dokumen/flowchart.pdf)

---

## Cara Instalasi & Menjalankan Sistem Lokal

### 1. Clone Repository
```bash
git clone https://github.com/FaizNala/task-manager.git
cd task-manager
```

### 2. Install Dependency
```bash
composer update
```

### 3. Konfigurasi Environment
- Copy file `.env.example` menjadi `.env`
- Sesuaikan pengaturan database, mail, dsb.

```bash
cp .env.example .env
```

- Generate key:
```bash
php artisan key:generate
```

### 4. Setup Database
- Buat database baru di MySQL
- Jalankan migrasi:
```bash
php artisan migrate
```
- Jalankan migrasi (noted data projects dan tasks akan digenerate secara random)
```bash
php artisan db:seed --class=DatabaseSeeder
```

### 5. Jalankan Server Lokal
```bash
php artisan serve
```
Akses di browser: `http://localhost:8000/admin/login`

---

### 6. Login dengan data berikut
1. Admin :  
    - email : budi.santoso@company.id
    - password : password123
2. Manager : 
    - email : ani.wijaya@company.id / bambang.sunaryo@company.id / dewi.kurnia@company.id
    - password : password123
3. Staff : 
    - email : agus.setiawan@company.id / citra.ayu@company.id / eko.prasetyo@company.id / fitriani@company.id
    - password : password123

---

## Informasi Tambahan
- Framework: Laravel 10
- Admin Panel: Filament v3
- Role & Permission: Filament Shield
- Alur RBAC menggunakan `spatie/laravel-permission`

---
