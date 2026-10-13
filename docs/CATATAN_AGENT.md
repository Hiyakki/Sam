# Catatan Hardening & Pelengkapan Fitur Aplikasi SAM

Dokumen ini mencatat seluruh implementasi keamanan, optimasi, dan fitur baru pada cabang git `hardening-fitur`, membedakan butir yang telah ada sebelumnya vs yang baru dibuat, serta merinci konfigurasi yang perlu diisi oleh administrator/pengembang.

---

## Ringkasan Perubahan per Butir

### 1. Anti-Spam Form Appointment
- **Status Sebelumnya:** Sudah ada validasi dasar dan honeypot input, namun belum ada proteksi Cloudflare Turnstile, belum ada rate limiter ketat per nomor HP/IP, dan belum ada pembatasan jumlah janji aktif per nomor telepon.
- **Implementasi Baru:**
  - **Cloudflare Turnstile:** Dibuat validasi `App\Rules\TurnstileRule`. Captcha nonaktif otomatis jika `TURNSTILE_SECRET_KEY` tidak diatur di environment, sehingga tidak mengganggu lingkungan pengujian/lokal. Widget Turnstile terpasang di `resources/views/pages/appointment.blade.php`.
  - **Rate Limiting Ganda:** Membatasi maksimal 3 pengajuan per jam berdasarkan IP (`RateLimiter::tooManyAttempts('appointment-submit-ip:...')`) dan berdasarkan nomor telepon yang telah dinormalisasi (`appointment-submit-phone:...`).
  - **Batas Janji Aktif per Nomor:** Membatasi maksimal 2 janji temu yang berstatus aktif (`pending` atau `confirmed`) per nomor telepon.
  - **Pesan Ramah Bahasa Indonesia:** Seluruh validasi menyajikan pesan error yang informatif dalam Bahasa Indonesia.
  - **Commit Git:** `b1d9e34` - `feat(security): anti-spam honeypot, captcha, and appointment rate limiters`

---

### 2. Admin: Ubah Jadwal & Batal (AppointmentResource Filament)
- **Status Sebelumnya:** Aksi status Filament hanya menyediakan tombol konfirmasi, selesai, dan tidak hadir secara dasar. Belum ada modal pemilihan slot waktu baru dengan penguncian transaksi DB, dan belum ada aksi pembatalan yang membebaskan slot secara otomatis dan mencatat alasan pembatalan.
- **Implementasi Baru:**
  - **Aksi "Ubah Jadwal":**
    - Menggunakan modal interaktif memilih tanggal dan jam baru yang divalidasi terhadap `SlotService` (slot penuh, hari Minggu/libur cabang diblokir).
    - Berjalan di dalam database transaction dengan pessimistic row locking (`lockForUpdate`) untuk mencegah tabrakan slot.
    - Mencatat riwayat jadwal lama dan jadwal baru di kolom `admin_notes` (contoh: `[12/10/2026 13:00] Jadwal diubah oleh Admin dari 15/10/2026 09:00 ke 16/10/2026 10:00 WIB`).
    - Mengirim notifikasi email otomatis ke pasien (`AppointmentRescheduledPatientMail`).
    - Menyediakan tombol aksi langsung untuk mengirim template WhatsApp "Penyesuaian Jadwal".
  - **Aksi "Batalkan":**
    - Konfirmasi pembatalan dengan alasan opsional yang dicatat di `admin_notes`.
    - Mengubah status ke `cancelled`, mengisi `cancelled_by = 'admin'`, dan secara otomatis mengosongkan/membebaskan slot.
    - Mengirim notifikasi email pembatalan ke pasien (`AppointmentStatusChangedPatientMail`).
    - Menyediakan tautan WhatsApp template pembatalan ke pasien.
  - **Keputusan Produk (Mengikat):** Pasien sama sekali tidak memiliki akses mengubah atau membatalkan janji temu secara mandiri dari sisi publik. Seluruh aksi perubahan hanya dapat dilakukan oleh admin dari panel `/kelola-sam`.
  - **Commit Git:** `b46aff7` - `feat(admin): reschedule and cancellation actions in AppointmentResource`

---

### 3. SEO & Structured Data
- **Status Sebelumnya:** Meta title dasar dan schema structured data sederhana di halaman beranda.
- **Implementasi Baru:**
  - **Canonical URL:** Menambahkan tag `<link rel="canonical" href="...">` dinamis di seluruh halaman melalui `layouts/app.blade.php`.
  - **Open Graph & Twitter Cards:** Ditambahkan meta tag `og:title`, `og:description`, `og:url`, `og:image`, `og:site_name`, `twitter:card`, `twitter:title`, `twitter:description`, dan `twitter:image` di semua halaman publik.
  - **JSON-LD Schema.org:**
    - `MedicalBusiness` & `LocalBusiness` per cabang di Beranda (`/`) dan Tentang Kami (`/about`).
    - `FAQPage` di halaman Pertanyaan Umum (`/faq`) yang diekstrak langsung dari model FAQ aktif.
    - `Product` schema di halaman rincian produk katalog (`/katalog/{product:slug}`) lengkap dengan gambar, deskripsi, dan ketersediaan.
  - **Commit Git:** `62d787a` - `feat(seo): canonical urls, open graph, twitter cards, and json-ld structured data`

---

### 4. Keamanan Produksi
- **Status Sebelumnya:** Panel admin sudah diproteksi path custom `/kelola-sam` dan registrasi publik ditutup. Namun belum ada paksa HTTPS otomatis di produksi, secure cookie flag dinamis, security headers middleware, dan rate limit login admin.
- **Implementasi Baru:**
  - **Force HTTPS:** Diaktifkan otomatis pada environment produksi via `AppServiceProvider::boot()` menggunakan `URL::forceScheme('https')`.
  - **Secure Cookie:** Mengatur session cookie secure flag di `config/session.php` bernilai `true` saat di environment produksi (`env('SESSION_SECURE_COOKIE', env('APP_ENV') === 'production')`).
  - **Security Headers Middleware:** Memastikan respons menyertakan header keamanan modern:
    - `X-Frame-Options: SAMEORIGIN` (mencegah clickjacking)
    - `X-Content-Type-Options: nosniff` (mencegah MIME sniffing)
    - `Referrer-Policy: strict-origin-when-cross-origin`
    - `Permissions-Policy: camera=(), microphone=(), geolocation=()`
  - **Rate Limiting Login Admin:** Dibuat class kustom `App\Filament\Pages\Auth\Login` yang membatasi maksimal 5 percobaan login per menit dengan pesan peringatan berbahasa Indonesia.
  - **Proteksi File Resep Dokter:** File resep disimpan di disk privat (`local`), tidak dapat diakses melalui URL storage publik. Hanya dapat diunduh oleh admin terotentikasi melalui `PrescriptionDownloadController`. Diterapkan validasi MIME sesungguhnya (`image/jpeg,image/png,application/pdf`).
  - **Sanitasi Log:** Dipastikan tidak ada nomor HP atau isi pesan pasien yang bocor di file log (`NullSender` dan `FonnteSender` menyamarkan nomor telepon menjadi format sensorik, contoh: `62812****890`).
  - **Halaman Konfirmasi Aman:** Menggunakan kode unik minimal 10 karakter acak (`SAM-XXXXXXXX`), tidak menampilkan keluhan/data medis sensitif, dan menyamarkan nomor HP pasien.
  - **Catatan 2FA Admin:** Filament 3.2 core tidak menyertakan modul 2FA bawaan tanpa pustaka pihak ketiga atau Laravel Fortify. Untuk keamanan maksimal di server hosting, disarankan mengintegrasikan paket `filament-breezy` atau Fortify jika multi-factor authentication berbasis aplikasi TOTP (Google Authenticator) diwajibkan.
  - **Commit Git:** `14216a7` - `feat(security): production security hardening, headers, login rate limit, and secure downloads`

---

### 5. Retensi & Anonimisasi Data Pasien
- **Status Sebelumnya:** Belum ada mekanisme pembersihan otomatis data lama.
- **Implementasi Baru:**
  - Dibuat Artisan command `sam:purge-old-data {--months=} {--dry-run}` (`App\Console\Commands\PurgeOldDataCommand`).
  - Periode retensi dapat diatur via `config('app.data_retention_months')` (default 24 bulan).
  - Menghapus fisik file resep dari disk lokal dan menganonimkan data identitas pasien (`patient_name = 'Pasien Anonim'`, `patient_phone = '620000000000'`, `patient_email = 'anonim@sam.internal'`, `complaint = '[Data keluhan dianonymkan]'`) pada seluruh janji temu berstatus `completed`, `cancelled`, atau `no_show` yang melampaui masa retensi.
  - Terdaftar di Laravel Scheduler mingguan (`routes/console.php`) setiap hari Minggu pukul 02:00 WIB.
  - **Commit Git:** `e33762c` - `feat(retention): command sam:purge-old-data for prescription file deletion and patient anonymization`

---

### 6. Sistem Backup Otomatis
- **Status Sebelumnya:** Belum ada paket backup otomatis terpasang.
- **Implementasi Baru:**
  - Menginstal paket `spatie/laravel-backup`.
  - Mengonfigurasi `config/backup.php`: mencadangkan database MySQL serta direktori privat `storage/app/private`.
  - Retensi backup diset selama 14 hari (`keep_all_backups_for_days => 14`).
  - Mengaktifkan notifikasi email kegagalan backup ke `ADMIN_NOTIFICATION_EMAIL`.
  - Menjadwalkan `backup:clean` harian pukul 01:00 WIB dan `backup:run` pukul 01:30 WIB di `routes/console.php`.
  - Menyusun panduan pemulihan (restore) database dan file pada situasi darurat di `docs/DEPLOYMENT.md`.
  - **Commit Git:** `15d3595` - `feat(backup): install spatie/laravel-backup, configure retention and daily schedule`

---

### 7. Notifikasi Email Administrator
- **Status Sebelumnya:** Notifikasi pengajuan ulasan hanya berupa log; agenda harian belum ada.
- **Implementasi Baru:**
  - **Notifikasi Testimoni Baru:** Mailable `App\Mail\TestimonialPendingAdminMail` otomatis terkirim ke email admin setiap kali pasien mengirimkan testimoni yang menunggu persetujuan moderasi di panel.
  - **Agenda Harian Jam 07:30 WIB:** Command `appointment:send-daily-summary` (`App\Console\Commands\SendDailyAgendaSummaryCommand`) berjalan setiap hari pukul 07:30 WIB. Mailable `App\Mail\DailyAgendaAdminMail` hanya dikirimkan jika terdapat setidaknya 1 janji temu yang dijadwalkan pada hari tersebut.
  - **Commit Git:** `8f0f85d` - `feat(notifications): admin mail for pending testimonials and daily agenda summary at 07:30`

---

### 8. Tombol WhatsApp Mengambang (Floating Action Button)
- **Status Sebelumnya:** Tombol WhatsApp hanya ada pada bagian footer dan header.
- **Implementasi Baru:**
  - Ditambahkan floating button WhatsApp di file layout utama (`resources/views/layouts/app.blade.php`).
  - Mengambil nomor WhatsApp dinamis dari `SiteSetting` (fallback nomor kontak cabang).
  - Tampilan responsif: ikon melayang dengan indikator denyut (*pulse*) di perangkat bergerak (360px) tanpa menutupi tombol penting, serta badge teks "Konsultasi WhatsApp" pada layar desktop (1280px).
  - Tombol otomatis tidak muncul pada panel admin (`/kelola-sam`).
  - **Commit Git:** `e763410` - `feat(ui): floating responsive whatsapp consultation button across all public pages`

---

### 9. Halaman Error Kustom (404, 419, 429, 500)
- **Status Sebelumnya:** Menggunakan tampilan template kesalahan bawaan framework.
- **Implementasi Baru:**
  - Dibuat layout kustom `resources/views/errors/layout.blade.php` serta view:
    - `404.blade.php` (Halaman Tidak Ditemukan)
    - `419.blade.php` (Sesi Kedaluwarsa)
    - `429.blade.php` (Terlalu Banyak Permintaan / Rate Limit Exceeded)
    - `500.blade.php` (Kendala Server Internal)
  - Desain mengikuti tema profesional SAM: warna Navy `#1E2A4A`, Emerald Green `#15803D`, tipografi Inter & Playfair Display, kartu berbayang rapi, tombol navigasi "Kembali ke Beranda", dan tautan bantuan WhatsApp yang langsung terhubung ke nomor customer care.
  - **Commit Git:** `5cb9027` - `feat(ui): custom branded error pages for 404, 419, 429, and 500`

---

### 10. Pengujian Fitur & Verifikasi Menyeluruh
- **Status Sebelumnya:** 94 pengujian fitur/unit.
- **Implementasi Baru:**
  - Ditambahkan test komprehensif di `tests/Feature/HardeningVerificationTest.php`, `tests/Feature/ErrorPagesTest.php`, `tests/Feature/AdminAppointmentActionsTest.php`, `tests/Feature/ProductionSecurityTest.php`, `tests/Feature/PurgeOldDataCommandTest.php`, `tests/Feature/BackupConfigurationTest.php`, `tests/Feature/AdminNotificationsTest.php`, `tests/Feature/FloatingWhatsAppButtonTest.php`, dan `tests/Feature/AntiSpamAndRateLimitTest.php`.
  - Memverifikasi secara ketat:
    - Perlindungan *race condition* slot ganda (`test_race_condition_slot_protection`).
    - Penolakan route kelola pasien `/janji/{code}` dengan kode HTTP 404.
    - Halaman konfirmasi tidak memiliki tombol batal/ubah untuk pasien.
    - Aksi admin Ubah Jadwal dan Batalkan memindahkan dan membebaskan kapasitas slot dengan akurat.
    - Akses download resep tanpa login admin ditolak (*redirect* ke login `/kelola-sam/login`).
  - **Hasil Akhir Pengujian:** Seluruh 125 pengujian (22 file tes) lulus 100% tanpa ada *failure* atau *error*.
  - **Commit Git:** `a6d8552` - `test(hardening): comprehensive feature verification tests for all security and hardening items`

---

## Yang Perlu Dikonfigurasi Administrator di File `.env` & Server Hosting

Agar seluruh fitur di atas berfungsi optimal di lingkungan *production*, mohon lengkapi variabel berikut:

### 1. Kunci Cloudflare Turnstile (Anti-Spam)
Daftarkan domain aplikasi di [Cloudflare Turnstile](https://dash.cloudflare.com/), kemudian masukkan kunci ke `.env`:
```dotenv
TURNSTILE_SITE_KEY=0x4AAAAAA...
TURNSTILE_SECRET_KEY=0x4AAAAAA...
```
*(Catatan: Jika dikosongkan, form janji temu tetap berjalan aman dengan honeypot dan rate limiter tanpa memblokir pengguna).*

### 2. Kredensial Email (SMTP) & Notifikasi Admin
Untuk pengiriman email konfirmasi, perubahan jadwal, pembatalan, review testimoni, ringkasan agenda harian (07:30), serta alarm backup:
```dotenv
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailgun.org
MAIL_PORT=587
MAIL_USERNAME=postmaster@your-domain.com
MAIL_PASSWORD=your-smtp-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="info@sam-orthotic.com"
MAIL_FROM_NAME="SAM Orthotic Prosthetic"

# Email tujuan penerima notifikasi admin:
ADMIN_NOTIFICATION_EMAIL="admin@sam-orthotic.com"
```

### 3. WhatsApp Gateway (Opsional untuk WhatsApp Otomatis)
Jika menggunakan gateway pihak ketiga (misalnya Fonnte):
```dotenv
WHATSAPP_SENDER=fonnte
FONNTE_TOKEN=your-fonnte-api-token
```
*(Jika diatur ke `null`, admin tetap dapat mengirim pesan WhatsApp langsung ke nomor pasien melalui link satu-klik `wa.me` yang telah terformat rapi di panel admin).*

### 4. Cron Job Server Hosting (Laravel Scheduler)
Jalankan satu entri cron berikut pada server hosting/VPS (misalnya cPanel Cron Jobs atau crontab Ubuntu):
```bash
* * * * * cd /path/to/sam && php artisan schedule:run >> /dev/null 2>&1
```
Entri ini otomatis mengelola:
- Agenda harian janji temu setiap pukul 07:30 WIB (`appointment:send-daily-summary`).
- Pembersihan backup lama setiap pukul 01:00 WIB (`backup:clean`).
- Pembuatan backup harian database & file resep setiap pukul 01:30 WIB (`backup:run`).
- Retensi & anonimisasi data pasien setiap hari Minggu pukul 02:00 WIB (`sam:purge-old-data`).

### 5. Queue Worker (Supervisor di Server)
Karena pengiriman email menggunakan antrean database (`QUEUE_CONNECTION=database`), pastikan supervisor atau process manager menjalankan queue worker:
```bash
php artisan queue:work --sleep=3 --tries=3 --timeout=90
```

---

## 11. Implementasi & Audit SEO Lengkap (Cabang Git `seo-lengkap`)

Bagian ini merangkum seluruh pekerjaan implementasi SEO on-page, SEO teknis, data terstruktur (JSON-LD), SEO lokal, panel admin, automated testing, dan dev tools yang diselesaikan pada cabang git `seo-lengkap`.

### A. Migrasi Database Baru
1. `database/migrations/2026_10_13_000001_add_seo_fields_to_products_and_categories_table.php`:
   - Menambahkan kolom `meta_title` (nullable string 100) dan `meta_description` (nullable string 255) pada tabel `products`.
   - Menambahkan kolom `meta_title`, `meta_description`, dan `intro_text` (nullable text) pada tabel `product_categories`.
2. `database/migrations/2026_10_13_000002_create_redirects_table.php`:
   - Membuat tabel `redirects` (`id`, `source_path` unique index, `target_path`, `status_code` default 301, timestamps) untuk melacak perubahan slug lama produk & kategori agar tidak terjadi link mati (dead links).

### B. Service & Komponen Terpusat Baru
1. `app/Support/Seo.php`:
   - Service terpusat untuk membangun metadata per halaman (title unik <= 65 karakter, meta description 120-160 karakter unik, canonical absolut, Open Graph, Twitter card summary_large_image, theme-color navy).
   - Mendukung fallback otomatis dan override dari `site_settings` serta kolom model produk/kategori.
2. `app/Support/StructuredData.php`:
   - Generator JSON-LD Schema.org terpusat: `WebSite` dengan SearchAction, `Organization`, `LocalBusiness` per cabang aktif (dengan konversi standar hari buka `Monday`–`Sunday`), `BreadcrumbList`, `FAQPage` (hanya dari FAQ aktif), dan `Product` (tanpa data fiktif offers/price).
3. `app/Http/Middleware/SeoRedirectMiddleware.php`:
   - Normalisasi URL: 301 redirect untuk huruf besar ke huruf kecil, penghapusan trailing slash, dan resolusi slug lama dari tabel `redirects`.
4. `app/Http/Middleware/SeoRobotsHeaderMiddleware.php`:
   - Menyematkan header `X-Robots-Tag: noindex, nofollow` pada halaman admin (`/kelola-sam`), konfirmasi janji, pencarian internal, respons API, dan error pages.
   - Menyematkan `X-Robots-Tag: noindex` pada seluruh halaman publik bila lingkungan bukan `production`.
5. `app/Http/Controllers/SitemapController.php`:
   - Menyajikan `sitemap.xml` dinamis dengan `<loc>` absolut dan `<lastmod>` dari `updated_at` untuk seluruh halaman statis, cabang aktif, kategori aktif, dan produk aktif.
   - Menyajikan `robots.txt` dinamis (Allow di production, Disallow di lingkungan uji).
6. `app/Http/Controllers/BranchController.php`:
   - Halaman khusus cabang `/cabang/{slug}` (`resources/views/pages/branch/show.blade.php`) untuk penguatan SEO lokal.
7. `resources/views/components/seo.blade.php`:
   - Komponen Blade terpadu yang merender seluruh tag SEO, Open Graph, Twitter Card, favicon lengkap (ico, 32px, 192px, 512px, apple-touch-icon), dan JSON-LD.
8. `public/site.webmanifest`:
   - Web App Manifest standar untuk PWA dan SEO mobile.

### C. Pembaruan Antarmuka Admin Filament
1. `ProductResource`:
   - Tab "SEO & Metadata" dengan penghitung karakter dan Google SERP Preview interaktif.
   - Kolom tabel dan filter "Konten Kurang" (<40 karakter deskripsi).
2. `ProductCategoryResource`:
   - Tab "SEO & Metadata" dengan SERP preview dan field `intro_text` (minimal ~100 kata).
3. `SiteSettings`:
   - Tab "SEO & Metadata Publik" untuk `seo.home_title`, `seo.home_description`, `seo.about_title`, `seo.about_description`, `seo.catalog_title`, `seo.catalog_description`, serta upload `seo.og_default_image` (1200x630).

### D. Perbaikan On-Page & Heading Hierarchy
- Footer di `resources/views/layouts/app.blade.php`: Judul kolom dinormalkan dari `<h4>` ke paragraf berbobot teks tebal, menghilangkan heading skip di seluruh website.
- Detail produk di `resources/views/pages/catalog/show.blade.php`: Judul bagian dinormalkan menjadi `<h2>` dan kartu produk terkait menjadi `<h3>`.
- Hero Beranda di `resources/views/pages/home/_hero.blade.php`: Kartu fitur dinormalkan tanpa tag heading sembarangan; gambar hero menggunakan `fetchpriority="high"`, WebP, dan dimensi eksplisit tanpa `lazy`.

### E. Dev Audit Tooling & Testing
- CLI Crawler: `tools/seo-audit` (merayapi 68 URL, 0 isu, 0 broken link). Laporan tersimpan di `storage/app/qa/seo-audit.csv`.
- Headless Lighthouse: `tools/lighthouse-audit.mjs` (skrip audit mobile & desktop Chromium headless).
- Automated Feature Test: `tests/Feature/SeoAuditTest.php` (8 test case komprehensif, 272 assertions). Seluruh test suite (133 tests, 669 assertions) lulus 100%.

### F. Asumsi yang Diambil
1. **Keputusan Produk (Mengikat):** Tetap satu admin tanpa role di `/kelola-sam`; `/janji/{code}` tidak ada (404); `/janji/konfirmasi/{code}` hanya layar sukses transaksional bertag `noindex, nofollow`; pembatalan/ubah jadwal hanya dapat dilakukan admin dari panel.
2. **Ketiadaan Klaim Berlebihan:** Seluruh default title dan meta description menggunakan bahasa faktual Indonesia (contoh: "sejak 2014", "konsultasi gratis"), tanpa kata-kata superlatif yang tidak dapat dibuktikan ("terbaik", "nomor 1", "100% garansi").
3. **Data Harga Produk:** Schema `Product` tidak menyertakan elemen `offers` atau harga fiktif karena website SAM tidak menampilkan harga publik langsung (harga didasarkan pada hasil asesmen klinis dan spesifikasi khusus pasien).
4. **Proteksi Lingkungan Uji:** Di local/staging, `robots.txt` sengaja memblokir crawl (`Disallow: /`) dan menyertakan `X-Robots-Tag: noindex` agar mesin pencari tidak mengindeks situs lokal secara tidak sengaja. Di production, konfigurasi ini otomatis terbuka.

### G. Hal-Hal yang Perlu Diisi oleh Pemilik/Administrator Situs
1. **Isi Deskripsi Kategori (Intro Text):** Buka panel `/kelola-sam` > Kategori Produk, isi field "Teks Pengantar Kategori (Intro Text)" dengan penjelasan minimal 100 kata mengenai masing-masing kelompok alat (Orthosis, Prosthesis, dsb.).
2. **Isi Meta Kustom (Bila Diperlukan):** Buka produk-produk unggulan dan isi meta title/description kustom pada tab SEO jika ingin kata kunci khusus di luar pola default otomatis.
3. **Upload Default Social Share Image (`seo.og_default_image`):** Buka Pengaturan Situs > Tab SEO, upload foto branding SAM beresolusi 1200x630 piksel untuk tampilan pratinjau tautan media sosial.
4. **Koordinat Geolokasi Cabang:** Lengkapi kolom latitude & longitude pada data cabang di database/panel jika ingin pin peta geo-koordinat muncul lengkap di schema `LocalBusiness`.
5. **Konfigurasi Domain Produksi:** Saat deploy ke hosting, ubah `APP_URL=https://samorthotic.com` di `.env` produksi, pasang SSL HTTPS, dan kirim `sitemap.xml` ke Google Search Console. Detail lengkap tersedia di `docs/LAPORAN_SEO.md`.

