<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$tables = \DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $tableName = array_values((array)$table)[0];
    $count = \DB::table($tableName)->count();
    if ($count > 0) {
        echo "Table: {$tableName} - Count: {$count}\n";
    }
}
