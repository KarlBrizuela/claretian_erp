<?php
$lines = file(__DIR__ . '/../resources/views/production/inventory/overview.blade.php');

// Let's search for closing braces followed by blade tags or unmatched braces around line 2050 to 3000
foreach ($lines as $index => $line) {
    $lineNum = $index + 1;
    if ($lineNum > 2057) {
        // check for suspicious JS syntax like trailing commas, double braces, unclosed quotes, etc.
        if (preg_match('/\}\s*\{\{/', $line) || preg_match('/\}\}\s*\{/', $line)) {
            echo "Line $lineNum: $line";
        }
    }
}
