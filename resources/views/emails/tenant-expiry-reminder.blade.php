<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; max-width: 600px; margin: 0 auto; }
        .header { background-color: #f59e0b; color: white; padding: 15px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { padding: 20px; }
        .footer { font-size: 12px; color: #6b7280; text-align: center; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 20px; }
        .btn { display: inline-block; padding: 10px 20px; background-color: #f59e0b; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Peringatan: Masa Aktif Paket Akan Berakhir</h2>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $user->name }}</strong>,</p>
            <p>Kami ingin mengingatkan Anda bahwa paket langganan Anda akan berakhir dalam <strong>{{ $days_remaining }} hari lagi</strong>.</p>
            <p>Untuk menghindari gangguan pada operasional bisnis Anda dan memastikan sistem POS tetap dapat digunakan, harap segera melakukan perpanjangan paket langganan Anda sebelum masa aktif berakhir.</p>
            <p style="text-align: center;">
                <a href="{{ env('FRONTEND_URL') }}/billing" class="btn">Perpanjang Sekarang</a>
            </p>
            <br>
            <p>Abaikan email ini jika Anda sudah melakukan pembayaran hari ini.</p>
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
