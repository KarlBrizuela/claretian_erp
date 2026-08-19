<?php
$content = file_get_contents(__DIR__ . '/../resources/views/production/inventory/overview.blade.php');

preg_match('/@push\(\'scripts\'\)(.*?)@endpush/s', $content, $matches);
$scriptContent = $matches[1] ?? '';

// Replace @json(...) with []
while (preg_match('/@json\(/', $scriptContent)) {
    $scriptContent = preg_replace_callback('/@json\(/', function($m) use (&$scriptContent) {
        return '/*json*/[]';
    }, $scriptContent, 1);
}

// Remove rest of Blade interpolations
$cleanJs = preg_replace('/\{\{.*?\}\}/s', '1', $scriptContent);
$cleanJs = preg_replace('/\{!!.*?!!\}/s', '1', $cleanJs);
$cleanJs = preg_replace('/<\/?script.*?>/i', '', $cleanJs);

file_put_contents(__DIR__ . '/test_script.js', $cleanJs);

echo "Extracted JS length: " . strlen($cleanJs) . " bytes\n";
exec('node --check ' . escapeshellarg(__DIR__ . '/test_script.js') . ' 2>&1', $output, $returnCode);

echo implode("\n", $output) . "\n";
echo "Return code: " . $returnCode . "\n";
