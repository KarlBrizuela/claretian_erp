<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$nbsOrders = \App\Models\SalesOrder::where('so_number', 'like', 'SO-NBS-%')->get();

foreach ($nbsOrders as $so) {
    if ($so->remarks && (str_starts_with($so->remarks, 'Branch:') || str_contains($so->remarks, 'Branch:'))) {
        // If remarks only contained Branch: ..., clear it or keep actual notes
        $cleaned = trim(preg_replace('/Branch:\s*[^|\n\r]+/', '', $so->remarks));
        $so->update(['remarks' => $cleaned ?: null]);
        echo "Cleaned remarks for SO #{$so->so_number} (was: '{$so->remarks}' -> now: " . var_export($cleaned, true) . ")" . PHP_EOL;
    }
}
