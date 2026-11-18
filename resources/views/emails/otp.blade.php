
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email SIMASTA</title>
</head>

<body style="margin:0;padding:32px 16px;background:#f1f5f9;font-family:Arial,sans-serif;color:#334155;">

    <div style="max-width:520px;margin:auto;background:#ffffff;border:1px solid #e2e8f0;border-radius:14px;overflow:hidden;">

        <!-- HEADER -->
        <div style="padding:28px 24px;text-align:center;background:#eff6ff;border-bottom:1px solid #dbeafe;">

            <h1 style="margin:0;color:#163f71;font-size:26px;">
                SIMASTA
            </h1>

            <p style="margin:9px 0 0;color:#64748b;font-size:13px;">
                Sistem Informasi Manajemen Magang
            </p>

            <p style="margin:5px 0 0;color:#2563eb;font-size:12px;font-weight:bold;">
                BPS Kota Batu
            </p>
        </div>

        <!-- CONTENT -->
        <div style="padding:30px 26px;">

            <h2 style="margin:0 0 18px;color:#163f71;font-size:20px;">
                Verifikasi Alamat Email
            </h2>

            <p style="font-size:14px;line-height:1.8;">
                Halo, <strong>{{ $nama }}</strong>!
            </p>

            <p style="font-size:14px;line-height:1.8;">
                Terima kasih telah mendaftar di SIMASTA.
                Masukkan kode berikut pada halaman verifikasi
                untuk mengaktifkan akun mahasiswa kamu.
            </p>

            <!-- OTP -->
            <div style="margin:25px 0;padding:22px 12px;background:#eff6ff;border:1px solid #bfdbfe;border-radius:10px;text-align:center;">

                <p style="margin:0 0 12px;color:#64748b;font-size:12px;font-weight:bold;">
                    KODE VERIFIKASI
                </p>

                <div style="font-size:34px;font-weight:bold;letter-spacing:8px;color:#2563eb;">
                    {{ $code }}
                </div>

                <p style="margin:13px 0 0;color:#64748b;font-size:12px;">
                    Kode berlaku selama 5 menit
                </p>
            </div>

            <p style="font-size:13px;line-height:1.8;color:#64748b;">
                Demi keamanan akun, jangan bagikan kode OTP ini
                kepada siapa pun, termasuk pihak yang mengaku
                sebagai petugas SIMASTA.
            </p>

            <p style="font-size:13px;line-height:1.8;color:#64748b;">
                Jika kamu tidak merasa melakukan pendaftaran,
                abaikan pesan ini.
            </p>

        </div>

        <!-- FOOTER -->
        <div style="padding:18px;text-align:center;background:#f8fafc;border-top:1px solid #e2e8f0;">

            <p style="margin:0;color:#64748b;font-size:12px;font-weight:bold;">
                SIMASTA — BPS Kota Batu
            </p>

            <p style="margin:6px 0 0;color:#94a3b8;font-size:11px;">
                Email verifikasi otomatis. Mohon tidak membalas.
            </p>

        </div>

    </div>

</body>
</html>
