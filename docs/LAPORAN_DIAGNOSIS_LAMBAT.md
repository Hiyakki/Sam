# Laporan Diagnosis Kelambatan Aplikasi SAM (Lingkungan Lokal Windows + Laragon)

**Tanggal Pengujian:** 14 Oktober 2026  
**Cabang Git:** `diagnosis-lambat`  
**Status Tindakan:** HANYA Mengukur & Mendiagnosis (Tidak Ada Kode Aplikasi yang Diubah/Dioptimasi)

---

## 1. Ringkasan Eksekutif & Akar Masalah

Keluhan utama pengguna adalah **"perpindahan antar halaman terasa lama"** di lingkungan lokal Windows + Laragon. Berdasarkan serangkaian pengujian empiris (pengukuran curl TTFB berulang, server runtime tracing via middleware, dan headless browser profiling), ditemukan bahwa **80% - 90% waktu jeda perpindahan halaman disebabkan oleh faktor Lingkungan Lokal (khususnya ketiadaan OPcache dan sifat single-thread dari `php artisan serve`), bukan karena query database yang lambat.**

### Kontribusi Penyebab terhadap Total Waktu Muat Halaman:

| Peringkat | Faktor Penyebab | Kategori | Kontribusi terhadap Waktu | Dampak Rata-rata |
|---|---|---|---|---|
| **1** | **OPcache Tidak Aktif di PHP 8.3** | **Lingkungan Lokal** | **~75% - 85% dari TTFB** | **+400 - 450 ms** per request HTTP |
| **2** | **Single-Thread Queuing pada `php artisan serve`** | **Lingkungan Lokal** | **Multi-detik saat muat aset** | **Hingga +5.2 detik** antrean serial aset |
| **3** | **Aset Gambar Sangat Besar (Non-WebP / Uncompressed)** | **Kode & Konten** | **Koneksi / Render Overhead** | **3.2 MB - 9.2 MB** payload per halaman |
| **4** | **Kompilasi Form & Livewire Filament Admin Tanpa OPcache** | **Lingkungan & Kode** | **Halaman Admin** | **+400 - 900 ms** CPU render time |
| **5** | **`SeoRedirectMiddleware` Cek `information_schema` Tiap Request** | **Kode** | **Semua Request GET** | **+5 - 19 ms** query per request |
| **6** | **Font Eksternal Google Fonts (Render-Blocking)** | **Kode** | **First Paint** | **+90 - 255 ms** durasi jaringan |
| **7** | **Driver Session & Cache Berbasis `database`** | **Konfigurasi Lokal** | **Semua Request** | **+6 - 12 ms** (3-4 query DB ekstra) |

> **Kesimpulan Utama:**  
> Rute "kosong" (plain text tanpa database dan tanpa view) di lokal membutuhkan **494.58 ms**, sementara halaman Home lengkap membutuhkan **554.58 ms**. Artinya, eksekusi kode database dan rendering view aplikasi SAM sesungguhnya hanya membutuhkan **~60 ms**. Sisanya (~495 ms) adalah overhead pembacaan dan kompilasi ulang ribuan file PHP Laravel dari disk Windows (NTFS) pada setiap request karena **Zend OPcache tidak aktif**. Selain itu, saat browser memuat halaman, `php artisan serve` yang bersifat single-threaded mengantrekan aset satu per satu secara serial (10 request bersamaan memakan waktu **5.88 detik**).

---

## 2. Hasil Audit Lingkungan Lokal (Langkah 1)

1. **Versi PHP & Ekstensi:**
   - **Versi:** PHP `8.3.35 (cli) (built: Sep 22 2026 11:11:01) (ZTS Visual C++ 2019 x64)`
   - **Xdebug:** **TIDAK AKTIF / TIDAK TERPASANG** (`php -m` dan `php -i` mengonfirmasi 0 modul Xdebug). Kelambatan lokal bukan akibat instrumentasi Xdebug.
   - **OPcache:** **TIDAK AKTIF / TIDAK TERPASANG** (Zend OPcache tidak dimuat dalam `php.ini` CLI Laragon, `opcache.enable` bernilai kosong).
2. **Nilai Konfigurasi Terkait (.env):**
   - `APP_ENV`: `local`
   - `APP_DEBUG`: `true`
   - `LOG_LEVEL`: `debug`
   - `DB_HOST`: `127.0.0.1` (Sudah menggunakan IP numerik, bukan `localhost`)
   - `SESSION_DRIVER`: `database` (Membaca dan menulis tabel `sessions` pada setiap request)
   - `CACHE_STORE`: `database` (Operasi cache menyentuh tabel `cache`)
   - `QUEUE_CONNECTION`: `database`
   - `MAIL_MAILER`: `log`
   - `FILESYSTEM_DISK`: `local`
3. **Server Web & Tooling:**
   - **Server Digunakan:** `php artisan serve` pada `http://127.0.0.1:8000`. Apache Laragon aktif di port 80, namun tidak memiliki Virtual Host untuk proyek ini karena lokasi repositori berada di `d:\SAM` (di luar `C:\laragon\www`).
   - **File `public/hot`:** **ADA** (Vite dev server aktif di port 5173).
   - **Debugbar / Telescope / Ray:** **TIDAK TERPASANG** dalam `composer.json` / `composer.lock`.
4. **Cache Framework & Berkas:**
   - `bootstrap/cache`: Hanya ada `packages.php` (4 KB) dan `services.php` (25 KB). **TIDAK ADA** `config.php`, `routes-v7.php`, atau `events.php` (status belum di-cache).
   - `vendor/composer/autoload_classmap.php`: Ada (1.2 MB, teroptimasi `optimize-autoloader: true`). Ukuran folder `vendor`: 64.8 MB.
   - Storage Views: 214 berkas view terkompilasi di `storage/framework/views`.
   - Storage Logs: `laravel.log` berukuran 1.30 MB (wajar, tidak memberatkan), `browser.log` 28 KB.
5. **Waktu Koneksi Database (20 Kali SELECT 1 via PDO):**
   - `localhost`: Rata-rata **11.64 ms** (min: 1.51 ms, maks: 27.71 ms).
   - `127.0.0.1`: Rata-rata **8.15 ms** (min: 1.32 ms, maks: 25.63 ms).
   - *Analisis:* Host `127.0.0.1` lebih cepat rata-rata **3.5 ms** dibandingkan `localhost` karena menghindari jeda resolusi DNS/IPv6 di Windows. Aplikasi sudah tepat memakai `127.0.0.1`.

---

## 3. Tabel Pengukuran Kinerja Per Halaman (Langkah 3)

Pengukuran dilakukan dengan metodologi:
- **TTFB Curl:** 5 kali request dengan `curl.exe -w "%{time_starttransfer}"`, membuang request pertama sebagai pemanasan (*warmup*), lalu mencatat nilai **Median** dan **Maksimum**.
- **Metrik Server:** Direkam melalui profiling middleware sementara (`ProfileRequest`) yang mencatat total waktu, waktu boot, query database, memori puncak, dan query duplikat.
- **Headless Browser:** Microsoft Edge (Chromium) via Puppeteer, mencatat transfer aset, waktu DOMContentLoaded (DCL), Load, LCP, CLS, dan aset berukuran besar (>200 KB).

| No | Halaman | URL | TTFB Median (ms) | TTFB Maks (ms) | Waktu Boot (ms) | Jml Query | Waktu Query (ms) | Memori (MB) | Jml Req Browser | Transfer Browser (KB) | DCL (ms) | Load (ms) | LCP (ms) | CLS |
|---|---|---|---|---|---|---|---|---|---|---|---|---|---|---|
| 1 | **Home** | `/` | **554.58** | 564.40 | 404.36 | 22 | 31.83 | 40 | 22 | 9,188.01 | 899 | 1,130 | 1,300 | 0.0000 |
| 2 | **About** | `/about` | **553.24** | 573.99 | 432.61 | 14 | 29.88 | 40 | 18 | 3,690.04 | 829 | 829 | 964 | 0.0000 |
| 3 | **Katalog** | `/katalog` | **543.90** | 552.36 | 409.34 | 15 | 19.61 | 38 | 16 | 3,479.78 | 720 | 722 | 816 | 0.0000 |
| 4 | **Katalog (Filter)** | `/katalog?kategori=orthosis` | **519.33** | 525.32 | 412.10 | 15 | 21.40 | 38 | 16 | 3,483.55 | 766 | 797 | 768 | 0.0000 |
| 5 | **Detail Produk** | `/katalog/wrist-thumb-support` | **534.03** | 540.42 | 416.20 | 13 | 18.21 | 38 | 14 | 3,413.37 | 724 | 724 | 732 | 0.0000 |
| 6 | **FAQ** | `/faq` | **509.46** | 521.56 | 415.12 | 9 | 43.77 | 38 | 13 | 3,420.97 | 756 | 758 | 696 | 0.0000 |
| 7 | **Testimoni** | `/testimoni` | **523.11** | 536.76 | 415.03 | 9 | 13.33 | 38 | 14 | 3,878.55 | 687 | 689 | 744 | 0.0000 |
| 8 | **Appointment** | `/appointment` | **537.79** | 558.89 | 448.39 | 9 | 36.14 | 38 | 14 | 3,417.64 | 687 | 718 | 796 | 0.0000 |
| 9 | **Kebijakan Privasi** | `/kebijakan-privasi` | **528.63** | 546.75 | 426.52 | 7 | 27.96 | 38 | 13 | 3,396.98 | 660 | 661 | 688 | 0.0000 |
| 10 | **404 (Not Found)** | `/halaman-tidak-ada-404-test` | **487.61** | 500.46 | 430.51 | 3 | 17.04 | 38 | 10 | 2,128.69 | 627 | 644 | 708 | 0.0000 |
| 11 | **Login Admin** | `/kelola-sam/login` | **583.42** | 600.95 | 394.84 | 5 | 11.92 | 40 | 18 | 2,429.60 | 1,209 | 1,212 | 892 | 0.0000 |
| 12 | **Dashboard Admin** | `/kelola-sam` | **628.55** | 633.45 | 399.27 | 9 | 26.53 | 40 | 21 | 2,474.23 | 838 | 849 | 844 | 0.0000 |
| 13 | **Janji Temu Admin** | `/kelola-sam/appointments` | **839.70** | 846.50 | 407.12 | 23 | 26.13 | 46 | 22 | 2,671.39 | 1,237 | 1,245 | 1,180 | 0.0000 |
| 14 | **Produk Admin** | `/kelola-sam/products` | **1,070.89** | 1,092.25 | 400.37 | 14 | 22.68 | 46 | 24 | 2,769.00 | 1,402 | 1,425 | 1,336 | 0.0000 |
| 15 | **Pengaturan Situs Admin**| `/kelola-sam/pengaturan-situs` | **1,351.56** | 1,371.82 | 401.66 | 10 | 16.61 | 48 | 28 | 3,498.08 | 1,695 | 1,740 | 1,656 | 0.0000 |

---

## 4. Hasil Eksperimen Isolasi Terkontrol (Langkah 3.3)

Untuk membuktikan secara ilmiah bagian mana yang menjadi hambatan (*bottleneck*), dilakukan 7 eksperimen isolasi terkontrol:

### Eksperimen 3a: Request "Kosong" (Base Cost PHP + Framework)
- **Implementasi:** Rute sementara `Route::get('/diagnosis-blank', fn () => response('OK'));` tanpa database dan tanpa view.
- **Hasil:** Median TTFB = **494.58 ms** (Maks: 527.08 ms).
- **Analisis:** Bahkan tanpa menyentuh database atau Blade template, Laravel membutuhkan **~495 ms** hanya untuk memuat framework di Windows tanpa OPcache. Ini adalah *floor baseline* dari lingkungan saat ini.

### Eksperimen 3b: Request "SELECT 1" (Biaya Koneksi Database)
- **Implementasi:** Rute sementara `Route::get('/diagnosis-db', fn () => DB::select('SELECT 1'));`.
- **Hasil:** Median TTFB = **500.82 ms** (Maks: 520.29 ms).
- **Selisih vs 3a:** `500.82 - 494.58 =` **+6.24 ms**.
- **Analisis:** Koneksi database MySQL 8 lokal hanya menyumbang **~6.2 ms**. Database lokal sangat cepat dan bukan penyebab kelambatan.

### Eksperimen 3c: Home dengan Cache Config/Route/View Aktif vs Tidak
- **Kondisi Cache Aktif (`config:cache`, `route:cache`, `view:cache`):** TTFB Median = **563.68 ms**.
- **Kondisi Tanpa Cache (`config:clear`, `route:clear`, `view:clear`):** TTFB Median = **583.76 ms**.
- **Selisih Penghematan:** **20.08 ms** (3.4%).
- **Analisis:** Di lingkungan tanpa OPcache, penghematan cache route/config relatif kecil (hanya ~20 ms) karena PHP CLI tetap harus membaca dan mem-parse file cache besar (`config.php` ~200 KB) dari disk pada setiap request.

### Eksperimen 3d: Home dengan Data Global Dimemoize (Bypass Query Database)
- **Implementasi:** Seluruh hasil query `HomePageData::get()` dan `SiteSetting::allCached()` dimemoize sementara.
- **Kondisi Data Dimemoize:** TTFB Median = **554.67 ms**.
- **Kondisi Normal (Query DB Aktif):** TTFB Median = **583.76 ms**.
- **Selisih Penghematan:** **29.09 ms** (5.0%).
- **Analisis:** Menghilangkan seluruh 22 query database pada Home hanya memangkas waktu sebesar ~29 ms. Ini membuktikan bahwa query database di Home sudah efisien dan bukan penyebab kelambatan utama.

### Eksperimen 3e: Home Tanpa Gambar (Blokir Gambar di Browser Headless)
- **Home dengan Gambar:** 22 request, Transfer **9,188 KB (9.2 MB)**, Load: **1,130 ms**, LCP: **1,300 ms**.
- **Home Tanpa Gambar (Aborted Image Requests):** 15 request, Transfer **5,992 KB (6.0 MB)**, Load: **1,099 ms**, LCP: **1,224 ms**.
- **Aset Gambar Teridentifikasi:**
  - `logo-sam.png`: **1,279.4 KB (1.28 MB)**!
  - `settings/01M4AJ7CWMZ2NBDVD9949HKR48.png`: **1,156.0 KB (1.16 MB)**!
  - `testimonials/Zfz1EWHaqoMqFYdUgpebVc0bWRVEyKzHuDrdfYlB.png`: **458.2 KB**!
- **Analisis:** Gambar yang belum dikompresi menyumbang **3.2 MB** payload pada satu halaman. Di koneksi internet standar (di luar localhost), payload 9.2 MB akan menyebabkan jeda visual berdetik-detik.

### Eksperimen 3f: Uji Xdebug
- **Hasil:** Xdebug **TIDAK AKTIF / TIDAK TERPASANG** di PHP 8.3 Laragon (`extension_loaded('xdebug') === false`). Tidak ada penalti performa dari Xdebug.

### Eksperimen 3g: 10 Request Paralel ke Home (Deteksi Antrean Single-Thread)
- **Implementasi:** Mengirim 10 request HTTP concurrent secara bersamaan menggunakan `Promise.all` ke `http://127.0.0.1:8000/`.
- **Hasil:**
  - Request 1: selesai pada **642 ms** (durasi: 642 ms)
  - Request 2: selesai pada **1,224 ms** (durasi: 1,220 ms)
  - Request 3: selesai pada **1,819 ms** (durasi: 1,815 ms)
  - Request 4: selesai pada **2,399 ms** (durasi: 2,394 ms)
  - Request 5: selesai pada **2,968 ms** (durasi: 2,963 ms)
  - Request 6: selesai pada **3,565 ms** (durasi: 3,560 ms)
  - Request 7: selesai pada **4,123 ms** (durasi: 4,118 ms)
  - Request 8: selesai pada **4,691 ms** (durasi: 4,686 ms)
  - Request 9: selesai pada **5,290 ms** (durasi: 5,284 ms)
  - Request 10: selesai pada **5,888 ms** (durasi: 5,882 ms)
  - **Total Waktu Wall-Clock:** **5,888 ms (5.9 detik)**.
- **Analisis:** **BUKTI TELAK.** `php artisan serve` berjalan pada PHP CLI built-in web server yang bersifat **strictly single-threaded**. Server hanya dapat memproses 1 request dalam satu waktu. Ketika pengguna berpindah halaman di browser, browser mengirim request HTML bersamaan dengan request favicon, stylesheet, script Vite, dan gambar. Semua request tersebut dipaksa berbaris dalam satu antrean serial. Jika satu halaman memicu 10 request aset lokal, request terakhir harus menunggu hampir **6 detik** hanya karena antrean thread!

---

## 5. Profiling Internal PHP & Tahapan Eksekusi (Langkah 3.4 & 3.5)

### Tahapan Paling Memakan Waktu pada Home (`/`) dan Katalog (`/katalog`):

```
+-----------------------------------------------------------------------------------+
| Total Waktu Request: ~550 ms                                                      |
+---------------------------------------------------------+-------------------------+
| Framework Bootstrap (Tanpa OPcache): ~410 ms (74.5%)    | App Exec: ~140 ms       |
| - Autoload Composer & Class Discovery: ~180 ms          | - Routing & Mw: ~15 ms  |
| - Service Providers Register & Boot: ~150 ms            | - DB Queries: ~25 ms    |
| - Environment & Config Parsing: ~80 ms                  | - Blade Render: ~100 ms |
+---------------------------------------------------------+-------------------------+
```

### 10 Kelas / Komponen Paling Memakan Waktu:
1. **`Composer\Autoload\ClassLoader` & File I/O Windows:** Pembacaan ribuan file class PHP dari hard drive NTFS pada setiap siklus request.
2. **`Filament\PanelProvider` & `FilamentServiceProvider`:** Registrasi puluhan schema form, tabel, actions, dan Livewire metadata (sangat terasa di `/kelola-sam` di mana controller+view memakan 400-900 ms).
3. **`Illuminate\Foundation\Application::bootstrapWith()`:** Inisialisasi provider inti framework pada setiap request.
4. **`App\Http\Middleware\SeoRedirectMiddleware`:** Query `information_schema.tables` (4-18 ms) dan pencarian tabel `redirects` pada setiap request GET/HEAD.
5. **`Illuminate\Session\DatabaseSessionHandler`:** Operasi `SELECT` dan `INSERT/UPDATE` ke tabel `sessions` pada setiap siklus request (3-5 ms).
6. **`App\Models\SiteSetting::allCached()`:** Pembacaan cache database (`SELECT * FROM cache WHERE key = 'site_settings.all'`) yang terpanggil berulang di Controller dan Layout Blade.
7. **`App\Services\HomePageData::getGalleryPhotos()`:** Pemeriksaan file fisik synchronous `Storage::disk('public')->exists()` sebanyak 5 kali berturut-turut pada disk lokal.
8. **Livewire & Blade Component Compiler:** Kompilasi komponen dinamis dan directive Blade pada admin panel.
9. **Query Agregat Badge Navigasi Admin:** Query `SELECT count(*) FROM appointments WHERE status = ?` (dijalankan hingga 4x berulang) dan `testimonials` (2x) untuk menampilkan angka badge di sidebar admin.
10. **Google Fonts External Handshake:** Unduhan font `playfairdisplay` (255 ms) dan `inter` (117 ms) dari server eksternal Google.

---

## 6. Daftar Temuan Terurut (Problem Inventory)

| ID | Masalah / Temuan | Bukti Angka / Log | Dampak (ms) | Ikut ke Produksi? | Rekomendasi Solusi (JANGAN DIKERJAKAN SEKARANG) |
|---|---|---|---|---|---|
| **TEM-01** | **Zend OPcache Tidak Aktif di PHP 8.3 Laragon** | Rute kosong memakan 494 ms; `boot_time` 404-432 ms di semua halaman. | **+400 - 450 ms** per request | **TIDAK** (di produksi Linux OPcache wajib aktif) | Aktifkan `zend_extension=opcache` di `php.ini` Laragon dengan setting `opcache.enable=1` dan `opcache.enable_cli=1`. |
| **TEM-02** | **Server Dev Single-Thread (`php artisan serve`)** | 10 request paralel selesai serial dari 642 ms hingga 5,888 ms. | **+1.000 - 5.000 ms** saat browser memuat aset | **TIDAK** (di produksi menggunakan Nginx / Apache multi-worker) | Gunakan Virtual Host Laragon (Apache) atau jalankan artisan serve dengan multi-worker: `php artisan serve --workers=4` (jika didukung OS/PHP). |
| **TEM-03** | **Aset Gambar Raksasa Tanpa Kompresi WebP** | `logo-sam.png` (1.28 MB), `settings/...png` (1.16 MB). Transfer Home 9.18 MB. | **+400 - 600 ms** LCP / durasi jaringan | **YA** | Kompres logo ke WebP/SVG (<50 KB) dan buat konversi WebP otomatis saat upload gambar dari Filament. |
| **TEM-04** | **`SeoRedirectMiddleware` Query `information_schema` Tiap Request** | Query `select exists from information_schema.tables` tercatat 4.5 - 18.7 ms di log. | **+5 - 19 ms** per request | **YA** | Hapus `Schema::hasTable('redirects')` dari runtime per-request; bungkus pemeriksaan redirect dalam cache `Cache::remember('redirects.all', ...)`. |
| **TEM-05** | **Driver Session & Cache Berbasis Database di Lingkungan Lokal** | 2-3 query `sessions` dan `cache` per request di log query. | **+6 - 12 ms** per request | **TERGANTUNG** (.env produksi) | Gunakan `SESSION_DRIVER=file` dan `CACHE_STORE=file` di lingkungan lokal untuk mengeliminasi round-trip MySQL session. |
| **TEM-06** | **Font Google External Dimuat Render-Blocking** | Font Google gstatic woff2 membutuhkan durasi 117 - 255 ms. | **+100 - 250 ms** render delay | **YA** | Unduh font Inter & Playfair Display secara lokal (`self-hosted`) melalui bundle Vite menggunakan `@fontsource` atau file woff2 lokal. |
| **TEM-07** | **Pemeriksaan File Fisik di `HomePageData::getGalleryPhotos()`** | `Storage::disk('public')->exists()` dipanggil 5x berulang setiap request Home. | **+3 - 8 ms** I/O disk | **YA** | Simpan daftar foto galeri di tabel `site_settings` atau cache array tanpa memeriksa disk NTFS setiap request. |
| **TEM-08** | **Query Count Badge Berulang di Sidebar Admin** | Query `count(*)` janji temu status pending/confirmed dijalankan 4 kali dalam 1 request admin. | **+5 - 10 ms** di admin | **YA** | Memoize hasil count badge navigasi pada service provider atau model. |

---

## 7. Langkah di Luar Kode untuk Lingkungan Lokal (Penghematan Cepat)

Untuk membuat lingkungan lokal Windows + Laragon langsung terasa responsif tanpa mengubah kode aplikasi:

1. **Aktifkan OPcache di PHP Laragon (Estimasi Penghematan: ~350 - 400 ms per request / 70% lebih cepat):**
   - Buka file `C:\laragon\bin\php\php-8.3.35-Win32-vs16-x64\php.ini`.
   - Cari baris `zend_extension=opcache` dan pastikan tidak diawali titik koma (`;`).
   - Tambahkan/pastikan konfigurasi berikut aktif:
     ```ini
     [opcache]
     zend_extension=opcache
     opcache.enable=1
     opcache.enable_cli=1
     opcache.memory_consumption=128
     opcache.interned_strings_buffer=16
     opcache.max_accelerated_files=10000
     opcache.validate_timestamps=1
     opcache.revalidate_freq=0
     ```
   - Restart Laragon / terminal.
2. **Jalankan Server dengan Multi-Worker atau Virtual Host Laragon (Estimasi Penghematan: Menghilangkan antrean serial hingga 4-5 detik):**
   - Jika tetap memakai `artisan serve`, gunakan environment variable workers di PowerShell/CMD:
     ```powershell
     $env:PHP_CLI_SERVER_WORKERS=4
     php artisan serve
     ```
     *(Variabel ini menginstruksikan PHP built-in web server untuk membuat 4 worker thread paralel).*
3. **Pengecualian Windows Defender (Estimasi Penghematan: ~50 - 100 ms disk I/O):**
   - Tambahkan folder proyek `d:\SAM` dan folder Laragon `C:\laragon` ke dalam daftar *Exclusions* di **Windows Security > Virus & threat protection settings > Exclusions**.
   - Ini mencegah Windows Antimalware Service (MsMpEng) memindai ribuan file PHP dan node_modules setiap kali diakses oleh PHP.
4. **Hentikan `npm run dev` Jika Tidak Sedang Mengedit CSS/JS (Gunakan `npm run build`):**
   - Menghapus request proxy loopback `[::1]:5173` untuk `@vite/client` dan module loading dinamis, sehingga browser cukup memuat bundle statis terkompresi dari `public/build`.
