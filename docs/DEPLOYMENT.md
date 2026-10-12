# Panduan Deployment & Prosedur Restore Backup SAM Website

Dokumen ini memuat panduan deployment production dan prosedur pemulihan bencana (*Disaster Recovery / Restore*) untuk aplikasi website SAM Orthotic Prosthetic.

---

## 1. Persyaratan Server Production

- **OS:** Ubuntu 22.04 LTS / 24.04 LTS atau Linux setara.
- **PHP:** Versi 8.3 dengan ekstensi: `bcmath`, `ctype`, `curl`, `dom`, `fileinfo`, `filter`, `hash`, `intl`, `json`, `mbstring`, `openssl`, `pcre`, `pdo`, `pdo_mysql`, `session`, `tokenizer`, `xml`, `zip`.
- **Database:** MySQL 8.0+ dengan karakter `utf8mb4` dan collation `utf8mb4_unicode_ci`.
- **Web Server:** Nginx dengan SSL/TLS (Let's Encrypt).
- **Node.js & npm:** Node 20+ untuk build asset frontend.
- **mysqldump / mysql-client:** Terpasang pada PATH server untuk kebutuhan backup dan restore database.

---

## 2. Jadwal Otomatis (Cron & Queue)

### Cron Job (Laravel Scheduler)
Daftarkan cron job pada user web server (misal `www-data`):
```bash
* * * * * cd /path/to/sam && php artisan schedule:run >> /dev/null 2>&1
```

Jadwal task yang otomatis berjalan di scheduler:
1. `appointment:send-reminders` — Setiap hari pukul 08:00 WIB (pengingat H-1 ke pasien).
2. `appointment:send-daily-summary` — Setiap hari pukul 07:30 WIB (ringkasan agenda admin jika ada janji).
3. `sam:purge-old-data` — Mingguan setiap Minggu pukul 02:00 WIB (retensi data > 24 bulan).
4. `backup:clean` — Harian pukul 01:00 WIB (membersihkan backup lama > 14 hari).
5. `backup:run` — Harian pukul 01:30 WIB (backup database & `storage/app/private`).

### Queue Worker (Supervisor)
Jalankan queue worker agar email dan notifikasi asynchronous terkirim:
```ini
[program:sam-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/sam/artisan queue:work database --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/sam/storage/logs/worker.log
stopwaitsecs=3600
```

---

## 3. Sistem Backup (`spatie/laravel-backup`)

- **Komponen yang dibackup:**
  1. Seluruh database MySQL (`sam_website`).
  2. Direktori privat penyimpanan resep dokter (`storage/app/private`).
- **Lokasi Penyimpanan:** `storage/app/private/Laravel/` (atau disk backup yang dikonfigurasi).
- **Retensi:** Disimpan otomatis selama 14 hari terakhir.
- **Notifikasi Kegagalan:** Email peringatan otomatis dikirim jika backup atau cleanup gagal.

Untuk menjalankan backup manual:
```bash
php artisan backup:run
```
Untuk backup database saja:
```bash
php artisan backup:run --only-db
```

---

## 4. Langkah-Demi-Langkah Restore Backup

Jika terjadi kegagalan server, kerusakan data, atau migrasi ke server baru, ikuti prosedur berikut:

### Langkah 1: Siapkan Mode Pemeliharaan
Aktifkan mode maintenance agar tidak ada data baru yang masuk selama proses restore:
```bash
php artisan down --message="Sedang dilakukan pemeliharaan sistem. Silakan coba beberapa saat lagi."
```

### Langkah 2: Temukan File Backup Terbaru
Arsip backup tersimpan dalam format `.zip` di direktori backup:
```bash
ls -la storage/app/private/Laravel/*.zip
```
Pilih file zip terbaru, misalnya: `storage/app/private/Laravel/2026-10-12-01-30-00.zip`.

### Langkah 3: Ekstraksi Arsip Backup ke Folder Sementara
```bash
mkdir -p storage/app/restore_temp
unzip storage/app/private/Laravel/2026-10-12-01-30-00.zip -d storage/app/restore_temp
```
Di dalam folder `storage/app/restore_temp` akan terdapat:
- Folder `db-dumps/mysql-sam_website.sql` (file dump database).
- Folder `storage/app/private/prescriptions/` (kumpulan file resep dokter pasien).

### Langkah 4: Restore Database MySQL
Pastikan kredensial database sesuai dengan `.env`:
```bash
# Ambil nilai dari .env atau masukkan langsung
DB_USER=$(grep DB_USERNAME .env | cut -d '=' -f2)
DB_PASS=$(grep DB_PASSWORD .env | cut -d '=' -f2)
DB_NAME=$(grep DB_DATABASE .env | cut -d '=' -f2)

# Impor kembali dump database
mysql -u "$DB_USER" -p"$DB_PASS" "$DB_NAME" < storage/app/restore_temp/db-dumps/mysql-*.sql
```

### Langkah 5: Restore File Resep Privat
Salin kembali file resep dokter ke direktori privat aplikasi:
```bash
cp -r storage/app/restore_temp/storage/app/private/* storage/app/private/
```
Pastikan kepemilikan dan hak akses file benar:
```bash
chown -R www-data:www-data storage/app/private
chmod -R 750 storage/app/private
```

### Langkah 6: Bersihkan Folder Sementara & Cache Aplikasi
```bash
rm -rf storage/app/restore_temp

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Langkah 7: Nonaktifkan Mode Pemeliharaan
```bash
php artisan up
```

### Langkah 8: Verifikasi Hasil Restore
1. Buka browser dan login ke panel admin (`/kelola-sam`).
2. Periksa daftar **Janji Temu**: pastikan record janji temu muncul lengkap.
3. Klik tombol unduh resep pada salah satu janji temu yang memiliki resep: pastikan file PDF/gambar berhasil diunduh dan tidak corrupt.
4. Cek halaman Beranda publik (`/`): pastikan konten dan katalog tampil normal.
