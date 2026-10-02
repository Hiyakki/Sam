# Prompt Antigravity untuk Membangun Website SAM

Dokumen ini berisi **langkah persiapan** dan **prompt siap tempel** untuk membangun website SAM di Google Antigravity, berdasarkan file `PANDUAN_PENGEMBANGAN_WEBSITE_SAM.md`.

---

## A. Persiapan (sekali saja)

### 1. Siapkan folder proyek
Buat folder kosong, misalnya `sam-website`, lalu buka lewat **File > Open Folder** di Antigravity. Pastikan yang dibuka adalah folder proyek itu sendiri, bukan folder induknya.

### 2. Taruh panduan di dalam proyek
```
sam-website/
├── docs/
│   └── PANDUAN_PENGEMBANGAN_WEBSITE_SAM.md   <- file panduan dari kita
└── .agent/            (atau .agents/ pada versi Antigravity yang lebih baru)
    └── rules/
        └── sam-project.md                      <- isi dari bagian B di bawah
```
> Antigravity membaca rules dari folder `.agent/rules/` (beberapa versi baru memakai `.agents/rules/`). Kalau rules tidak terbaca, cek lewat tab **Rules** di pengaturan dan pastikan Activation Mode-nya **Always On**.

### 3. Pengaturan agent yang disarankan
- Mode: **Planning** (proyek ini multi-file dan kompleks). Pakai **Fast** hanya untuk perbaikan kecil.
- Autonomy: pilih yang meminta persetujuan di titik penting (misalnya *Agent Decides*), jangan *Always Proceed* untuk perintah terminal.
- Aktifkan **deny list** untuk perintah berbahaya (`rm -rf`, `drop database`, dll.).
- Pastikan di komputer sudah terpasang: **PHP 8.2+, Composer, Node.js, MySQL 8**, dan Git.

### 4. Cara kerja yang disarankan
Kerjakan **satu fase per sesi/percakapan** (Fase 1, lalu Fase 2, dst.). Sebelum agent menulis kode, baca dulu *Implementation Plan* yang dibuatnya, koreksi bila perlu, baru setujui. Setelah tiap fase, minta agent menjalankan checklist verifikasi, lalu **commit ke Git**.

---

## B. Rules Proyek (Always On)

Simpan sebagai `.agent/rules/sam-project.md`:

```markdown
# SAM Website - Rules

## Sumber kebenaran
- Semua kebutuhan, skema database, alur, dan checklist ada di `docs/PANDUAN_PENGEMBANGAN_WEBSITE_SAM.md`.
- Baca file itu sebelum mengerjakan tugas apa pun. Jika ada konflik antara permintaan chat dan panduan, tanyakan dulu, jangan menebak.

## Stack (wajib, jangan diganti)
- PHP 8.2+, Laravel (versi stabil terbaru), Filament (versi kompatibel), MySQL 8 (utf8mb4).
- Frontend publik: Blade + Tailwind CSS + Alpine.js (Livewire boleh untuk form appointment). Build dengan Vite.
- Testing: Pest atau PHPUnit. Antrian: queue driver `database`.

## Aturan kerja
- Kerjakan HANYA fase yang diminta. Jangan mengerjakan fase berikutnya tanpa diminta.
- Buat Implementation Plan dulu, tunggu persetujuan, baru tulis kode.
- Semua perubahan database lewat migration. Jangan membuat tabel manual.
- Validasi di server dengan Form Request. Gunakan foreign key, index, dan soft delete sesuai panduan.
- Teks antarmuka Bahasa Indonesia. Nama kode/tabel/kolom Bahasa Inggris.
- Timezone `Asia/Jakarta`, locale `id`.
- Jangan hardcode isi Home/About, kontak, jam buka, FAQ, atau testimoni. Semuanya dikelola dari admin.
- Jangan menyimpan password, API key, atau data pasien di kode/log. Gunakan `.env`.
- File resep dokter disimpan di disk privat dan hanya bisa diunduh admin yang login.
- Satu akun admin saja, tanpa role, tanpa registrasi. Path panel bukan `/admin` (pakai `/kelola-sam`).
- Jangan menjalankan perintah destruktif (hapus database/folder, `migrate:fresh` di produksi) tanpa konfirmasi.

## Definisi selesai untuk setiap fase
- Kode berjalan, tes relevan lulus, checklist fase di panduan sudah dijalankan dan dilaporkan.
- Ringkas apa yang dibuat, file penting yang berubah, dan hal yang perlu keputusan saya.
```

---

## C. Prompt Pembuka (tempel pertama kali)

```text
Kamu adalah senior full-stack engineer (Laravel + Filament). Kita akan membangun website
company profile + appointment online untuk "SAM Orthotic Prosthetic" (usaha pembuatan alat
orthotic dan prosthetic).

SUMBER KEBENARAN: file `docs/PANDUAN_PENGEMBANGAN_WEBSITE_SAM.md`. Baca SELURUH file itu dulu
sebelum melakukan apa pun, termasuk lampiran A (data katalog) dan lampiran B.

Tugas pertamamu BUKAN menulis kode. Lakukan ini:
1. Baca panduan dan rules di `.agent/rules/`.
2. Rangkum pemahamanmu dalam maksimal 15 poin: tujuan proyek, halaman publik, fitur admin,
   alur form appointment (kantor -> layanan -> tanggal -> jam), fitur FAQ dan testimoni,
   dan hal yang SENGAJA tidak dibuat.
3. Tuliskan daftar asumsi, hal yang ambigu, atau konflik yang kamu temukan di panduan,
   beserta usulanmu.
4. Periksa lingkungan lokal: versi PHP, Composer, Node, MySQL, Git. Laporkan hasilnya,
   jangan install apa pun tanpa izin.
5. Buat Implementation Plan tingkat tinggi untuk 10 fase di panduan, dengan estimasi
   urutan dan risikonya.

Berhenti setelah itu dan tunggu persetujuan saya. Jangan membuat file proyek dulu.
```

---

## D. Prompt per Fase

> Tempel satu prompt per sesi. Setelah agent selesai, minta ia menampilkan hasil checklist, lalu commit.

### Fase 1 - Inisialisasi Proyek
```text
Kerjakan FASE 1 (Inisialisasi Proyek) di `docs/PANDUAN_PENGEMBANGAN_WEBSITE_SAM.md`.

- Buat proyek Laravel di folder ini (jika folder berisi docs/ dan .agent/, buat proyek di
  folder sementara lalu pindahkan isinya agar docs/ dan .agent/ tetap ada).
- Konfigurasikan .env (MySQL, Asia/Jakarta, locale id), pasang Filament, Tailwind, Alpine/Livewire, Vite.
- Buat layout Blade dasar (navbar: Home, About Us, Katalog, FAQ, Appointment + tombol "Buat Janji"; footer),
  route dan controller kosong untuk semua halaman publik, serta struktur folder sesuai panduan.
- Terapkan identitas visual dari bagian 3 panduan (hijau sage, abu muda, aksen merah logo, mobile-first).

Buat Implementation Plan dulu dan tunggu persetujuan saya. Setelah selesai, jalankan checklist
Fase 1, lalu laporkan hasilnya. Jangan lanjut ke Fase 2.
```

### Fase 2 - Database (MySQL)
```text
Kerjakan FASE 2 (Database) di panduan.

Buat migration, model, relasi Eloquent, factory, dan seeder untuk SEMUA tabel di bagian 2.1:
users (bawaan), branches, services, branch_service, branch_schedules, branch_closures,
appointments, product_categories, products, product_images, site_settings, faqs, testimonials.
Ikuti tipe kolom, foreign key, index, enum, dan soft delete persis seperti di panduan.

Seeder: ServiceSeeder (Konsultasi, Maintenance Alat (Perbaikan), Fitting), BranchSeeder (1 cabang
placeholder, aktif, terhubung ke 3 layanan), BranchScheduleSeeder (Senin-Sabtu 08:00-17:00, slot
60 menit, kapasitas 1, Minggu tutup), ProductCategorySeeder + ProductSeeder dari Lampiran A
(pakai tanda [LENGKAPI] untuk data yang kosong), SiteSettingSeeder, FaqSeeder, TestimonialSeeder
(nonaktif, jangan buat testimoni palsu aktif).
Buat artisan command `sam:create-admin` (interaktif, tanpa password di kode).

Buat plan dulu, tunggu persetujuan. Setelah selesai, jalankan `php artisan migrate:fresh --seed`
di database LOKAL, uji relasi di tinker, dan laporkan hasil checklist Fase 2.
```

### Fase 3 - Admin Panel (Filament)
```text
Kerjakan FASE 3 (Admin Panel Filament) di panduan.

Panel di path `/kelola-sam`, registrasi mati, lupa password aktif, 2FA bila didukung versinya.
Buat Resource: BranchResource (relasi layanan + repeater jadwal mingguan), ServiceResource,
BranchClosureResource ("Hari Libur"), AppointmentResource (filter, pencarian, aksi ubah status
cepat, tombol chat WA, unduh resep lewat route aman), ProductCategoryResource, ProductResource
(upload foto, urutan drag-and-drop, toggle aktif), FaqResource, TestimonialResource (checkbox izin
tampil; testimoni tidak bisa diaktifkan tanpa izin), halaman Pengaturan Situs, dan widget dashboard.
Hapus memakai soft delete dengan konfirmasi.

Buat plan dulu, tunggu persetujuan. Setelah selesai, verifikasi lewat browser: login admin,
tambah cabang lengkap, tambah hari libur, tambah/hapus FAQ dan testimoni, ubah teks Home.
Pastikan `/admin` mengembalikan 404. Lampirkan screenshot/walkthrough hasilnya.
```

### Fase 4 - Halaman Publik
```text
Kerjakan FASE 4 (Home, About Us, Katalog, FAQ, Testimoni) di panduan.

Semua konten dinamis dari database/site_settings. Ikuti detail bagian 4.1 sampai 4.5: hero,
kategori, keunggulan, testimoni, cuplikan FAQ, daftar cabang + peta + jam buka, katalog dengan
pencarian dan filter kategori 2 level, halaman detail produk dengan tombol WhatsApp berpesan
otomatis, halaman /faq (accordion + pencarian), SEO dasar, sitemap.xml, robots.txt.
Bagian kosong (tanpa testimoni/FAQ aktif) disembunyikan otomatis. Harga produk tidak ditampilkan.
Gunakan frontend design yang bersih, rounded, mobile-first sesuai identitas visual.

Buat plan dulu, tunggu persetujuan. Setelah selesai, buka di browser dan cek lebar 360px, 768px,
1280px. Uji bahwa perubahan FAQ/testimoni dari admin langsung tampil. Laporkan checklist Fase 4.
```

### Fase 5 - Logika Slot (backend)
```text
Kerjakan FASE 5 (SlotService) di panduan, TANPA membuat UI form dulu.

Buat App\Services\SlotService: availableServices, isDateBookable, slotsFor, book (DB transaction +
lockForUpdate, kode janji acak unik). Tulis semua tes wajib: Minggu tidak bisa dipesan, libur
(per cabang dan semua cabang), slot penuh, race condition 2 request ke slot kapasitas 1,
slot lewat hari ini, layanan yang tidak tersedia di cabang.

Buat plan dulu, tunggu persetujuan. Jalankan seluruh tes dan tampilkan hasilnya (harus lulus semua).
```

### Fase 6 - Form Appointment
```text
Kerjakan FASE 6 (Form Appointment) di panduan.

Urutan field dan perilaku dinamis persis seperti di panduan: Nama, Email, Usia, Keluhan, No Telepon,
Pilih Kantor -> Pilih Layanan (hanya layanan cabang itu) -> Tanggal (hari tutup/libur nonaktif) ->
Pilih Waktu (muncul setelah tanggal, slot penuh abu-abu) -> Upload Resep (opsional, privat) ->
persetujuan privasi -> Submit. Mengganti kantor/tanggal mereset pilihan di bawahnya.

Sertakan: endpoint JSON (slots, services, closed-dates) dengan rate limit, validasi server,
honeypot + captcha (Turnstile/hCaptcha), rate limit pengajuan per IP dan per nomor telepon,
halaman konfirmasi, serta halaman /janji/{code} untuk melihat, mengubah jadwal, dan membatalkan janji.

Buat plan dulu, tunggu persetujuan. Setelah selesai, uji alur lengkap di browser di tampilan HP
dan desktop, termasuk percobaan double booking dan input tidak valid. Laporkan checklist Fase 6.
```

### Fase 7 - Notifikasi dan Pengingat
```text
Kerjakan FASE 7 (Notifikasi & Pengingat) di panduan.

Email ke pasien (janji dibuat, status berubah, jadwal diubah) dan ke admin (janji baru, nama cabang
di subjek). Pengingat H-1 lewat Laravel Scheduler. Semua email lewat queue.
Gunakan mail driver `log` untuk pengujian lokal. Tulis tes untuk setiap notifikasi dan pengingat.
Jelaskan perintah cron dan queue worker yang perlu dipasang di hosting.
```

### Fase 8 - WhatsApp (modular)
```text
Kerjakan FASE 8 (WhatsApp) di panduan.

Pastikan semua tombol wa.me berfungsi dan template pesan admin bisa diedit. Buat interface
WhatsAppSender dengan NullSender sebagai default dan satu implementasi gateway opsional yang
dimatikan lewat .env (WA_GATEWAY_ENABLED=false). Kegagalan gateway tidak boleh menggagalkan
pembuatan janji. Jangan memasukkan API key apa pun ke kode.
```

### Fase 9 - Keamanan, Privasi, SEO
```text
Kerjakan FASE 9 di panduan.

Terapkan: redirect HTTPS dan cookie secure di produksi, header keamanan, rate limit login,
halaman Kebijakan Privasi yang bisa diedit admin, command retensi data pasien terjadwal
(default 2 tahun, dapat dikonfigurasi), aturan izin testimoni, structured data (LocalBusiness,
Product, FAQPage), alt gambar, lazy loading.
Lakukan audit singkat: pastikan resep dokter tidak bisa diakses lewat URL langsung, tidak ada
data pasien di log, dan tidak ada rahasia di repo. Laporkan temuan dan perbaikannya.
```

### Fase 10 - Backup, Testing, Deployment
```text
Kerjakan FASE 10 di panduan.

Pasang dan konfigurasi backup otomatis (database + storage) dengan notifikasi gagal.
Jalankan seluruh test suite dan perbaiki yang gagal. Jalankan audit Lighthouse (mobile) dan
perbaiki masalah besar. Buat dokumen `docs/DEPLOYMENT.md` berisi langkah deploy ke shared
hosting/VPS (build asset lokal, .env produksi, migrate --force, storage:link, cache, cron, queue,
pembuatan admin, daftar data yang harus diisi lewat admin).
JANGAN melakukan deploy sungguhan ke server; hanya siapkan dokumen dan skrip.
```

---

## E. Prompt Pendukung

### Mengulang/memperbaiki satu bagian
```text
Pada Fase [N], bagian [nama bagian] belum sesuai panduan: [jelaskan masalahnya].
Baca kembali bagian terkait di docs/PANDUAN_PENGEMBANGAN_WEBSITE_SAM.md, perbaiki hanya bagian itu,
jalankan tes yang relevan, dan jelaskan apa yang diubah. Jangan menyentuh fase lain.
```

### Debug
```text
Terjadi error berikut: [tempel pesan error/log].
Langkah: 1) jelaskan penyebab yang paling mungkin, 2) tunjukkan file dan baris terkait,
3) usulkan perbaikan minimal, 4) tunggu persetujuan sebelum mengubah kode.
```

### Review akhir sebelum go-live
```text
Lakukan review menyeluruh terhadap proyek berdasarkan "Definition of Done" di panduan.
Untuk setiap item, beri status (Lulus / Belum / Sebagian) disertai bukti (tes, screenshot, atau
file terkait). Tambahkan daftar risiko keamanan atau privasi yang masih tersisa dan hal yang
perlu saya isi dari Lampiran B.
```

---

## F. Tips Singkat

- **Satu fase satu sesi**, supaya konteks agent tetap fokus dan hemat kuota.
- **Selalu baca Implementation Plan** sebelum menyetujui, terutama di Fase 2 (skema database), karena perubahan di belakang lebih mahal.
- **Commit setiap fase** (`git commit -m "feat: fase 3 admin panel"`), jadi mudah kembali bila ada yang rusak.
- **Jangan pernah** menempel data pasien asli, password, atau API key ke chat agent.
- Gunakan database **lokal**, bukan database produksi, selama pengembangan.
- Bila agent mulai menyimpang dari panduan, ingatkan: *"Rujuk kembali ke docs/PANDUAN_PENGEMBANGAN_WEBSITE_SAM.md bagian [X]."*
