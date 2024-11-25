<?php

function moveDirectory($source, $dest) {
    if (!is_dir($source)) {
        echo "Source n'est pas un dossier : $source\n";
        return false;
    }

    if (!file_exists($dest)) {
        mkdir($dest, 0777, true);
    }

    $dir = opendir($source);
    while (($file = readdir($dir)) !== false) {
        if ($file != '.' && $file != '..') {
            $srcPath = $source . '/' . $file;
            $destPath = $dest . '/' . $file;
            
            if (is_dir($srcPath)) {
                moveDirectory($srcPath, $destPath);
                rmdir($srcPath);
            } else {
                rename($srcPath, $destPath);
            }
        }
    }
    closedir($dir);
    return true;
}

// Chemins
$publicInPublic = __DIR__ . '/public/uploads';
$correctUploads = __DIR__ . '/uploads';

// Déplacer le contenu
if (is_dir($publicInPublic)) {
    echo "Déplacement des fichiers de $publicInPublic vers $correctUploads\n";
    moveDirectory($publicInPublic, $correctUploads);
    
    // Supprimer l'ancien dossier public vide
    rmdir(__DIR__ . '/public/uploads');
    rmdir(__DIR__ . '/public');
    echo "Structure corrigée avec succès\n";
} else {
    echo "Le dossier public/uploads n'existe pas\n";
}
