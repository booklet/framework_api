<?php
// Helper for prevent XSS in view when user input data display
function h(string $text = null)
{
    if (empty($text)) {
        return $text;
    }

    return htmlspecialchars($text);
}
