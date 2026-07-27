<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $request = Illuminate\Http\Request::create('/api/reports/export', 'GET', [
        'start_date' => '2026-07-01',
        'end_date' => '2026-07-27',
        'format' => 'pdf',
        'report_type' => 'shifts'
    ]);
    $user = App\Models\User::where('role', 'manager')->first();
    Auth::login($user);
    $controller = new App\Http\Controllers\ReportController();
    $response = $controller->export($request);
    if (method_exists($response, 'getStatusCode')) {
        echo 'STATUS: ' . $response->getStatusCode() . "\n";
    } else {
        echo 'Response is not an HTTP response (maybe a download stream)' . "\n";
        echo get_class($response) . "\n";
    }
} catch (\Exception $e) {
    echo 'ERROR: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . "\n";
}
