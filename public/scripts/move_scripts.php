<?php

// Création du dossier scripts s'il n'existe pas
$scriptsDir = __DIR__ . '/scripts';
if (!file_exists($scriptsDir)) {
    mkdir($scriptsDir, 0777, true);
    echo "Dossier scripts créé\n";
}

// Liste des scripts à déplacer
$scripts = [
    'check_php_config.php',
    'copy_test_image.php',
    'create_test_dir.php',
    'fix_structure.php',
    'test_enhance.php',
    'test_imagick.php',
    'gd_test.php',
    'info.php',
    'phpinfo.php',
    'move_scripts.php'  // Ce script sera déplacé en dernier
];

// Déplacement des fichiers
foreach ($scripts as $script) {
    $source = __DIR__ . '/' . $script;
    $destination = $scriptsDir . '/' . $script;
    
    if (file_exists($source)) {
        rename($source, $destination);
        echo "Déplacé: $script\n";
    }
}

echo "\nTous les scripts ont été déplacés dans le dossier 'scripts'";
