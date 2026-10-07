<?php

namespace App\Contracts;

interface WhatsAppSender
{
    /**
     * Kirim pesan teks WhatsApp ke nomor tujuan.
     *
     * @param  string  $to  Nomor tujuan (format 08xx atau 62xx)
     * @param  string  $message  Isi pesan teks
     * @return bool True jika berhasil atau diabaikan secara aman, False jika gagal
     */
    public function send(string $to, string $message): bool;
}
