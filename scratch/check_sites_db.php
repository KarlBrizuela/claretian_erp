<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Site;

$allInDb = Site::all();
echo "Total sites in DB: " . $allInDb->count() . "\n\n";

foreach ($allInDb as $s) {
    echo "ID: {$s->id} | Name: {$s->name} | Code: {$s->code} | Active: " . ($s->is_active ? 'YES' : 'NO') . "\n";
}
