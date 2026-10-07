<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Konfirmasi Janji Temu</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f7f7f6; margin: 0; padding: 24px; color: #242e24;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e3e3e1; overflow: hidden; padding: 32px;">
        <div style="text-align: center; border-bottom: 1px solid #e3e3e1; pb: 20px; margin-bottom: 24px;">
            <h2 style="font-size: 20px; font-weight: bold; margin: 0; color: #242e24;">SAM Orthotic Prosthetic</h2>
            <p style="font-size: 13px; color: #869480; margin: 4px 0 0 0;">Layanan Ortotik & Prostetik Presisi</p>
        </div>

        <p style="font-size: 15px; line-height: 1.6;">
            Halo <strong>{{ $appointment->patient_name }}</strong>,
        </p>
        <p style="font-size: 14px; line-height: 1.6; color: #4b5549;">
            Terima kasih telah mempercayakan kebutuhan mobilitas Anda kepada SAM Orthotic Prosthetic. Pendaftaran janji temu konsultasi Anda telah berhasil kami terima.
        </p>

        <!-- Box Kode Janji -->
        <div style="background-color: #ebf2e8; border: 1px dashed #a5bf98; border-radius: 12px; padding: 16px; text-align: center; margin: 24px 0;">
            <div style="font-size: 11px; text-transform: uppercase; font-weight: bold; color: #869480; letter-spacing: 1px;">Kode Janji Temu Anda</div>
            <div style="font-size: 24px; font-weight: bold; color: #242e24; margin: 6px 0; letter-spacing: 2px; font-family: monospace;">{{ $appointment->code }}</div>
            <div style="font-size: 11px; color: #4b5549;">Gunakan kode ini untuk memeriksa atau mengelola jadwal Anda.</div>
        </div>

        <!-- Detail Kunjungan -->
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; margin-bottom: 24px;">
            <tr>
                <td style="padding: 8px 0; color: #869480; width: 140px;">Cabang:</td>
                <td style="padding: 8px 0; font-weight: bold; color: #242e24;">{{ $appointment->branch?->name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #869480;">Alamat:</td>
                <td style="padding: 8px 0; color: #4b5549;">{{ $appointment->branch?->address }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #869480;">Layanan:</td>
                <td style="padding: 8px 0; font-weight: bold; color: #242e24;">{{ $appointment->service?->name }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #869480;">Tanggal:</td>
                <td style="padding: 8px 0; font-weight: bold; color: #242e24;">{{ $appointment->appointment_date->isoFormat('dddd, D MMMM Y') }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #869480;">Waktu:</td>
                <td style="padding: 8px 0; font-weight: bold; color: #242e24;">{{ substr($appointment->start_time, 0, 5) }} - {{ substr($appointment->end_time, 0, 5) }} WIB</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #869480;">Status:</td>
                <td style="padding: 8px 0; font-weight: bold; color: #b45309;">Menunggu Konfirmasi Admin</td>
            </tr>
        </table>

        <!-- Tombol Aksi Kelola -->
        <div style="text-align: center; margin: 32px 0 16px 0;">
            <a href="{{ route('appointment.manage', $appointment->code) }}"
               style="background-color: #242e24; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-size: 13px; font-weight: bold; display: inline-block;">
                Kelola / Lihat Detail Janji Temu
            </a>
        </div>

        <p style="font-size: 12px; color: #869480; line-height: 1.5; margin-top: 24px; border-top: 1px solid #e3e3e1; padding-top: 16px;">
            Jika Anda ingin bertanya atau konfirmasi cepat via WhatsApp, silakan hubungi kontak cabang kami di <strong>{{ $appointment->branch?->phone_wa }}</strong>.
        </p>
    </div>
</body>
</html>
