<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$v = __DIR__ . '/../resources/views/marketing/approval-queue.blade.php';
$content = file_get_contents($v);
try {
    $compiled = Illuminate\Support\Facades\Blade::compileString($content);
    $tmpFile = __DIR__ . '/temp_compiled.php';
    file_put_contents($tmpFile, $compiled);
    exec('php -l ' . escapeshellarg($tmpFile), $out, $ret);
    if ($ret !== 0) {
        echo "SYNTAX ERROR IN: " . basename($v) . "\n";
        echo implode("\n", $out) . "\n";
    } else {
        echo "SUCCESS: approval-queue.blade.php COMPILED WITH ZERO SYNTAX ERRORS!\n";
    }
    @unlink($tmpFile);
} catch (\Throwable $e) {
    echo "BLADE COMPILER ERROR IN: " . basename($v) . " -> " . $e->getMessage() . "\n";
}
