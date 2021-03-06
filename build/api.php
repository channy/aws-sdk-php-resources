<?php
/*
 * Loads the JSON models from the provided directory, converts them to .php
 * files, does some pre-processing, and places them into the src/models.
 */

if (!isset($argv[1])) {
    die('A source path was not provided in argument 1');
}
$dir = $argv[1];

if (is_file($dir)) {
    copyJson($dir);
} elseif (is_dir($dir)) {
    foreach (scandir($dir) as $file) {
        copyJson($dir . '/' . $file);
    }
} else {
    die('Invalid file/directory');
}

function copyJson($file) {
    if (!strpos($file, 'resources.json')) {
        return;
    }
    $phpFile = __DIR__ . '/../src/models/'
        . str_replace('.json', '.php', basename($file));
    $json = json_decode(file_get_contents($file), true);
    $script = "<?php return " . var_export($json, true) . ";\n";
    // Convert "array()" to "[]"
    $script = str_replace('array (', '[', $script);
    $script = str_replace(')', ']', $script);
    // Removing trailing whitespace
    $script = preg_replace('/\s+$/m', '', $script);
    // Move arrays to the same line
    $script = preg_replace('/=>\s*\n\s*\[/', '=> [', $script);
    // Get rid of numbered array indexes
    $script = preg_replace('/(\s*)(\d+ => )/', '$1', $script);
    // Adding trailing new line
    $script .= "\n";
    echo "Creating {$phpFile}\n";
    file_put_contents($phpFile, $script);
}
