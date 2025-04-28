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

## Alur Sistem (Flowchart)

Sistem ini mengikuti flow:
- Proses login dan validasi credential
- Role menentukan akses fitur
- Akses menu sesuai hak masing-masing

> Lihat file: [`/dokumen/flowchart.pdf`](./dokumen/flowchart.pdf)

---

## Cara Instalasi & Menjalankan Sistem Lokal

### 1. Clone Repository
```bash
git clone https://github.com/your-username/your-repo-name.git
cd your-repo-name
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
- Jalankan migrasi
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
    - email : ani.wijaya@company.id
    - password : password123
3. Staff : 
    - email : agus.setiawan@company.id
    - password : password123

---

## Informasi Tambahan
- Framework: Laravel 10
- Admin Panel: Filament v3
- Role & Permission: Filament Shield
- Alur RBAC menggunakan `spatie/laravel-permission`

---

## Folder Struktur

```
root/
├── app/
├── database/
├── routes/
├── public/
├── dokumen/
│   ├── ERD.pdf
│   ├── flowchart.pdf
│   ├── penjelasan_fitur.md
│   └── catatan_debugging.md (opsional)
├── README.md
