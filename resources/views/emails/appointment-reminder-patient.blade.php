<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pengingat Janji Temu Besok</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f7f7f6; margin: 0; padding: 24px; color: #242e24;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e3e3e1; overflow: hidden; padding: 32px;">
        <div style="text-align: center; border-bottom: 1px solid #e3e3e1; padding-bottom: 20px; margin-bottom: 24px;">
            <span style="font-size: 11px; text-transform: uppercase; font-weight: bold; color: #869480; letter-spacing: 1px;">Pengingat Kunjungan (H-1)</span>
            <h2 style="font-size: 20px; font-weight: bold; margin: 6px 0 0 0; color: #242e24;">Janji Temu Anda Besok Hari</h2>
        </div>

        <p style="font-size: 15px; line-height: 1.6;">
            Halo <strong>{{ $appointment->patient_name }}</strong>,
        </p>
        <p style="font-size: 14px; line-height: 1.6; color: #4b5549;">
            Ini adalah pengingat ramah bahwa Anda memiliki jadwal kunjungan konsultasi ortotik prostetik yang <strong>terkonfirmasi untuk besok hari</strong> di SAM Orthotic Prosthetic.
        </p>

        <!-- Detail Kunjungan Besok -->
        <div style="background-color: #ebf2e8; border: 1px solid #a5bf98; border-radius: 12px; padding: 20px; margin: 20px 0;">
            <div style="font-size: 13px; color: #4b5549; margin-bottom: 6px;">Kode Reservasi: <strong style="font-family: monospace; font-size: 15px; color: #242e24;">{{ $appointment->code }}</strong></div>
            <div style="font-size: 16px; font-weight: bold; color: #242e24;">
                Pukul {{ substr($appointment->start_time, 0, 5) }} - {{ substr($appointment->end_time, 0, 5) }} WIB
            </div>
            <div style="font-size: 13px; color: #242e24; margin-top: 8px;">
                Lokasi: <strong>{{ $appointment->branch?->name }}</strong>
            </div>
            <div style="font-size: 12px; color: #4b5549; margin-top: 4px;">
                {{ $appointment->branch?->address }}
            </div>
        </div>

        <!-- Tips Persiapan -->
        <div style="background-color: #f7f7f6; border-radius: 12px; padding: 16px; margin: 20px 0; font-size: 12px; color: #4b5549; line-height: 1.6;">
            <strong style="color: #242e24;">Tips Persiapan Kunjungan:</strong>
            <ul style="margin: 6px 0 0 0; padding-left: 20px;">
                <li>Harap tiba 10–15 menit sebelum slot waktu Anda dimulai.</li>
                <li>Kenakan pakaian yang nyaman dan memudahkan pemeriksaan anggota gerak (misal celana pendek/longgar).</li>
                <li>Bawalah hasil rontgen atau rujukan dokter bila ada.</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 28px 0 12px 0;">
            <a href="{{ route('appointment.manage', $appointment->code) }}"
               style="background-color: #242e24; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-size: 13px; font-weight: bold; display: inline-block;">
                Buka Halaman Kelola Janji
            </a>
        </div>
    </div>
</body>
</html>
