<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="background-color: #fdfbf6; padding: 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0;">

    <div style="background-color: #fffaf0; border: 1px solid #f2e2c5; border-radius: 12px; padding: 24px; max-width: 400px; margin: 0 auto; box-sizing: border-box;">
        
        <!-- Header -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="font-weight: 900; font-size: 22px; color: #5c3a21;">POS</td>
                <td align="right"><span style="background-color: #dcf3df; color: #2d7346; padding: 4px 12px; border-radius: 12px; font-size: 11px; font-weight: bold; letter-spacing: 0.5px;">PAID</span></td>
            </tr>
            <tr>
                <td colspan="2" style="color: #7b7167; font-size: 13px; padding-top: 5px; font-family: monospace;">{{ $order->invoice_number ?? $order->id }}</td>
            </tr>
        </table>

        <!-- Divider -->
        <div style="border-top: 1px solid #f2e2c5; margin: 15px 0;"></div>

        <!-- Order Details -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 14px; color: #7b7167; line-height: 2;">
            <tr>
                <td>Atas Nama</td>
                <td align="right" style="color: #4a3424; font-weight: 600;">{{ $order->customer_name ?? 'Guest' }}</td>
            </tr>
            <tr>
                <td>Tanggal</td>
                <td align="right" style="color: #4a3424; font-weight: 600;">{{ $order->created_at->format('d/m/Y, H.i.s') }}</td>
            </tr>
            <tr>
                <td>Metode Bayar</td>
                <td align="right" style="color: #4a3424; font-weight: 600;">Online ({{ strtoupper($order->payment_method ?? 'QRIS') }})</td>
            </tr>
        </table>

        <!-- Menu Title -->
        <div style="font-weight: 800; font-size: 11px; color: #9c8e81; margin-top: 24px; margin-bottom: 12px; letter-spacing: 0.5px;">RINCIAN MENU</div>

        <!-- Items List -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 15px; color: #4a3424;">
            @foreach($order->items as $item)
            <tr>
                <td width="30" style="font-weight: bold; vertical-align: top; padding-bottom: 10px;">{{ $item->qty }}x</td>
                <td style="font-weight: 600; vertical-align: top; padding-bottom: 10px;">{{ $item->product_name ?? ($item->product->name ?? 'Produk') }}</td>
                <td align="right" style="vertical-align: top; font-family: monospace; padding-bottom: 10px;">Rp {{ number_format($item->price * $item->qty, 0, ',', '.') }}</td>
            </tr>
            @endforeach
        </table>

        <!-- Dashed Divider -->
        <div style="border-top: 1px dashed #d68735; margin: 8px 0 16px 0;"></div>

        <!-- Totals -->
        <table width="100%" cellpadding="0" cellspacing="0" border="0" style="font-size: 14px; color: #7b7167;">
            <tr>
                <td style="padding-bottom: 12px;">Subtotal</td>
                <td align="right" style="padding-bottom: 12px; font-family: monospace;">Rp {{ number_format($order->subtotal_price, 0, ',', '.') }}</td>
            </tr>
            @if($order->discount_amount > 0)
            <tr>
                <td style="padding-bottom: 12px;">Diskon</td>
                <td align="right" style="padding-bottom: 12px; font-family: monospace;">-Rp {{ number_format($order->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($order->tax_amount > 0)
            <tr>
                <td style="padding-bottom: 12px;">Pajak</td>
                <td align="right" style="padding-bottom: 12px; font-family: monospace;">Rp {{ number_format($order->tax_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr>
                <td style="font-weight: 900; font-size: 16px; color: #5c3a21; padding-top: 5px;">TOTAL AKHIR</td>
                <td align="right" style="font-weight: 900; font-size: 18px; color: #a45a16; padding-top: 5px; font-family: monospace;">Rp {{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- Dashed Divider -->
        <div style="border-top: 1px dashed #d68735; margin: 16px 0;"></div>

        <!-- Footer -->
        <div style="text-align: center; color: #d68735; font-size: 12px; line-height: 1.6; margin-top: 20px;">
            Terima kasih telah berkunjung!<br>
            Sudah termasuk Pajak & Layanan
        </div>

    </div>

</body>
</html>
