<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\JobRequest;
use App\Models\PurchaseOrder;
use App\Models\PaymentRequest;

echo "JobRequest statuses and counts:\n";
$jobRequestStats = JobRequest::select('status', \DB::raw('count(*) as count'))->groupBy('status')->get();
foreach ($jobRequestStats as $stat) {
    echo "  Status: '{$stat->status}', Count: {$stat->count}\n";
}

echo "PurchaseOrder statuses and counts:\n";
$poStats = PurchaseOrder::select('status', \DB::raw('count(*) as count'))->groupBy('status')->get();
foreach ($poStats as $stat) {
    echo "  Status: '{$stat->status}', Count: {$stat->count}\n";
}

echo "PaymentRequest statuses and counts:\n";
$prStats = PaymentRequest::select('status', \DB::raw('count(*) as count'))->groupBy('status')->get();
foreach ($prStats as $stat) {
    echo "  Status: '{$stat->status}', Count: {$stat->count}\n";
}

// Let's also check if there is a printing job model or table
$tables = \DB::select('SHOW TABLES');
echo "All tables:\n";
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    if (str_contains($tableName, 'print') || str_contains($tableName, 'job')) {
        echo "  Table: {$tableName}\n";
    }
}
