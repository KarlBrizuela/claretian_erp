<?php

$tokens = token_get_all(file_get_contents(__DIR__ . '/test_compiled_overview.php'));
$stack = [];

foreach ($tokens as $token) {
    if (is_array($token)) {
        list($id, $text, $line) = $token;
        if ($id === T_IF) {
            $stack[] = ['line' => $line];
        } elseif ($id === T_ENDIF) {
            if (count($stack) > 0) {
                $popped = array_pop($stack);
                // echo "Matched IF line {$popped['line']} with ENDIF line $line\n";
            } else {
                echo "ERR: Unmatched ENDIF at line $line\n";
            }
        }
    }
}

echo "\nUnclosed T_IF count: " . count($stack) . "\n";
foreach ($stack as $s) {
    echo "Unclosed IF originally at compiled line: {$s['line']}\n";
}
