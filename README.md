
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
   DB_DATABASE=pmo
   DB_USERNAME=root
   DB_PASSWORD=root
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

<img width="1351" height="601" alt="Untitled 1" src="https://github.com/user-attachments/assets/7de99707-c7e4-4b2b-be9c-6c3dce063f54" />
<img width="1347" height="593" alt="Untitled" src="https://github.com/user-attachments/assets/c9579dcd-dfeb-4213-a01c-c3e8c6153b79" />
<img width="913" height="645" alt="Untitled 3" src="https://github.com/user-attachments/assets/8c2a3ea7-8182-4f8a-b948-bd1f1b2368a4" />
<img width="1363" height="645" alt="Untitled 2" src="https://github.com/user-attachments/assets/52d66a82-a01c-47b9-9e29-ecb9e70432d3" />


