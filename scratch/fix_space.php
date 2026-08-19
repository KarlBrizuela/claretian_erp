<?php
$filePath = 'c:/Users/karlb/Downloads/erp_v9/claretian-ERP/resources/views/production/inventory/overview.blade.php';
$content = file_get_contents($filePath);

$pattern = '/(\s*<\/div>\s*<\/div>\s*<\/div>\s*<\/div>)\s*<\/div>\s*<nav>\s*\{\{\s*\$books->appends\(\[\'search\' => request\(\'search\'\)\]\)->links\(\)\s*\}\}\s*<\/nav>\s*<\/div>\s*<\/div>/s';

if (preg_match($pattern, $content)) {
    $newContent = preg_replace($pattern, '$1', $content);
    file_put_contents($filePath, $newContent);
    echo "SUCCESS: Replaced stray closing divs and nav!\n";
} else {
    echo "PATTERN MATCH FAILED\n";
}
