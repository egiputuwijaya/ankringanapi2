<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$orders = \App\Models\Order::latest()->take(3)->get(['id', 'invoice_number', 'outlet_id', 'status', 'created_at']);
$history = \App\Models\HistoryTransaction::latest()->take(3)->get(['id', 'order_id', 'outlet_id', 'invoice_number']);
$counters = \App\Models\InvoiceCounter::all();
$outlets = \App\Models\Outlet::all(['id', 'name']);
$users = \App\Models\User::where('role', 'karyawan')->get(['id', 'name', 'outlet_id']);

echo json_encode([
    'latest_orders' => $orders,
    'latest_history' => $history,
    'counters' => $counters,
    'outlets' => $outlets,
    'cashiers' => $users,
], JSON_PRETTY_PRINT);
