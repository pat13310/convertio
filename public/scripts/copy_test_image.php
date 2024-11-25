<?php
$source = __DIR__ . '/assets/img/logo.jfif';
$destination = __DIR__ . '/assets/img/test.jpg';

try {
    // Charger l'image avec Imagick
    $image = new Imagick($source);
    
    // Convertir en JPG
    $image->setImageFormat('jpg');
    
    // Sauvegarder
    $image->writeImage($destination);
    
    echo "Image copiée et convertie avec succès : $destination\n";
    
    // Libérer la mémoire
    $image->clear();
    $image->destroy();
    
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
