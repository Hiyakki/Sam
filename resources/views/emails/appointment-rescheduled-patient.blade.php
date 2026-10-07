<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Perubahan Jadwal Janji Temu</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f7f7f6; margin: 0; padding: 24px; color: #242e24;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e3e3e1; overflow: hidden; padding: 32px;">
        <div style="text-align: center; border-bottom: 1px solid #e3e3e1; padding-bottom: 20px; margin-bottom: 24px;">
            <h2 style="font-size: 20px; font-weight: bold; margin: 0; color: #242e24;">SAM Orthotic Prosthetic</h2>
            <p style="font-size: 13px; color: #869480; margin: 4px 0 0 0;">Perubahan Jadwal Kunjungan</p>
        </div>

        <p style="font-size: 15px; line-height: 1.6;">
            Halo <strong>{{ $appointment->patient_name }}</strong>,
        </p>
        <p style="font-size: 14px; line-height: 1.6; color: #4b5549;">
            Jadwal kunjungan janji temu Anda dengan kode <strong>{{ $appointment->code }}</strong> telah berhasil diperbarui dengan detail berikut:
        </p>

        <!-- Box Jadwal Baru -->
        <div style="background-color: #ebf2e8; border: 1px solid #a5bf98; border-radius: 12px; padding: 20px; text-align: center; margin: 24px 0;">
            <div style="font-size: 11px; text-transform: uppercase; font-weight: bold; color: #869480; letter-spacing: 1px;">Jadwal Kedatangan Baru</div>
            <div style="font-size: 18px; font-weight: bold; color: #242e24; margin: 8px 0;">
                {{ $appointment->appointment_date->isoFormat('dddd, D MMMM Y') }}
            </div>
            <div style="font-size: 16px; font-weight: bold; color: #242e24;">
                Pukul {{ substr($appointment->start_time, 0, 5) }} - {{ substr($appointment->end_time, 0, 5) }} WIB
            </div>
            <div style="font-size: 12px; color: #4b5549; margin-top: 8px;">
                Lokasi: <strong>{{ $appointment->branch?->name }}</strong>
            </div>
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
