# Panduan Pengembangan Website SAM Orthotic Prosthetic

> Dokumen ini adalah **prompt dan panduan pengembangan** yang berurutan. Bisa dipakai langsung sebagai instruksi untuk AI coding assistant (Claude Code, dll.) atau sebagai acuan developer. Kerjakan **per fase, berurutan**. Jangan lompat fase, dan jangan lanjut sebelum checklist fase sebelumnya terpenuhi.

---

## 0. Cara memakai dokumen ini

Salin bagian **"1. Konteks Proyek"** sampai **"4. Aturan Umum"** sebagai konteks awal, lalu berikan fase satu per satu (Fase 1, Fase 2, dst.) dengan kalimat seperti:

> "Kerjakan Fase 3 sesuai panduan. Setelah selesai, jalankan checklist verifikasinya dan laporkan hasilnya."

---

## 1. Konteks Proyek

**SAM Orthotic Prosthetic** (sejak 2014, Instagram `@sam_kakipalsu`) adalah usaha pembuatan alat orthotic (penyangga/brace) dan prosthetic (anggota gerak tiruan). Dibutuhkan website company profile dengan fitur katalog dan appointment online.

### Halaman publik (tanpa login)
1. **Home**
2. **About Us**
3. **Katalog**
4. **Appointment**
5. **FAQ** (pertanyaan umum)
6. Kebijakan Privasi (halaman singkat)

Selain itu, bagian **Testimoni** tampil di Home. Isi **FAQ** dan **Testimoni** sepenuhnya dikelola admin (tambah, ubah, hapus, aktif/nonaktif, atur urutan).

### Admin panel
- **Hanya satu akun admin** (tanpa role, tanpa pembagian hak per cabang). Admin mengelola semuanya.
- Tidak ada registrasi publik. Akun admin dibuat lewat perintah di server.
- Pengunjung/pasien **tidak pernah login**.

### Ruang lingkup yang SENGAJA TIDAK dibuat (versi 1)
- Status pembuatan alat (tracking)
- Info kerja sama (RS/dokter/BPJS)
- Akun pasien / login pasien
- Admin per cabang / multi-role
- Galeri foto, artikel edukasi, tipe janji lanjutan, laporan, dua bahasa (ditunda ke tahap kedua)

---

## 2. Tech Stack (wajib)

| Komponen | Pilihan |
|---|---|
| Bahasa | PHP 8.2+ |
| Framework | **Laravel** (versi stabil terbaru) |
| Admin panel | **Filament** (versi stabil terbaru yang kompatibel dengan Laravel terpilih) |
| Database | **MySQL 8** (engine InnoDB, charset `utf8mb4`, collation `utf8mb4_unicode_ci`) |
| Frontend publik | Blade + **Tailwind CSS** + **Alpine.js** (form appointment dinamis boleh memakai Livewire) |
| Build asset | Vite |
| Antrian & jadwal | Laravel Queue (driver `database`) + Laravel Scheduler (cron) |
| Email | Laravel Mail (SMTP) |
| WhatsApp | Tombol `wa.me` untuk interaksi pengunjung. Notifikasi otomatis ke admin/pasien lewat WA gateway bersifat **opsional dan dibuat sebagai service terpisah yang bisa dimatikan** (lihat Fase 8) |
| Upload file | Laravel Storage (disk `local`/private untuk resep dokter, disk `public` untuk gambar katalog) |
| Testing | Pest atau PHPUnit |
| Hosting target | Shared hosting/VPS PHP + MySQL. Pastikan kompatibel (tanpa bergantung pada Node di server produksi: asset di-build lokal lalu di-upload) |

**Paket tambahan yang diperbolehkan:** `spatie/laravel-medialibrary` (opsional untuk foto produk), `spatie/laravel-backup` (backup), paket captcha ringan (Cloudflare Turnstile / hCaptcha) atau honeypot, `filament/spatie-laravel-settings-plugin` (opsional untuk pengaturan situs).

---

## 3. Identitas Visual

- Warna dasar mengikuti katalog dan logo: **hijau sage** (sekitar `#A5BF98`), **abu muda** (`#E3E3E1`), **hijau tua keabuan** (`#869480`), dengan **aksen merah** dari logo SAM (sekitar `#E8343B`).
- Gaya: bersih, rounded (sudut membulat seperti kartu di katalog), kontras teks cukup (aksesibilitas).
- Judul memakai font serif (mirip tampilan katalog), isi memakai sans-serif yang mudah dibaca.
- **Mobile-first**: mayoritas pengunjung memakai HP.
- Logo: gunakan file logo SAM asli (minta dari pemilik). Sementara pakai placeholder teks.

---

## 4. Aturan Umum (berlaku di semua fase)

1. Seluruh teks antarmuka berbahasa **Indonesia**. Kode, nama tabel, dan nama kolom memakai bahasa **Inggris**.
2. Gunakan **migration, model, factory, seeder** Laravel. Jangan membuat tabel manual di luar migration.
3. Semua input divalidasi di server (Form Request). Jangan hanya mengandalkan validasi JS.
4. Gunakan **foreign key** dan **index** yang sesuai. Gunakan **soft delete** untuk cabang, layanan, dan produk.
5. Zona waktu aplikasi: `Asia/Jakarta`. Locale: `id`.
6. Data sensitif (nama, telepon, email, keluhan, resep dokter) diperlakukan sebagai **data pribadi**: HTTPS wajib, file resep **tidak** boleh bisa diakses publik lewat URL langsung.
7. Jangan hardcode isi Home/About, nomor WA, atau jam buka. Semuanya harus bisa diubah dari admin.
8. Setiap fase diakhiri dengan **checklist verifikasi**. Jalankan dan laporkan hasilnya.
9. Commit kecil dan sering, dengan pesan commit yang jelas (misal `feat(appointment): hitung slot tersedia`).

---

## FASE 1 - Inisialisasi Proyek

**Tujuan:** kerangka proyek siap jalan.

1. Buat proyek Laravel baru bernama `sam-website`.
2. Konfigurasi `.env`: koneksi MySQL, `APP_TIMEZONE=Asia/Jakarta`, `APP_LOCALE=id`, `APP_FALLBACK_LOCALE=id`.
3. Pasang Filament, Tailwind CSS, Alpine.js (atau Livewire), Vite.
4. Buat layout Blade dasar (`layouts/app.blade.php`) berisi: navbar (Home, About Us, Katalog, FAQ, Appointment + tombol "Buat Janji"), footer (alamat, WA, email, Instagram), slot konten.
5. Buat route dan controller kosong untuk halaman publik (Home, About Us, Katalog, FAQ, Appointment) + kebijakan privasi.
6. Siapkan struktur folder:
   ```
   app/
     Filament/Resources/...
     Http/Controllers/
     Http/Requests/
     Models/
     Services/            # logika bisnis (SlotService, dll.)
     Notifications/
   database/
     migrations/
     seeders/
   resources/views/
     layouts/ pages/ components/
   ```

**Checklist:** `php artisan serve` jalan, semua halaman publik menampilkan layout dengan navbar dan footer, koneksi MySQL berhasil (`php artisan migrate` sukses).

---

## FASE 2 - Database (MySQL)

**Tujuan:** semua tabel dibuat lewat migration.

### 2.1 Tabel

#### `users` (bawaan Laravel)
Hanya untuk satu admin. Tidak ada kolom role.

#### `branches` (cabang/kantor)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | bigint unsigned PK | |
| name | varchar(100) | contoh: "SAM Bogor" |
| slug | varchar(120) unique | |
| address | text | |
| phone_wa | varchar(20) | format 62xxxxxxxxxx |
| email | varchar(150) nullable | |
| maps_embed_url | text nullable | |
| maps_link | varchar(255) nullable | |
| photo | varchar(255) nullable | |
| is_active | boolean default true | cabang belum siap = false (tidak muncul di form) |
| sort_order | int default 0 | |
| timestamps + deleted_at | | |

#### `services` (layanan)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | PK | |
| name | varchar(100) | |
| slug | varchar(120) unique | |
| description | text nullable | |
| is_active | boolean default true | |
| sort_order | int | |
| timestamps + deleted_at | | |

#### `branch_service` (pivot: layanan yang tersedia per cabang)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | PK | |
| branch_id | FK -> branches (cascade) | |
| service_id | FK -> services (cascade) | |
| unique(branch_id, service_id) | | |

#### `branch_schedules` (jam buka per hari per cabang)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | PK | |
| branch_id | FK | |
| day_of_week | tinyint | 1=Senin ... 7=Minggu |
| is_open | boolean | |
| open_time | time nullable | |
| close_time | time nullable | |
| slot_duration_minutes | smallint default 60 | |
| capacity_per_slot | smallint default 1 | kuota janji per slot |
| unique(branch_id, day_of_week) | | |

#### `branch_closures` (hari libur / tanggal khusus)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | PK | |
| branch_id | FK nullable | `NULL` = berlaku untuk semua cabang |
| date | date | |
| reason | varchar(150) nullable | contoh: "Idul Fitri" |
| is_closed | boolean default true | |
| open_time / close_time | time nullable | diisi bila "jam khusus" (is_closed = false) |
| index(branch_id, date) | | |

#### `appointments`
| Kolom | Tipe | Catatan |
|---|---|---|
| id | PK | |
| code | varchar(12) unique | kode acak sulit ditebak, untuk link pasien |
| branch_id | FK | |
| service_id | FK | |
| appointment_date | date | |
| start_time | time | |
| end_time | time | |
| patient_name | varchar(120) | |
| patient_email | varchar(150) | |
| patient_phone | varchar(20) | |
| patient_age | tinyint unsigned | |
| complaint | text | |
| prescription_path | varchar(255) nullable | file privat |
| status | enum('pending','confirmed','completed','cancelled','no_show') default 'pending' | |
| cancelled_by | enum('patient','admin') nullable | |
| admin_notes | text nullable | |
| reminder_sent_at | timestamp nullable | |
| timestamps | | |
| index(branch_id, appointment_date, start_time) | | untuk cek slot |

#### `products` (katalog)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | PK | |
| category_id | FK -> product_categories | |
| name | varchar(150) | |
| slug | varchar(170) unique | |
| description | text nullable | |
| function | varchar(255) nullable | "Fungsi" |
| indication | text nullable | "Indikasi" |
| photo | varchar(255) nullable | foto utama |
| is_active | boolean default true | |
| sort_order | int | |
| price | decimal(12,2) nullable | opsional, **tidak ditampilkan** secara default |
| timestamps + deleted_at | | |

#### `product_categories` (bertingkat, 2 level)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | PK | |
| parent_id | FK nullable (self) | |
| name | varchar(100) | |
| slug | varchar(120) unique | |
| sort_order | int | |

#### `product_images` (galeri tambahan produk, opsional)
| Kolom | Tipe |
|---|---|
| id, product_id (FK), path, sort_order | |

#### `site_settings` (isi Home/About yang bisa diedit admin)
Tabel key-value: `key` (unique), `value` (longText), `type` (text/richtext/image), `group` (home/about/contact/general).

Contoh key: `home.hero_title`, `home.hero_subtitle`, `home.cta_text`, `home.advantages` (JSON), `about.story`, `about.vision`, `about.mission`, `about.team` (JSON), `contact.whatsapp`, `contact.email`, `contact.instagram`, `general.site_name`, `general.privacy_policy`.

#### `faqs` (pertanyaan umum, dikelola admin)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | PK | |
| question | varchar(255) | |
| answer | text | mendukung rich text sederhana (tebal, daftar, link) |
| category | varchar(60) nullable | opsional untuk pengelompokan, misal "Umum", "Appointment", "Produk" |
| is_active | boolean default true | nonaktif = tidak tampil di publik |
| show_on_home | boolean default false | tampil di cuplikan FAQ di Home |
| sort_order | int default 0 | urutan tampil, bisa drag-and-drop |
| timestamps + deleted_at | | |

#### `testimonials` (testimoni, dikelola admin)
| Kolom | Tipe | Catatan |
|---|---|---|
| id | PK | |
| name | varchar(100) | boleh inisial/samaran, misal "Ibu R." |
| role_or_info | varchar(150) nullable | misal "Pengguna kaki palsu", "Orang tua pasien skoliosis" |
| product_id | FK nullable -> products | opsional, produk terkait (null on delete) |
| content | text | isi testimoni |
| rating | tinyint unsigned nullable | 1-5, opsional |
| photo | varchar(255) nullable | opsional, **hanya dengan izin tertulis/persetujuan** |
| consent_confirmed | boolean default false | admin menandai bahwa izin tampil sudah didapat |
| is_active | boolean default true | |
| sort_order | int default 0 | |
| timestamps + deleted_at | | |

### 2.2 Relasi Eloquent
- `Branch` belongsToMany `Service`; hasMany `BranchSchedule`, `BranchClosure`, `Appointment`.
- `Service` belongsToMany `Branch`; hasMany `Appointment`.
- `Product` belongsTo `ProductCategory`; hasMany `ProductImage`.
- `ProductCategory` self-referencing (parent/children).
- `Testimonial` belongsTo `Product` (nullable).
- `Faq` dan `Testimonial` memakai scope `active()` (is_active = true, urut `sort_order`).

### 2.3 Seeder
- `AdminSeeder`: **tidak** menyimpan password di kode. Buat artisan command `php artisan sam:create-admin` yang meminta nama, email, password.
- `ServiceSeeder`: **Konsultasi**, **Maintenance Alat (Perbaikan)**, **Fitting**.
- `BranchSeeder`: satu cabang (nama/alamat placeholder, bisa diubah di admin), aktif, terhubung ke ketiga layanan.
- `BranchScheduleSeeder`: Senin-Sabtu 08:00-17:00, slot 60 menit, kapasitas 1; Minggu tutup.
- `ProductCategorySeeder` dan `ProductSeeder`: lihat **Lampiran A**.
- `SiteSettingSeeder`: teks awal Home/About (placeholder) dan kontak.
- `FaqSeeder`: 5-8 FAQ contoh (misal: cara membuat janji, berapa kali kunjungan untuk alat custom, apakah perlu resep dokter, cara merawat alat, cara mengubah/membatalkan janji, cabang dan jam buka). Tandai sebagai contoh agar diganti admin.
- `TestimonialSeeder`: **jangan** isi testimoni palsu. Cukup buat 2-3 data contoh dengan `is_active = false` agar admin melihat bentuknya, lalu menggantinya dengan testimoni asli.

**Checklist:** `php artisan migrate:fresh --seed` sukses tanpa error, semua FK dan index terbentuk, relasi Eloquent dapat dipanggil di `tinker`.

---

## FASE 3 - Admin Panel (Filament)

**Tujuan:** admin dapat mengelola semua data.

1. Konfigurasi panel Filament dengan path **bukan `/admin`** (misal `/kelola-sam`).
2. Akses panel hanya untuk user yang ada (satu akun). **Matikan registrasi**. Aktifkan "lupa password" via email.
3. Aktifkan **2FA** bila tersedia di versi Filament yang dipakai.
4. Buat Resource:
   - **BranchResource**: CRUD cabang. Di form: relasi layanan (multi-select / checkbox) dan repeater jadwal mingguan (`branch_schedules`: hari, buka/tutup, jam buka-tutup, durasi slot, kuota).
   - **ServiceResource**: CRUD layanan.
   - **BranchClosureResource** (menu "Hari Libur"): tambah tanggal libur untuk satu cabang atau semua cabang; opsi "jam khusus".
   - **AppointmentResource**: tabel dengan filter (cabang, layanan, status, tanggal), pencarian (nama, telepon, kode), aksi ubah status cepat (Konfirmasi / Selesai / Batal / Tidak Hadir), tombol "Chat WA pasien", unduh resep (lewat route aman). Tampilan ringkas "hari ini" dan "mendatang".
   - **ProductCategoryResource** dan **ProductResource**: CRUD, upload foto (konversi/resize ke ukuran wajar), drag-and-drop urutan, toggle aktif.
   - **Halaman Pengaturan Situs**: form untuk key di `site_settings` (grup Home, About, Kontak, Kebijakan Privasi) dengan rich text dan upload gambar.
   - **FaqResource**: tabel dengan kolom pertanyaan, kategori, status aktif, tampil di Home. Aksi **tambah, ubah, hapus** (dengan konfirmasi), toggle aktif langsung dari tabel, **drag-and-drop urutan** (reorderable), serta bulk delete dan bulk aktif/nonaktif. Jawaban memakai rich text.
   - **TestimonialResource**: tabel dengan nama, cuplikan isi, rating, produk terkait, status aktif. Aksi **tambah, ubah, hapus** (dengan konfirmasi), toggle aktif, drag-and-drop urutan, bulk delete. Form memuat field upload foto (opsional) dan checkbox **"Izin tampil sudah dikonfirmasi"**. Testimoni **tidak bisa diaktifkan** bila checkbox ini belum dicentang (validasi). Tampilkan hint: "Gunakan nama samaran/inisial bila pasien tidak ingin namanya dipublikasikan."
   - Pengaturan "Hapus" untuk FAQ dan testimoni memakai soft delete, jadi tidak hilang permanen karena salah klik (sediakan juga aksi "hapus permanen" bagi admin).
5. Dashboard: widget jumlah janji hari ini, janji pending, dan 5 janji terdekat.

**Checklist:** admin dapat login, membuat cabang baru lengkap dengan layanan dan jadwal, menambah hari libur, mengelola produk, dan mengubah teks Home. Tidak ada halaman register. URL `/admin` mengembalikan 404.

---

## FASE 4 - Halaman Publik (Home, About, Katalog)

**Tujuan:** tampilan publik lengkap dan responsif.

### 4.1 Home
- Hero: judul, subjudul, tombol **"Buat Janji"** dan **"Chat WhatsApp"**, badge "Sejak 2014".
- Kategori utama (kartu): Orthosis, Prosthesis, Komponen. Tiap kartu menuju Katalog terfilter.
- Keunggulan SAM (diambil dari `site_settings`).
- **Testimoni**: bagian carousel/grid yang menampilkan testimoni aktif (nama, info singkat, isi, rating bintang bila ada, foto bila ada). Disembunyikan otomatis bila tidak ada testimoni aktif.
- **Cuplikan FAQ**: menampilkan FAQ dengan `show_on_home = true` (accordion) dan tautan "Lihat semua FAQ". Disembunyikan bila kosong.
- Produk unggulan (opsional, ambil beberapa produk aktif).
- Cabang singkat + kontak + link Instagram.

### 4.2 About Us
- Cerita, visi misi, tim (dari settings).
- **Daftar cabang aktif**: nama, alamat, WA, peta (iframe Google Maps), jam buka (dihitung dari `branch_schedules`).

### 4.3 Katalog
- Navigasi kategori 2 level (Orthosis > Upper Limb/Lower Limb/Spinal, dst.).
- Grid kartu produk: foto, nama, fungsi.
- **Pencarian** (nama, indikasi) dan filter kategori.
- Halaman detail produk `/katalog/{slug}`: foto, deskripsi, fungsi, indikasi, tombol **"Tanya via WhatsApp"** dengan pesan otomatis, misalnya `https://wa.me/62xxxx?text=Halo SAM, saya mau tanya tentang {nama produk}`, dan tombol **"Buat Janji Konsultasi"**.
- Harga **tidak ditampilkan**.
- Setiap halaman punya `<title>`, meta description, dan Open Graph sendiri. Buat `sitemap.xml` dan `robots.txt`.

### 4.4 FAQ (`/faq`)
- Daftar FAQ aktif berbentuk **accordion** (buka-tutup), urut sesuai `sort_order`, dikelompokkan per kategori bila kategori diisi.
- Kolom pencarian sederhana untuk menyaring pertanyaan (Alpine.js, tanpa reload).
- Di bawah daftar: kotak "Belum menemukan jawaban?" dengan tombol WhatsApp dan tombol Buat Janji.
- Bila tidak ada FAQ aktif, tampilkan pesan kosong yang ramah.

### 4.5 Testimoni
- Ditampilkan sebagai bagian di Home (lihat 4.1). Bila testimoni terkait produk, tampilkan tautan ke halaman produk, dan di halaman detail produk tampilkan testimoni terkait (maks. 3) bila ada.
- Hanya testimoni dengan `is_active = true` **dan** `consent_confirmed = true` yang tampil di publik.

**Checklist:** semua halaman tampil benar di lebar 360px, 768px, dan 1280px; pencarian dan filter berjalan; tombol WA membuka chat dengan pesan terisi; FAQ dan testimoni yang ditambah, diubah, dihapus, atau dinonaktifkan dari admin langsung tercermin di halaman publik (cache dibersihkan otomatis saat data berubah).

---

## FASE 5 - Logika Slot Appointment (backend)

**Tujuan:** layanan penghitung slot yang benar dan aman sebelum dibuat form-nya.

Buat `App\Services\SlotService` dengan fungsi:

1. `availableServices(Branch $branch)`: layanan aktif yang terhubung ke cabang.
2. `isDateBookable(Branch $branch, Carbon $date)`: `false` jika hari itu `is_open = false` di `branch_schedules`, ada `branch_closures` yang menutup (untuk cabang itu atau semua cabang), tanggal sudah lewat, atau melebihi batas pemesanan (default maksimal 60 hari ke depan; konfigurasi di settings).
3. `slotsFor(Branch $branch, Carbon $date)`: 
   - Bentuk slot dari `open_time` sampai `close_time` sesuai `slot_duration_minutes` (atau jam khusus dari `branch_closures`).
   - Hitung janji aktif (`pending`, `confirmed`) di slot itu. Slot **penuh** jika jumlah >= `capacity_per_slot`.
   - Jika tanggal = hari ini, slot yang sudah lewat (plus buffer 60 menit, bisa dikonfigurasi) ditandai tidak tersedia.
   - Kembalikan daftar `{start, end, label, available}`.
4. `book(array $data)`: jalankan dalam **DB transaction** dengan `lockForUpdate()` pada query hitung janji di slot tersebut, validasi ulang ketersediaan, buat `code` unik, lalu simpan. Jika slot sudah penuh, lempar exception yang diubah menjadi pesan "Slot sudah terisi, silakan pilih jam lain".

**Tes wajib (Pest/PHPUnit):**
- Hari Minggu tidak bisa dipesan.
- Tanggal libur tidak bisa dipesan, dan libur "semua cabang" berlaku untuk semua cabang.
- Slot penuh tidak tersedia.
- Dua request bersamaan ke slot kapasitas 1 hanya satu yang berhasil.
- Slot yang sudah lewat hari ini tidak tersedia.
- Layanan yang tidak terhubung ke cabang ditolak.

**Checklist:** semua tes lulus.

---

## FASE 6 - Form Appointment (frontend)

**Tujuan:** form sesuai alur berikut.

### Urutan field
1. Nama Lengkap (wajib)
2. Alamat Email (wajib, format email)
3. Usia (wajib, angka 0-120)
4. Keluhan (wajib, textarea, maks 1000 karakter)
5. No Telepon/WA (wajib, normalisasi ke format 62xxxxxxxxxx; terima 08xx/62xx/+62)
6. **Pilih Kantor** (dropdown, hanya cabang aktif)
7. **Pilih Layanan** (nonaktif sebelum kantor dipilih; terisi hanya dengan layanan cabang itu)
8. **Tanggal Kedatangan** (date picker; hari tutup/libur dinonaktifkan; teks bantuan "Operasional: Senin - Sabtu (Minggu & Tanggal Merah Tutup)" dihasilkan otomatis dari jadwal cabang)
9. **Pilih Waktu** (muncul **setelah tanggal dipilih**; tombol slot; slot penuh/lewat tampil abu-abu dan tidak bisa diklik)
10. Upload Resep Dokter (opsional; JPG/PNG/PDF, maks 5 MB)
11. Checkbox persetujuan kebijakan privasi (wajib)
12. Tombol **Submit Appointment**

### Perilaku dinamis
- Mengganti **kantor** me-reset layanan, tanggal, dan waktu.
- Mengganti **tanggal** me-reset waktu.
- Slot dimuat lewat endpoint JSON, misalnya `GET /api/slots?branch_id=&date=` dengan rate limit. Layanan dimuat lewat `GET /api/branches/{id}/services`. Atau gunakan komponen Livewire.
- Endpoint tanggal tidak tersedia: `GET /api/closed-dates?branch_id=&month=` untuk menonaktifkan tanggal di date picker.
- Tampilkan status loading, pesan error per field, dan pesan jika tidak ada slot kosong di tanggal itu.

### Keamanan form
- Validasi server (Form Request), termasuk validasi bahwa layanan memang tersedia di cabang dan slot masih kosong.
- **Anti-spam:** honeypot + Cloudflare Turnstile/hCaptcha, plus **rate limit** (misalnya maks 3 pengajuan per jam per IP dan per nomor telepon). Batasi jumlah janji aktif per nomor telepon (misal maks 2).
- File resep: cek MIME dan ekstensi, simpan di disk **privat** dengan nama acak. Unduhan hanya lewat route yang butuh login admin.

### Setelah submit
- Halaman konfirmasi berisi ringkasan janji, **kode janji**, dan link `https://domain/janji/{code}`.
- Halaman `/janji/{code}` (tanpa login, kode acak minimal 10 karakter) menampilkan detail dan tiga aksi:
  - **Ubah jadwal** (pilih tanggal/jam baru dengan aturan slot yang sama; hanya selama status `pending`/`confirmed` dan minimal H-1 sebelum janji, konfigurasi di settings)
  - **Batalkan janji** (dengan konfirmasi)
  - Tombol WA ke cabang
- Rate limit pada route ini untuk mencegah tebak-tebakan kode.

**Checklist:** alur lengkap dari memilih kantor sampai submit berjalan di HP dan desktop; kombinasi tidak valid ditolak server; double booking tidak bisa terjadi; link janji bisa membatalkan dan mengubah jadwal.

---

## FASE 7 - Notifikasi & Pengingat

1. **Email ke pasien** saat janji dibuat (berisi detail + link kelola janji), saat status berubah (dikonfirmasi/dibatalkan), dan saat jadwal diubah.
2. **Email ke admin** saat ada janji baru, dengan **nama cabang** tertera di subjek dan isi.
3. **Pengingat H-1**: scheduler harian (`app()->schedule`) mencari janji `confirmed` besok yang `reminder_sent_at` masih kosong, kirim email pengingat, lalu isi `reminder_sent_at`.
4. Semua email dikirim lewat **queue** (driver `database`) agar form tidak lambat.
5. Konfigurasi cron di hosting: `* * * * * php /path/artisan schedule:run` dan worker queue (atau `queue:work --stop-when-empty` lewat cron bila hosting tidak mendukung proses berjalan terus).

---

## FASE 8 - WhatsApp (opsional, modular)

1. **Wajib (tanpa biaya/gateway):** tombol `wa.me` di Home, detail produk, About (per cabang), dan halaman konfirmasi. Di admin, tombol "Chat WA pasien" dengan pesan template (konfirmasi/pengingat) yang bisa diedit.
2. **Opsional:** interface `WhatsAppSender` dengan implementasi gateway (misalnya Fonnte atau lainnya) dan implementasi `NullSender` sebagai default. Diaktifkan lewat `.env` (`WA_GATEWAY_ENABLED=false`). Bila aktif, kirim notifikasi janji baru ke WA admin dan pengingat H-1 ke pasien. Kegagalan gateway **tidak boleh** menggagalkan pembuatan janji (log error saja).

---

## FASE 9 - Keamanan, Privasi, SEO

**Keamanan**
- HTTPS dipaksa (redirect + `URL::forceScheme('https')` di produksi), cookie `secure`, `APP_DEBUG=false`.
- Header keamanan dasar (X-Frame-Options, X-Content-Type-Options, Referrer-Policy; CSP bila memungkinkan).
- Password admin panjang, 2FA aktif, rate limit pada login.
- Resep dokter hanya dapat diunduh admin yang login.
- Pastikan tidak ada data pasien di log aplikasi.

**Privasi (UU PDP)**
- Halaman Kebijakan Privasi: data apa yang dikumpulkan, tujuan, lama penyimpanan, kontak pengelola, cara meminta penghapusan data. Teksnya dapat diedit admin.
- Checkbox persetujuan di form appointment.
- **Testimoni**: tampilkan hanya bila pasien memberi izin (kolom `consent_confirmed`). Sarankan nama samaran/inisial, dan foto hanya dengan persetujuan tertulis. Pasien berhak meminta testimoninya dihapus kapan saja (cantumkan di kebijakan privasi).
- Artisan command terjadwal untuk **menghapus/anonimkan** data janji dan file resep yang lebih tua dari periode retensi (default 2 tahun, bisa dikonfigurasi). Konfirmasi periode ini dengan pemilik usaha.

**SEO**
- URL bersih, `<title>` dan meta description unik per halaman, `sitemap.xml`, `robots.txt`, structured data `LocalBusiness` (per cabang), `Product` sederhana, dan `FAQPage` (dibuat otomatis dari FAQ aktif di `/faq`), gambar dengan atribut `alt`, lazy loading, format WebP.

---

## FASE 10 - Backup, Testing, Deployment

**Backup**
- Pasang `spatie/laravel-backup`: backup database + folder `storage/app` harian, simpan di lokasi terpisah dari server (Google Drive/S3), simpan 14-30 hari terakhir. Notifikasi ke email admin jika backup gagal.
- Uji pemulihan (restore) minimal sekali sebelum go-live.

**Testing**
- Tes fitur: alur appointment, ubah/batal lewat kode, rate limit, validasi resep, hak akses admin.
- Uji manual di HP nyata dan minimal dua browser.
- Uji performa dasar (Lighthouse): target skor Performance dan SEO baik di mobile.

**Deployment**
1. Siapkan hosting PHP 8.2+ dengan MySQL 8, SSL aktif, dan domain.
2. Build asset lokal (`npm run build`), upload kode dan `public/build`.
3. Set `.env` produksi (`APP_ENV=production`, `APP_DEBUG=false`, DB, mail, `APP_URL` https).
4. `php artisan key:generate`, `migrate --force`, `storage:link`, `config:cache`, `route:cache`, `view:cache`.
5. Buat akun admin: `php artisan sam:create-admin`.
6. Atur cron scheduler dan queue worker.
7. Isi data asli lewat admin: logo, cabang, jadwal, hari libur, teks Home/About, foto produk.
8. Uji alur appointment end-to-end di produksi (email diterima, link kelola janji berfungsi, backup berjalan).

---

## Definition of Done (versi 1)

- [ ] Halaman publik (Home, About Us, Katalog, FAQ, Appointment) + kebijakan privasi tampil benar dan responsif
- [ ] Admin dapat menambah, mengubah, menghapus, mengaktifkan/nonaktifkan, dan mengurutkan **FAQ** dan **Testimoni**; perubahan langsung tampil di publik
- [ ] Testimoni tanpa konfirmasi izin tidak bisa diaktifkan dan tidak tampil di publik
- [ ] Katalog lengkap sesuai Lampiran A, dengan pencarian, filter, dan tombol WhatsApp
- [ ] Form appointment berurutan: kantor -> layanan -> tanggal -> jam; kombinasi tidak valid ditolak
- [ ] Layanan dapat berbeda per cabang; cabang baru bisa ditambah dari admin tanpa mengubah kode
- [ ] Tidak ada double booking; hari libur dan Minggu tidak bisa dipilih
- [ ] Pasien dapat melihat, mengubah, dan membatalkan janji lewat link unik
- [ ] Email konfirmasi, notifikasi admin, dan pengingat H-1 berjalan
- [ ] Admin tunggal dengan panel di URL non-umum, 2FA, tanpa registrasi
- [ ] Resep dokter tersimpan privat
- [ ] Anti-spam dan rate limit aktif
- [ ] Backup otomatis berjalan dan restore sudah diuji
- [ ] Semua tes lulus

## Ditunda ke versi 2
Galeri foto, artikel edukasi, tipe janji lanjutan (konsultasi awal/pengukuran/fitting/kontrol sebagai alur terpisah), laporan di admin, dua bahasa, daftar konsultasi multi-produk.

---

## Lampiran A - Data Awal Katalog (dari PDF "KATALOG SAM")

> Typo di PDF sudah dibenahi (LSO Barce -> LSO Brace, Jewwet -> Jewett, Unstabble -> Unstable, Transfermoral -> Transfemoral, Bellow -> Below). **Foto produk diekstrak dari PDF** (atau minta file asli yang lebih bersih) lalu diunggah lewat admin. Deskripsi yang kosong atau terpotong ditandai `[LENGKAPI]` dan harus dilengkapi pemilik.

### Struktur kategori
```
Orthosis
  ├─ Upper Limb Orthosis
  ├─ Lower Limb Orthosis
  └─ Spinal Orthosis
Prosthesis
  ├─ Upper Limb Prosthesis
  └─ Lower Limb Prosthesis
Komponen
  ├─ Socket
  ├─ Adaptor Socket
  ├─ Tube & Klem
  ├─ Knee Joint
  ├─ Foot Component
  └─ Foot Component (Sport)
```

### Upper Limb Orthosis
| Produk | Fungsi | Indikasi |
|---|---|---|
| Wrist Thumb Support | Fiksasi | Rheumatic Arthritis, Trigger Finger |
| Wrist Support (tipe 1) | Fiksasi | CTS ringan, Trauma pergelangan tangan, Tendinitis pergelangan tangan |
| Wrist Support (tipe 2) | Fiksasi | CTS, Trauma pergelangan tangan, Tendinitis pergelangan tangan |
| Thumb Support | Fiksasi | Rheumatic Arthritis, Trigger Finger |
| Finger Splint | Fiksasi dan Koreksi | Trigger Finger |
| Dynamic Cock Up Splint | Fiksasi dan Koreksi | Post OP fraktur radius atau ulna, Nerve Injury, Drop hand |
| Resting Splint | Fiksasi, Koreksi | Drop hand, Post Stroke, CP, Claw hand, Post OP |
| Axillary Support | Fiksasi | Cedera shoulder |
| Shoulder Support | Fiksasi | Cedera Acromio Clavicular joint |

### Lower Limb Orthosis
| Produk | Fungsi | Indikasi |
|---|---|---|
| Insole | Fiksasi dan Koreksi | Flatfoot, Intoeing |
| Dennis Brown Splint (DBS) | Koreksi dan Fiksasi | CTEV |
| SMO Splint | Koreksi | Ankle tidak stabil, bagian tengah kaki tidak lurus, kaki bengkok ke dalam atau keluar |
| Orthopedic Shoes | Koreksi | Flatfoot, LLD, Intoeing & Toeingout, Metatarsus Adductus |
| Diabetic Shoes | Fiksasi | Diabetes mellitus |
| AFO | Fiksasi, Koreksi | CP, CTEV, LLD, Polio, Drop Foot, Pes Valgus, Pes Varus |
| PTB Brace | Fiksasi, Koreksi | Charcot Joint, Pembengkakan, Fraktur |
| AFO with Rocker Bottom | Fiksasi, Koreksi | Nyeri, sensitif saat berjalan `[LENGKAPI: indikasi terpotong]` |
| KAFO | Koreksi dan Kompensasi | CP, Genu Valgus, Genu Varus, Post Poliomyelitis, Atrofi otot |
| HKAFO | Fiksasi dan Mobilisasi | Paraplegia, Spina Bifida, CP, Dislokasi panggul |
| Knee Brace (tipe 1) | Fiksasi dan Koreksi | Genu Valgus, Genu Varus, Hiperekstensi/hiperfleksi Knee, Knee Laxity |
| Knee Brace (tipe 2) | Fiksasi dan Mobilisasi | ACL, kaki O/X, Limitasi pergerakan lutut grade 2, 3, dan 4 |
| Hip Stabilizer | Fiksasi | Limitasi pergerakan hip joint |
| Ankle Support | Fiksasi dan Mobilisasi | Cedera pergelangan kaki |

### Spinal Orthosis
| Produk | Fungsi | Indikasi |
|---|---|---|
| Soft Collar Neck | Fiksasi | CP, Trauma leher, Fraktur Cervical (cedera ringan) |
| Semi Rigid Collar (Rigid Collar Neck) | Fiksasi | CP, Trauma leher, Fraktur Cervical |
| Collar Brace | Fiksasi | CP, Trauma leher, Fraktur Cervical (cedera berat) |
| LS Corset | Fiksasi | Cedera otot area lumbal, HNP, Spondylosis, LBP, Arthritis |
| LSO Brace | Fiksasi | Fraktur, LBP |
| LSO Fabric | Fiksasi | Fraktur, LBP |
| TLSK | Fiksasi | Nyeri otot, HNP, LBP |
| TLSK Fabric | Fiksasi | Nyeri otot, HNP, LBP |
| TLSO | Fiksasi dan Koreksi | HNP, LBP, Post Op, Fraktur |
| Jewett Brace | Fiksasi | Fraktur, Kifosis `[LENGKAPI: indikasi terpotong]` |
| CTO Brace | Fiksasi | Fraktur, Spinal Injury, Whiplash Injury |
| Scoliosis Brace (varian: Milwaukee, MSO, Asymmetric MSO, DMSO) | Koreksi derajat skoliosis | Scoliosis |

### Upper Limb Prosthesis
| Produk | Indikasi |
|---|---|
| Finger Prosthesis | Level amputasi pada jari-jari tangan |
| Hand Gloves Prosthesis | Level amputasi bawah pergelangan tangan (telapak sampai jari tangan) |
| AE Prosthesis | Amputasi di bawah sendi bahu |
| BE Prosthesis | Amputasi di bawah sendi siku |
| BBE Prosthesis | Amputasi di bawah sendi siku `[LENGKAPI: indikasi sama dengan BE di PDF, mohon dikonfirmasi]` |

### Lower Limb Prosthesis
| Produk | Indikasi |
|---|---|
| Foot Prosthesis | Level amputasi bawah pergelangan kaki sampai jari-jari kaki |
| Transtibial Prosthesis | Level amputasi bawah lutut / tulang tibia |
| Syme Prosthesis | Level amputasi tulang tibia dan ankle |
| Transfemoral Prosthesis | Level amputasi bawah pinggang / tulang femur |
| Hip Disarticulation Prosthesis | Level amputasi tepat pada tulang hip |

### Komponen (tanpa deskripsi di PDF, mohon dilengkapi)
| Kategori | Produk |
|---|---|
| Socket | Above Knee Socket, Below Knee Socket, Soft Socket, Silicone Liner - Cushion, Silicone Liner - Locking |
| Adaptor Socket | Adaptor Socket (4 varian foto) |
| Tube & Klem | Tube, Klem |
| Knee Joint | Mechanical Knee Axis, Pneumatic (2 varian), Hydraulic, C-Leg |
| Foot Component | SACH Foot, Single Axis Foot, Multi Axis Foot, Foot Cover |
| Foot Component (Sport) | E-PRF01, E-PRF02, E-PRF03, E-PRF04, E-PRF05, E-PRF06 |

---

## Lampiran B - Pertanyaan yang Perlu Dijawab Pemilik Sebelum Go-Live

1. Nama, alamat, nomor WA, email, dan link peta cabang yang sudah aktif.
2. Jam operasional sebenarnya (default: Senin-Sabtu 08:00-17:00, slot 1 jam, kapasitas 1 per slot).
3. Email dan nomor WA yang menerima notifikasi janji baru.
4. Logo SAM resolusi tinggi dan foto asli produk/tim/cabang.
5. Teks Home dan About (cerita, visi misi, tim, keunggulan).
6. Periode penyimpanan data pasien (retensi) dan teks kebijakan privasi.
7. Apakah akan memakai WA gateway berbayar atau cukup tombol WhatsApp biasa.
8. Domain dan hosting yang akan dipakai.
9. Kelengkapan data produk bertanda `[LENGKAPI]` dan deskripsi komponen.
10. Daftar FAQ awal yang ingin ditampilkan (pertanyaan dan jawabannya).
11. Testimoni asli beserta bukti izin tampil dari pasien/pelanggan (nama atau inisial, foto bila ada).
