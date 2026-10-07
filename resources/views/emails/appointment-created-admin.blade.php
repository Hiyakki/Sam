<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Janji Temu Baru</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f7f7f6; margin: 0; padding: 24px; color: #242e24;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e3e3e1; overflow: hidden; padding: 32px;">
        <div style="border-bottom: 2px solid #ebf2e8; padding-bottom: 16px; margin-bottom: 20px;">
            <span style="font-size: 11px; text-transform: uppercase; font-weight: bold; color: #869480; letter-spacing: 1px;">Notifikasi Admin SAM</span>
            <h2 style="font-size: 18px; font-weight: bold; margin: 4px 0 0 0; color: #242e24;">
                Janji Temu Baru: Cabang {{ $appointment->branch?->name }}
            </h2>
        </div>

        <p style="font-size: 14px; line-height: 1.6; color: #4b5549;">
            Halo Administrator, terdapat reservasi janji temu pasien baru yang masuk melalui website untuk <strong>Cabang {{ $appointment->branch?->name }}</strong>.
        </p>

        <!-- Ringkasan Pasien & Kunjungan -->
        <table style="width: 100%; border-collapse: collapse; font-size: 13px; margin: 20px 0;">
            <tr style="border-bottom: 1px solid #f0f0ee;">
                <td style="padding: 8px 0; color: #869480; width: 140px;">Kode Janji:</td>
                <td style="padding: 8px 0; font-weight: bold; font-family: monospace;">{{ $appointment->code }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #f0f0ee;">
                <td style="padding: 8px 0; color: #869480;">Nama Pasien:</td>
                <td style="padding: 8px 0; font-weight: bold;">{{ $appointment->patient_name }} ({{ $appointment->patient_age }} Thn)</td>
            </tr>
            <tr style="border-bottom: 1px solid #f0f0ee;">
                <td style="padding: 8px 0; color: #869480;">No. WhatsApp:</td>
                <td style="padding: 8px 0; font-weight: bold;">{{ $appointment->patient_phone }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #f0f0ee;">
                <td style="padding: 8px 0; color: #869480;">Email:</td>
                <td style="padding: 8px 0;">{{ $appointment->patient_email }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #f0f0ee;">
                <td style="padding: 8px 0; color: #869480;">Cabang Tujuan:</td>
                <td style="padding: 8px 0; font-weight: bold; color: #242e24;">{{ $appointment->branch?->name }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #f0f0ee;">
                <td style="padding: 8px 0; color: #869480;">Layanan:</td>
                <td style="padding: 8px 0;">{{ $appointment->service?->name }}</td>
            </tr>
            <tr style="border-bottom: 1px solid #f0f0ee;">
                <td style="padding: 8px 0; color: #869480;">Jadwal:</td>
                <td style="padding: 8px 0; font-weight: bold; color: #242e24;">
                    {{ $appointment->appointment_date->isoFormat('dddd, D MMMM Y') }} • {{ substr($appointment->start_time, 0, 5) }} WIB
                </td>
            </tr>
            <tr style="border-bottom: 1px solid #f0f0ee;">
                <td style="padding: 8px 0; color: #869480;">Keluhan Medis:</td>
                <td style="padding: 8px 0; color: #4b5549;">{{ $appointment->complaint }}</td>
            </tr>
            <tr>
                <td style="padding: 8px 0; color: #869480;">File Resep:</td>
                <td style="padding: 8px 0;">
                    @if($appointment->prescription_path)
                        <span style="color: #15803d; font-weight: bold;">Tersedia (Tersimpan di Disk Privat)</span>
                    @else
                        <span style="color: #869480;">Tidak ada file resep</span>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Tombol Akses Panel -->
        <div style="text-align: center; margin: 28px 0 12px 0;">
            <a href="{{ route('filament.kelola-sam.resources.appointments.index') }}"
               style="background-color: #e8343b; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-size: 13px; font-weight: bold; display: inline-block;">
                Buka di Admin Panel (/kelola-sam)
            </a>
        </div>
    </div>
</body>
</html>
