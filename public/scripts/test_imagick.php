<?php
try {
    // Créer un objet Imagick
    $imagick = new Imagick();
    
    // Chemin vers l'image source
    $sourcePath = __DIR__ . '/logo.jfif';
    
    // Lire l'image source
    $imagick->readImage($sourcePath);
    
    // Convertir en PNG
    $imagick->setImageFormat('png');
    $imagick->writeImage(__DIR__ . '/output_test.png');
    
    // Convertir en JPEG
    $imagick->setImageFormat('jpeg');
    $imagick->writeImage(__DIR__ . '/output_test.jpg');
    
    // Convertir en WebP
    $imagick->setImageFormat('webp');
    $imagick->writeImage(__DIR__ . '/output_test.webp');
    
    // Libérer la mémoire
    $imagick->clear();
    $imagick->destroy();
    
    echo "Conversion réussie !\n";
    echo "Les fichiers suivants ont été créés :\n";
    echo "- output_test.png\n";
    echo "- output_test.jpg\n";
    echo "- output_test.webp\n";
    
} catch (ImagickException $e) {
    echo "Erreur lors de la conversion : " . $e->getMessage();
}
