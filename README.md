# Project Management (projectm)

Aplikasi manajemen proyek berbasis Laravel 11+.

## Cara Menjalankan Proyek di Lokal

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di komputer Anda:

### 1. Kloning Repositori
```bash
git clone https://github.com/flagsaka-prog/projectm.git
cd projectm
```

### 2. Install Dependencies
```bash
composer install
npm install && npm run dev
```

### 3. Konfigurasi Environment File
Salin file `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
*Buka file `.env` yang baru dibuat dan sesuaikan konfigurasi database Anda.*

### 4. Setup Database & Generate Key
1. Buat database baru kosong di MySQL Anda (misal melalui phpMyAdmin) dengan nama `projectm`.
2. Buka file `.env` Anda, lalu sesuaikan bagian konfigurasi database berikut:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=projectm
   DB_USERNAME=root
   DB_PASSWORD=
   ```
3. Jalankan perintah berikut di terminal untuk generate key dan mengisi struktur tabel beserta data awal:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   ```

### 5. Jalankan Server
```bash
php artisan serve
```
Buka `http://127.0.0.1:8000` di browser Anda.




