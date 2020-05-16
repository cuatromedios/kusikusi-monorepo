<?php
/**
 * Based on https://stackoverflow.com/questions/27381520/php-built-in-server-and-htaccess-mod-rewrites
 *
 * Use this php router if not working with apache or other web server that allows it
 */

chdir(__DIR__);
$path = ltrim($_SERVER["REQUEST_URI"], '/');
$foundQueryString = strpos($path, '?');
if ($foundQueryString !== FALSE) {
    $path = substr($path, 0, $foundQueryString - strlen($path));
}
$filePath = realpath($path);
if ($filePath && is_dir($filePath)){
    // attempt to find an index file
    foreach (['index.php', 'index.html'] as $indexFile){
        if ($filePath = realpath($filePath . DIRECTORY_SEPARATOR . $indexFile)){
            break;
        }
    }
}
if ($filePath && is_file($filePath)) {
    // 1. check that file is not outside of this directory for security
    // 2. check for circular reference to router.php
    // 3. don't serve dotfiles
    if (strpos($filePath, __DIR__ . DIRECTORY_SEPARATOR) === 0 &&
        $filePath != __DIR__ . DIRECTORY_SEPARATOR . 'phprouter.php' &&
        substr(basename($filePath), 0, 1) != '.'
    ) {
        if (strtolower(substr($filePath, -4)) == '.php') {
            // php file; serve through interpreter
            include $filePath;
        } else {
            // asset file; serve from filesystem
            return false;
        }
    } else {
        // disallowed file
        header("HTTP/1.1 404 Not Found");
        echo "404 Not Found r";
    }
} else {
    // rewrite to our index file
    if (substr($path, 0, 3) === 'cms') {
        readfile(__DIR__ . DIRECTORY_SEPARATOR . 'cms/index.html');
    } else {
        include __DIR__ . DIRECTORY_SEPARATOR . 'index.php';
    }
}
