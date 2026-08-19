<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $view = \Illuminate\Support\Facades\View::make('production.inventory.overview', [
        'totalBooks' => 10,
        'lowStock' => 0,
        'outOfStock' => 0,
        'books' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
        'nonBooks' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
        'bookIndices' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
        'bookBundles' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 10),
        'sites' => collect([]),
        'allBooks' => collect([]),
        'search' => '',
        'stockTransferWorkflow' => collect([]),
        'masterBookstoreInv' => collect([]),
        'masterAreaSalesInv' => collect([]),
        'masterConsignmentInv' => collect([]),
        'masterReservedInv' => collect([]),
        'masterBookSaleInv' => collect([]),
        'masterEcommerceInv' => collect([]),
        'masterDamagedInv' => collect([]),
        'masterReturnedInv' => collect([]),
        'masterInTransitInv' => collect([]),
        'areaConsignments' => collect([]),
        'directConsignments' => collect([]),
    ]);
    
    $compiled = \Illuminate\Support\Facades\Blade::compileString(file_get_contents(resource_path('views/production/inventory/overview.blade.php')));
    echo "SUCCESS: overview.blade.php COMPILED WITH ZERO SYNTAX ERRORS!\n";
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "In file: " . $e->getFile() . " on line " . $e->getLine() . "\n";
}
