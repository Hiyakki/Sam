<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembaruan Status Janji Temu</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f7f7f6; margin: 0; padding: 24px; color: #242e24;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e3e3e1; overflow: hidden; padding: 32px;">
        <div style="text-align: center; border-bottom: 1px solid #e3e3e1; padding-bottom: 20px; margin-bottom: 24px;">
            <h2 style="font-size: 20px; font-weight: bold; margin: 0; color: #242e24;">SAM Orthotic Prosthetic</h2>
            <p style="font-size: 13px; color: #869480; margin: 4px 0 0 0;">Informasi Status Janji Temu</p>
        </div>

        <p style="font-size: 15px; line-height: 1.6;">
            Halo <strong>{{ $appointment->patient_name }}</strong>,
        </p>

        @php
            $statusLabels = [
                'pending' => 'Menunggu Konfirmasi',
                'confirmed' => 'Terkonfirmasi',
                'completed' => 'Kunjungan Selesai',
                'cancelled' => 'Dibatalkan',
                'no_show' => 'Tidak Hadir',
            ];
            $statusTitle = $statusLabels[$appointment->status] ?? ucfirst($appointment->status);
        @endphp

        <p style="font-size: 14px; line-height: 1.6; color: #4b5549;">
            Status janji temu Anda dengan kode <strong>{{ $appointment->code }}</strong> telah diperbarui menjadi:
        </p>

        <div style="background-color: #f7f7f6; border-radius: 12px; padding: 16px; text-align: center; margin: 20px 0; border: 1px solid #e3e3e1;">
            <div style="font-size: 18px; font-weight: bold; color: #242e24;">{{ $statusTitle }}</div>
            @if($appointment->status === 'confirmed')
                <div style="font-size: 12px; color: #15803d; margin-top: 6px;">Jadwal Anda telah disetujui. Silakan hadir tepat waktu di klinik.</div>
            @elseif($appointment->status === 'cancelled')
                <div style="font-size: 12px; color: #be123c; margin-top: 6px;">
                    Janji temu telah dibatalkan{{ $appointment->cancelled_by === 'patient' ? ' atas permintaan Anda' : ' oleh pihak admin' }}.
                </div>
            @endif
        </div>

        @if($appointment->admin_notes)
            <div style="background-color: #fffbeb; border: 1px solid #fef3c7; border-radius: 10px; padding: 14px; margin: 16px 0; font-size: 13px; color: #92400e;">
                <strong>Catatan Admin:</strong> {{ $appointment->admin_notes }}
            </div>
        @endif

        <table style="width: 100%; border-collapse: collapse; font-size: 13px; margin: 20px 0;">
            <tr>
                <td style="padding: 6px 0; color: #869480; width: 120px;">Cabang:</td>
                <td style="padding: 6px 0; font-weight: bold;">{{ $appointment->branch?->name }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #869480;">Layanan:</td>
                <td style="padding: 6px 0;">{{ $appointment->service?->name }}</td>
            </tr>
            <tr>
                <td style="padding: 6px 0; color: #869480;">Jadwal:</td>
                <td style="padding: 6px 0;">
                    {{ $appointment->appointment_date->isoFormat('dddd, D MMMM Y') }} ({{ substr($appointment->start_time, 0, 5) }} WIB)
                </td>
            </tr>
        </table>

        <div style="text-align: center; margin: 28px 0 12px 0;">
            <a href="{{ route('appointment.manage', $appointment->code) }}"
               style="background-color: #242e24; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-size: 13px; font-weight: bold; display: inline-block;">
                Buka Halaman Kelola Janji
            </a>
        </div>
    </div>
</body>
</html>
