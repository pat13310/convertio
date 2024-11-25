<?php

namespace App\Helpers;

class ImageEnhancer
{
    /**
     * Améliore la qualité d'une image en utilisant ImageMagick
     * 
     * @param string $inputPath Chemin de l'image source
     * @param string $outputPath Chemin de destination pour l'image améliorée
     * @param array $options Options d'amélioration
     * @return bool Succès ou échec de l'opération
     */
    public static function enhanceImage($inputPath, $outputPath, $options = [])
    {
        try {
            // Création d'une instance Imagick
            $image = new \Imagick($inputPath);

            // Amélioration de la netteté
            $image->unsharpMaskImage(0, 0.5, 1.0, 0.05);

            // Optimisation du contraste
            $image->contrastImage(true);

            // Correction automatique des niveaux
            $image->autoLevelImage();

            // Optimisation des couleurs
            $image->modulateImage(100, 110, 100); // Saturation légèrement augmentée

            // Application d'options personnalisées
            if (isset($options['brightness'])) {
                $image->modulateImage($options['brightness'], 100, 100);
            }
            if (isset($options['contrast'])) {
                $image->levelImage($options['contrast'], 1, 65535);
            }
            if (isset($options['sharpen'])) {
                $image->sharpenImage(0, $options['sharpen']);
            }

            // Optimisation de la qualité
            $image->setImageCompressionQuality(92);

            // Suppression des métadonnées pour réduire la taille
            $image->stripImage();

            // Sauvegarde de l'image
            $success = $image->writeImage($outputPath);

            // Libération de la mémoire
            $image->clear();
            $image->destroy();

            return $success;

        } catch (\ImagickException $e) {
            error_log('Erreur lors de l\'amélioration de l\'image: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Exemple d'utilisation avec des paramètres personnalisés
     * 
     * @param string $inputPath Chemin de l'image source
     * @param string $outputPath Chemin de destination
     * @return bool
     */
    public static function enhanceImageWithPreset($inputPath, $outputPath)
    {
        $options = [
            'brightness' => 105,    // Légère augmentation de la luminosité
            'contrast' => 10,       // Amélioration du contraste
            'sharpen' => 1.5       // Niveau de netteté
        ];

        return self::enhanceImage($inputPath, $outputPath, $options);
    }
}
