<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Courier New', Courier, monospace; line-height: 1.4; color: #000; background-color: #f9f9f9; padding: 20px; }
        .receipt-container { background-color: #fff; padding: 20px; max-width: 350px; margin: 0 auto; border: 1px solid #ddd; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .divider { border-top: 1px dashed #000; margin: 10px 0; }
        .item-row { display: flex; justify-content: space-between; margin-bottom: 5px; }
        .item-name { flex-basis: 50%; }
        .item-qty { flex-basis: 15%; text-align: center; }
        .item-price { flex-basis: 35%; text-align: right; }
        .totals-table { width: 100%; margin-top: 10px; }
        .totals-table td { padding: 2px 0; }
    </style>
</head>
<body>
    <div class="receipt-container">
        <div class="text-center font-bold" style="font-size: 1.2em; margin-bottom: 5px;">
            {{ $order->outlet->name ?? 'POS Store' }}
        </div>
        <div class="text-center" style="font-size: 0.9em; margin-bottom: 10px;">
            {{ $order->outlet->address ?? '' }}<br>
            {{ $order->outlet->phone ?? '' }}
        </div>
        
        <div class="divider"></div>
        
        <div style="font-size: 0.85em; margin-bottom: 10px;">
            <div>No. Pesanan: {{ $order->invoice_number ?? $order->id }}</div>
            <div>Tanggal: {{ $order->created_at->format('d/m/Y H:i') }}</div>
            <div>Pelanggan: {{ $order->customer_name ?? 'Guest' }}</div>
        </div>

        <div class="divider"></div>

        <div style="font-size: 0.9em;">
            <div class="item-row font-bold">
                <div class="item-name">Item</div>
                <div class="item-qty">Qty</div>
                <div class="item-price">Harga</div>
            </div>
            
            @foreach($order->items as $item)
            <div class="item-row">
                <div class="item-name">{{ $item->product_name ?? ($item->product->name ?? 'Produk') }}</div>
                <div class="item-qty">{{ $item->qty }}</div>
                <div class="item-price">{{ number_format($item->price * $item->qty, 0, ',', '.') }}</div>
            </div>
            @endforeach
        </div>

        <div class="divider"></div>

        <table class="totals-table" style="font-size: 0.9em;">
            <tr>
                <td>Subtotal</td>
                <td class="text-right">{{ number_format($order->subtotal_price, 0, ',', '.') }}</td>
            </tr>
            @if($order->discount_amount > 0)
            <tr>
                <td>Diskon</td>
                <td class="text-right">-{{ number_format($order->discount_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            @if($order->tax_amount > 0)
            <tr>
                <td>Pajak</td>
                <td class="text-right">{{ number_format($order->tax_amount, 0, ',', '.') }}</td>
            </tr>
            @endif
            <tr class="font-bold" style="font-size: 1.1em;">
                <td style="padding-top: 10px;">TOTAL</td>
                <td class="text-right" style="padding-top: 10px;">{{ number_format($order->total_price, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="divider" style="margin-top: 15px;"></div>

        <div class="text-center" style="font-size: 0.85em; margin-top: 15px;">
            Terima kasih atas kunjungan Anda!<br>
            Harap simpan struk ini sebagai bukti pembayaran yang sah.
        </div>
    </div>
</body>
</html>
