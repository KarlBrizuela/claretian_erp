<?php
$content = file_get_contents('app/Http/Controllers/Production/LogisticController.php');
$pattern = '/function\s+(\w+)/i';
preg_match_all($pattern, $content, $matches);
echo "Methods in LogisticController:\n";
foreach ($matches[1] as $method) {
    if (strpos(strtolower($method), 'picklist') !== false || strpos(strtolower($method), 'management') !== false) {
        $pos = strpos($content, $method);
        $line = substr_count(substr($content, 0, $pos), "\n") + 1;
        echo "- Method: {$method} (Line: {$line})\n";
    }
}
