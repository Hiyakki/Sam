<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Agenda Kunjungan Pasien Hari Ini</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f7f7f6; margin: 0; padding: 24px; color: #242e24;">
    <div style="max-width: 650px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e3e3e1; overflow: hidden; padding: 32px;">
        <div style="text-align: center; border-bottom: 1px solid #e3e3e1; padding-bottom: 20px; margin-bottom: 24px;">
            <h2 style="font-size: 20px; font-weight: bold; margin: 0; color: #1E2A4A;">SAM Orthotic Prosthetic</h2>
            <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0 0;">Ringkasan Agenda Kunjungan Harian (07:30 WIB)</p>
        </div>

        <p style="font-size: 15px; line-height: 1.6;">
            Halo Tim Admin SAM,
        </p>
        <p style="font-size: 14px; line-height: 1.6; color: #4b5549;">
            Berikut adalah daftar janji temu pasien yang dijadwalkan hadir hari ini, 
            <strong>{{ \Carbon\Carbon::parse($date)->locale('id')->translatedFormat('l, d F Y') }}</strong>:
        </p>

        <div style="background-color: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px; padding: 12px 16px; margin: 16px 0; font-size: 13px; color: #166534;">
            Total Pasien Terjadwal: <strong>{{ $appointments->count() }} orang</strong>
        </div>

        <table style="width: 100%; border-collapse: collapse; font-size: 13px; margin: 20px 0;">
            <thead>
                <tr style="background-color: #f8fafc; border-bottom: 2px solid #e2e8f0; text-align: left;">
                    <th style="padding: 10px 8px; color: #475569;">Jam</th>
                    <th style="padding: 10px 8px; color: #475569;">Pasien / WA</th>
                    <th style="padding: 10px 8px; color: #475569;">Cabang</th>
                    <th style="padding: 10px 8px; color: #475569;">Layanan</th>
                    <th style="padding: 10px 8px; color: #475569;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($appointments as $appt)
                    <tr style="border-bottom: 1px solid #e2e8f0;">
                        <td style="padding: 10px 8px; font-weight: bold; color: #1E2A4A; white-space: nowrap;">
                            {{ substr($appt->start_time, 0, 5) }} WIB
                        </td>
                        <td style="padding: 10px 8px;">
                            <div style="font-weight: 600; color: #1e293b;">{{ $appt->patient_name }}</div>
                            <div style="font-size: 11px; color: #64748b;">
                                <a href="https://wa.me/{{ $appt->patient_phone }}" style="color: #15803d; text-decoration: none;">
                                    {{ $appt->patient_phone }}
                                </a>
                            </div>
                        </td>
                        <td style="padding: 10px 8px; color: #334155;">{{ $appt->branch?->name }}</td>
                        <td style="padding: 10px 8px; color: #334155;">{{ $appt->service?->name }}</td>
                        <td style="padding: 10px 8px;">
                            <span style="display: inline-block; padding: 2px 8px; border-radius: 9999px; font-size: 11px; font-weight: 600; {{ $appt->status === 'confirmed' ? 'background-color: #dbeafe; color: #1e40af;' : 'background-color: #fef3c7; color: #92400e;' }}">
                                {{ $appt->status === 'confirmed' ? 'Terkonfirmasi' : 'Menunggu' }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div style="text-align: center; margin: 28px 0 12px 0;">
            <a href="{{ url('/kelola-sam/appointments') }}"
               style="background-color: #1E2A4A; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-size: 13px; font-weight: bold; display: inline-block;">
                Buka Panel Kelola Janji Temu
            </a>
        </div>
    </div>
</body>
</html>
