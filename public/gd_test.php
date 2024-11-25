<?php
echo "PHP Version: " . PHP_VERSION . "\n";
echo "PHP SAPI: " . PHP_SAPI . "\n";
echo "php.ini location: " . php_ini_loaded_file() . "\n";
echo "GD extension loaded: " . (extension_loaded('gd') ? 'Yes' : 'No') . "\n";
echo "imagecreatetruecolor exists: " . (function_exists('imagecreatetruecolor') ? 'Yes' : 'No') . "\n";
echo "imagecreatefromjpeg exists: " . (function_exists('imagecreatefromjpeg') ? 'Yes' : 'No') . "\n";
echo "imagecreatefrompng exists: " . (function_exists('imagecreatefrompng') ? 'Yes' : 'No') . "\n";
echo "imagecreatefromgif exists: " . (function_exists('imagecreatefromgif') ? 'Yes' : 'No') . "\n";

if (extension_loaded('gd')) {
    $info = gd_info();
    echo "\nGD Info:\n";
    foreach ($info as $key => $value) {
        echo "$key: " . (is_bool($value) ? ($value ? 'Yes' : 'No') : $value) . "\n";
    }
}
