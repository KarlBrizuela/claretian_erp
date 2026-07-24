<?php
$html = file_get_contents('resources/views/production/inventory/overview.blade.php');

// Simple parser to find parent tags and their classes for a given element ID
function find_parent_hierarchy($html, $targetId) {
    $dom = new DOMDocument();
    // Suppress warnings due to HTML5 tags
    libxml_use_internal_errors(true);
    $dom->loadHTML($html);
    libxml_clear_errors();

    $element = $dom->getElementById($targetId);
    if (!$element) {
        return "Element ID '{$targetId}' not found in DOM!";
    }

    $hierarchy = [];
    $parent = $element->parentNode;
    while ($parent && $parent->nodeName !== '#document') {
        $className = '';
        if ($parent->hasAttribute('class')) {
            $className = '.' . str_replace(' ', '.', $parent->getAttribute('class'));
        }
        $idName = '';
        if ($parent->hasAttribute('id')) {
            $idName = '#' . $parent->getAttribute('id');
        }
        $hierarchy[] = $parent->nodeName . $idName . $className;
        $parent = $parent->parentNode;
    }

    return implode(' < ', $hierarchy);
}

echo "Hierarchy for indexStockModal:\n" . find_parent_hierarchy($html, 'indexStockModal') . "\n\n";
echo "Hierarchy for bundleStockModal:\n" . find_parent_hierarchy($html, 'bundleStockModal') . "\n\n";
echo "Hierarchy for stockManagementModal:\n" . find_parent_hierarchy($html, 'stockManagementModal') . "\n";
