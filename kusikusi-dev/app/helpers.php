<?php

use Highlight\Highlighter;

if (!function_exists('pretty_json')) {
    function pretty_json($expression) {
        $highlighter = new Highlighter();
        $string = is_array($expression) ? json_encode($expression, JSON_PRETTY_PRINT) : json_encode($expression->toArray(), JSON_PRETTY_PRINT);
        $colored = $highlighter->highlight('json', $string);
        return "<pre><code class=\"hljs json\">$colored->value</code></pre>";
    }
}