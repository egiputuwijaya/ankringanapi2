<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { padding: 20px; border: 1px solid #e2e8f0; border-radius: 8px; max-width: 600px; margin: 0 auto; }
        .header { background-color: #d97706; color: white; padding: 15px; border-radius: 8px 8px 0 0; text-align: center; }
        .content { padding: 20px; }
        .footer { font-size: 12px; color: #6b7280; text-align: center; margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Paket Berlangganan Aktif!</h2>
        </div>
        <div class="content">
            <p>Halo <strong>{{ $user->name }}</strong>,</p>
            <p>Terima kasih telah melakukan pembayaran. Kami ingin menginformasikan bahwa paket langganan Anda telah <strong>AKTIF</strong>.</p>
            <p>Sekarang Anda dapat mengakses semua fitur yang ada pada platform kami sesuai dengan paket yang Anda pilih.</p>
            <p>Silakan login ke dashboard Anda untuk mulai mengelola bisnis Anda.</p>
            <p>Jika Anda memiliki pertanyaan, jangan ragu untuk membalas email ini.</p>
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
