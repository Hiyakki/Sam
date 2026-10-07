---
trigger: always_on
---

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