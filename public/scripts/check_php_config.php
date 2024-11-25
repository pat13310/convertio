<?php
echo "Loaded php.ini: " . php_ini_loaded_file() . "\n";
echo "Additional .ini files:\n";
print_r(php_ini_scanned_files());
