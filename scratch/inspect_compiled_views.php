<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$viewPath = resource_path('views/production/inventory/overview.blade.php');
$compiler = app('blade.compiler');
$compiled = $compiler->compileString(file_get_contents($viewPath));

file_put_contents(__DIR__ . '/compiled_overview.php', $compiled);

// Render the view with fake/real controller data
$viewData = app(App\Http\Controllers\Production\InventoryController::class);

echo "Compiled Blade successfully written to scratch/compiled_overview.php\n";
