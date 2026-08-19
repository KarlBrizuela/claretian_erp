<?php

$tokens = token_get_all(file_get_contents(__DIR__ . '/test_compiled_overview.php'));
$controlStack = [];

foreach ($tokens as $token) {
    if (is_array($token)) {
        list($id, $text, $line) = $token;
        if ($id === T_IF) {
            $controlStack[] = ['type' => 'IF', 'line' => $line];
        } elseif ($id === T_ENDIF) {
            if (empty($controlStack)) {
                echo "Unexpected ENDIF on line $line\n";
            } else {
                $last = array_pop($controlStack);
                // echo "Matched IF line {$last['line']} with ENDIF line $line\n";
            }
        } elseif ($id === T_FOREACH) {
            $controlStack[] = ['type' => 'FOREACH', 'line' => $line];
        } elseif ($id === T_ENDFOREACH) {
            if (!empty($controlStack)) array_pop($controlStack);
        } elseif ($id === T_FOR) {
            $controlStack[] = ['type' => 'FOR', 'line' => $line];
        } elseif ($id === T_ENDFOR) {
            if (!empty($controlStack)) array_pop($controlStack);
        }
    }
}

echo "Remaining unclosed structures in token stream: " . count($controlStack) . "\n";
foreach ($controlStack as $item) {
    echo "Unclosed {$item['type']} at line {$item['line']}\n";
}
