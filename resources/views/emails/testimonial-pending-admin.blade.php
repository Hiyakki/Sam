<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Testimoni Baru Menunggu Moderasi</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f7f7f6; margin: 0; padding: 24px; color: #242e24;">
    <div style="max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; border: 1px solid #e3e3e1; overflow: hidden; padding: 32px;">
        <div style="text-align: center; border-bottom: 1px solid #e3e3e1; padding-bottom: 20px; margin-bottom: 24px;">
            <h2 style="font-size: 20px; font-weight: bold; margin: 0; color: #1E2A4A;">SAM Orthotic Prosthetic</h2>
            <p style="font-size: 13px; color: #6b7280; margin: 4px 0 0 0;">Notifikasi Moderasi Konten Website</p>
        </div>

        <p style="font-size: 15px; line-height: 1.6;">
            Halo Admin SAM,
        </p>
        <p style="font-size: 14px; line-height: 1.6; color: #4b5549;">
            Terdapat testimoni baru dari pasien/publik yang masuk dan menunggu moderasi sebelum ditampilkan pada website:
        </p>

        <div style="background-color: #f8fafc; border: 1px solid #e2e8f0; border-radius: 12px; padding: 20px; margin: 20px 0;">
            <table style="width: 100%; border-collapse: collapse; font-size: 13px;">
                <tr>
                    <td style="padding: 6px 0; color: #64748b; width: 130px;">Nama Pengirim:</td>
                    <td style="padding: 6px 0; font-weight: bold; color: #1e293b;">{{ $testimonial->name }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b;">Informasi / Peran:</td>
                    <td style="padding: 6px 0; color: #334155;">{{ $testimonial->role_or_info }}</td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b;">Penilaian:</td>
                    <td style="padding: 6px 0; font-weight: bold; color: #d97706;">
                        {{ $testimonial->rating }} / 5 Bintang
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b; vertical-align: top;">Isi Ulasan:</td>
                    <td style="padding: 6px 0; color: #1e293b; font-style: italic; line-height: 1.5;">
                        "{{ $testimonial->content }}"
                    </td>
                </tr>
                <tr>
                    <td style="padding: 6px 0; color: #64748b;">Status Saat Ini:</td>
                    <td style="padding: 6px 0; font-weight: bold; color: #ca8a04;">
                        Menunggu Moderasi (Belum Tayang)
                    </td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; margin: 28px 0 12px 0;">
            <a href="{{ url('/kelola-sam/testimonials') }}"
               style="background-color: #1E2A4A; color: #ffffff; text-decoration: none; padding: 12px 24px; border-radius: 10px; font-size: 13px; font-weight: bold; display: inline-block;">
                Buka Panel Kelola Testimoni
            </a>
        </div>
    </div>
</body>
</html>
