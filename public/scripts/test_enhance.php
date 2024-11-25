<?php

require_once __DIR__ . '/../app/Helpers/ImageEnhancer.php';

use App\Helpers\ImageEnhancer;

// Vérification de l'installation d'ImageMagick
if (!extension_loaded('imagick')) {
    die("L'extension ImageMagick n'est pas installée.");
}

// Informations sur la version d'ImageMagick
$imagick = new Imagick();
echo "Version ImageMagick : " . $imagick->getVersion()['versionString'] . "\n";

// Chemin des images
$sourceImage = __DIR__ . '/assets/img/test.jpg'; // Assurez-vous d'avoir une image test.jpg dans ce dossier
$outputImage = __DIR__ . '/assets/img/test_enhanced.jpg';

// Test avec les paramètres par défaut
echo "\nTest 1: Amélioration avec paramètres par défaut\n";
try {
    $start = microtime(true);
    $result = ImageEnhancer::enhanceImage($sourceImage, $outputImage);
    $time = number_format(microtime(true) - $start, 2);
    
    if ($result) {
        echo "✅ Image améliorée avec succès en {$time} secondes\n";
        echo "Image source : " . filesize($sourceImage) . " octets\n";
        echo "Image améliorée : " . filesize($outputImage) . " octets\n";
    } else {
        echo "❌ Échec de l'amélioration de l'image\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

// Test avec des paramètres personnalisés
echo "\nTest 2: Amélioration avec paramètres personnalisés\n";
$outputImage2 = __DIR__ . '/assets/img/test_enhanced_custom.jpg';
$options = [
    'brightness' => 105,
    'contrast' => 15,
    'sharpen' => 2.0
];

try {
    $start = microtime(true);
    $result = ImageEnhancer::enhanceImage($sourceImage, $outputImage2, $options);
    $time = number_format(microtime(true) - $start, 2);
    
    if ($result) {
        echo "✅ Image améliorée avec succès en {$time} secondes\n";
        echo "Image source : " . filesize($sourceImage) . " octets\n";
        echo "Image améliorée : " . filesize($outputImage2) . " octets\n";
    } else {
        echo "❌ Échec de l'amélioration de l'image\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

// Test avec preset
echo "\nTest 3: Amélioration avec preset\n";
$outputImage3 = __DIR__ . '/assets/img/test_enhanced_preset.jpg';

try {
    $start = microtime(true);
    $result = ImageEnhancer::enhanceImageWithPreset($sourceImage, $outputImage3);
    $time = number_format(microtime(true) - $start, 2);
    
    if ($result) {
        echo "✅ Image améliorée avec succès en {$time} secondes\n";
        echo "Image source : " . filesize($sourceImage) . " octets\n";
        echo "Image améliorée : " . filesize($outputImage3) . " octets\n";
    } else {
        echo "❌ Échec de l'amélioration de l'image\n";
    }
} catch (Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}

echo "\nTests terminés. Vérifiez les images générées dans le dossier assets/img/\n";
