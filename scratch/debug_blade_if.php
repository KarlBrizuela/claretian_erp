<?php

$lines = file(__DIR__ . '/test_compiled_overview.php');
$stack = [];

foreach ($lines as $num => $line) {
    $lineNum = $num + 1;
    
    // Check for "if (...):" or "elseif (...):" or "else:" or "endif;"
    if (preg_match('/<\?php\s+if\s*\(.*?\)\s*:\s*\?>/s', $line) || preg_match('/<\?php\s+if\s*\(.*?\):\s*\?>/', $line)) {
        $stack[] = ['line' => $lineNum, 'content' => trim($line)];
    } elseif (strpos($line, '<?php if(') !== false && strpos($line, '): ?>') !== false) {
        $stack[] = ['line' => $lineNum, 'content' => trim($line)];
    }
    
    if (strpos($line, '<?php endif; ?>') !== false) {
        if (!empty($stack)) {
            array_pop($stack);
        } else {
            echo "Extra endif at line $lineNum: " . trim($line) . "\n";
        }
    }
}

echo "Remaining unclosed IF statements: " . count($stack) . "\n";
foreach ($stack as $item) {
    echo "Line {$item['line']}: {$item['content']}\n";
}
