<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; max-width: 600px; margin: 0 auto; }
        .header { background-color: #f59e0b; color: white; padding: 15px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { padding: 20px; }
        .footer { font-size: 12px; color: #6b7280; text-align: center; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; margin-bottom: 15px; }
        .table th, .table td { border: 1px solid #e2e8f0; padding: 10px; text-align: left; }
        .table th { background-color: #f8fafc; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Tagihan Menunggu Pembayaran</h2>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $user->name }}</strong>,</p>
            <p>Terima kasih telah mendaftar di Ankringan POS. Berikut adalah rincian tagihan paket yang telah Anda pilih. Silakan selesaikan pembayaran untuk mengaktifkan akun Anda.</p>
            
            <table class="table">
                <tr>
                    <th>Paket</th>
                    <td>{{ $package->name }}</td>
                </tr>
                <tr>
                    <th>Harga</th>
                    <td>Rp {{ number_format($package->price, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td><strong style="color: #ea580c;">Menunggu Pembayaran</strong></td>
                </tr>
            </table>

            <p>Jika Anda tidak merasa melakukan pendaftaran ini, silakan abaikan email ini.</p>
            <br>
            <p>Salam hangat,</p>
            <p><strong>Tim Ankringan POS</strong></p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Ankringan POS. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
