<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $compiler = app('blade.compiler');
    $content = file_get_contents(__DIR__ . '/../resources/views/production/inventory/overview.blade.php');
    $contentWithCloseTag = $content . "\n</x-app-layout>\n";
    $compiled = $compiler->compileString($contentWithCloseTag);
    file_put_contents(__DIR__ . '/test_compiled_overview.php', $compiled);
    echo "Blade compiled successfully! Length: " . strlen($compiled) . "\n";
    system('php -l ' . escapeshellarg(__DIR__ . '/test_compiled_overview.php'));
} catch (\Throwable $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
