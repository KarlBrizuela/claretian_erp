<?php

$content = file_get_contents(__DIR__ . '/../resources/views/production/inventory/overview.blade.php');
$lines = explode("\n", $content);

$stack = [];

foreach ($lines as $idx => $line) {
    $lineNum = $idx + 1;
    // Match blade control directives
    preg_match_all('/@(if|elseif|else|endif|forelse|empty|endforelse|foreach|endforeach)\b/', $line, $matches, PREG_OFFSET_CAPTURE);
    
    foreach ($matches[1] as $m) {
        $tag = $m[0];
        if (in_array($tag, ['if', 'forelse', 'foreach'])) {
            $stack[] = ['tag' => $tag, 'line' => $lineNum];
        } elseif ($tag === 'endif') {
            if (empty($stack)) {
                echo "Line $lineNum: Extra @endif\n";
            } else {
                $top = array_pop($stack);
                if ($top['tag'] !== 'if' && $top['tag'] !== 'forelse') {
                    echo "Line $lineNum: Mismatched @endif for @{$top['tag']} from line {$top['line']}\n";
                }
            }
        } elseif ($tag === 'endforelse') {
            if (empty($stack)) {
                echo "Line $lineNum: Extra @endforelse\n";
            } else {
                $top = array_pop($stack);
                if ($top['tag'] !== 'forelse') {
                    echo "Line $lineNum: Mismatched @endforelse for @{$top['tag']} from line {$top['line']}\n";
                }
            }
        } elseif ($tag === 'endforeach') {
            if (empty($stack)) {
                echo "Line $lineNum: Extra @endforeach\n";
            } else {
                $top = array_pop($stack);
                if ($top['tag'] !== 'foreach') {
                    echo "Line $lineNum: Mismatched @endforeach for @{$top['tag']} from line {$top['line']}\n";
                }
            }
        }
    }
}

echo "\nUnclosed directives in overview.blade.php: " . count($stack) . "\n";
foreach ($stack as $s) {
    echo "Line {$s['line']}: @{$s['tag']}\n";
}
