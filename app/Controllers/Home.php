<?php

namespace App\Controllers;

//use function App\Helpers\getMessageBtn;

class Home extends BaseController
{
    private const DIRECTORIES = [
        'upload' => WRITEPATH . 'uploads',
        'improved' => 'public/uploads/images/improved',
        'convert' => 'public/uploads/images/convert'
    ];

    private const CHUNK_SIZE = 1024 * 1024; // 1MB chunks
    private const MAX_UPLOAD_SIZE = 10485760; // 10MB
    private const ALLOWED_TYPES = [
        'image/jpeg',
        'image/png',
        'image/gif'
    ];
    
    private const FORMATS = [
        'jpg' => ['image/jpeg', IMAGETYPE_JPEG],
        'png' => ['image/png', IMAGETYPE_PNG],
        'gif' => ['image/gif', IMAGETYPE_GIF]
    ];

    public function __construct()
    {
        helper(['form', 'url', 'me_helper']);
        $this->session = \Config\Services::session();
        
        // Créer les dossiers nécessaires s'ils n'existent pas
        foreach (self::DIRECTORIES as $dir) {
            if (!file_exists($dir)) {
                mkdir($dir, 0777, true);
            }
        }
        
        // Vérifier la disponibilité du service d'image
        try {
            $image = \Config\Services::image();
            if (!$image) {
                log_message('error', 'Le service d\'image n\'a pas pu être initialisé');
            } else {
                // Tester le service avec une petite image
                $testImage = WRITEPATH . 'uploads/test.png';
                if (!file_exists($testImage)) {
                    // Créer une petite image de test
                    $im = imagecreatetruecolor(1, 1);
                    imagepng($im, $testImage);
                    imagedestroy($im);
                }
                
                if (file_exists($testImage)) {
                    try {
                        $image->withFile($testImage);
                        log_message('info', 'Service d\'image initialisé avec succès');
                    } catch (\Exception $e) {
                        log_message('error', 'Erreur lors du test du service d\'image: ' . $e->getMessage());
                    }
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'Erreur lors de l\'initialisation du service d\'image: ' . $e->getMessage());
        }
        
        // Forcer le chargement de l'extension GD
        if (!extension_loaded('gd')) {
            // Chemin vers l'extension GD
            $gdPath = PHP_SHLIB_SUFFIX === 'dll' ? 'C:/xampp/php/ext/php_gd.dll' : 'gd.so';
            
            if (file_exists($gdPath)) {
                if (function_exists('dl')) {
                    @dl('gd.' . PHP_SHLIB_SUFFIX);
                }
                
                // Si le chargement dynamique échoue, essayons de charger via ini_set
                if (!extension_loaded('gd')) {
                    ini_set('extension', $gdPath);
                }
            }
            
            // Vérifier si GD est maintenant chargé
            if (!extension_loaded('gd')) {
                log_message('error', 'Impossible de charger l\'extension GD. Chemin tenté : ' . $gdPath);
            } else {
                log_message('info', 'Extension GD chargée avec succès');
            }
        }
    }

    private function updateProgress(int $current, int $total)
    {
        $percentage = ($total > 0) ? round(($current / $total) * 100) : 0;
        $this->session->set('upload_progress', [
            'progress' => $percentage,
            'current' => $current,
            'total' => $total
        ]);
    }

    public function getFileProgress($index)
    {
        $progressFile = WRITEPATH . 'progress/' . $index . '.txt';
        
        if (file_exists($progressFile)) {
            $progress = intval(file_get_contents($progressFile));
            return $this->response->setJSON(['progress' => $progress]);
        }
        
        return $this->response->setJSON(['progress' => 0]);
    }

    public function infos()
    {
        if (!$this->request->is('local')) {
            return redirect()->to('/');
        }
        return phpinfo();
    }

    public function index(): string
    {
        return view('home/index.php', [
            "btn" => getMessageBtn("convert"),
            "action" => "convert",
        ]);
    }

    public function scale(): string
    {
        helper(['form', 'url', 'message']);
        return view('home/scale.php', [
            "btn" => getMessageBtn("scale"),
            "action" => "scale",
        ]);
    }

    public function scale_action()
    {
        try {
            if (!$this->request->isAJAX()) {
                throw new \RuntimeException('Requête invalide');
            }

            // Validation et traitement des fichiers
            $files = $this->request->getFiles();
            $scaleFactor = $this->request->getPost('scale_factor');
            
            $uploadedFiles = $files['files'] ?? [];
            if (empty($uploadedFiles)) {
                throw new \RuntimeException('Aucun fichier n\'a été uploadé');
            }

            // Initialisation du suivi de progression
            $this->session->set('scale_progress', [
                'current' => 0,
                'total' => count($uploadedFiles),
                'percentage' => 0
            ]);

            $this->logScaleOperation('Début du processus de redimensionnement');
            $this->logScaleOperation('Dossiers vérifiés: upload=' . self::DIRECTORIES['upload'] . ', improved=' . self::DIRECTORIES['improved']);
            
            // Validation des fichiers
            $this->logScaleOperation('Début de la validation des fichiers');
            $this->logScaleOperation('Nombre de fichiers reçus: ' . count($uploadedFiles));
            
            $validFiles = [];
            foreach ($uploadedFiles as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $this->logScaleOperation('Validation du fichier: ' . $file->getName());
                    $this->logScaleOperation('Type: ' . $file->getClientMimeType() . ', Taille: ' . $file->getSize() . ' octets');
                    
                    if (!in_array($file->getClientMimeType(), self::ALLOWED_TYPES)) {
                        throw new \RuntimeException('Type de fichier non autorisé: ' . $file->getClientMimeType());
                    }
                    
                    if ($file->getSize() > self::MAX_UPLOAD_SIZE) {
                        throw new \RuntimeException('Fichier trop volumineux: ' . $file->getName());
                    }
                    
                    $this->logScaleOperation('Fichier validé: ' . $file->getName());
                    $validFiles[] = $file;
                }
            }
            
            $this->logScaleOperation('Nombre de fichiers valides: ' . count($validFiles));
            
            if (empty($validFiles)) {
                throw new \RuntimeException('Aucun fichier valide n\'a été trouvé');
            }
            
            $this->logScaleOperation(count($validFiles) . ' fichier(s) valide(s) reçu(s)');
            $this->logScaleOperation('Facteur d\'échelle: ' . $scaleFactor);

            // Traitement des fichiers
            $processed_files = [];
            $current = 0;
            $total = count($validFiles);

            foreach ($validFiles as $file) {
                $this->updateScaleProgress($current, $total);
                $this->logScaleOperation('Progression mise à jour: ' . $current . '/' . $total . ' (' . ($current * 100 / $total) . '%)');

                // Générer un nom unique pour le fichier
                $newName = time() . '_' . bin2hex(random_bytes(10)) . '.' . $file->getExtension();
                $this->logScaleOperation('Traitement du fichier: ' . $file->getName() . ' -> ' . $newName);

                // Déplacer le fichier vers le dossier temporaire
                $file->move(self::DIRECTORIES['upload'], $newName);
                $sourcePath = self::DIRECTORIES['upload'] . '/' . $newName;
                $this->logScaleOperation('Fichier déplacé vers: ' . $sourcePath);

                // Créer le nom du fichier de destination
                $destPath = self::DIRECTORIES['improved'] . '/scaled_' . $newName;
                
                $this->logScaleOperation('Source: ' . $sourcePath);
                $this->logScaleOperation('Destination: ' . $destPath);

                try {
                    // Créer un objet Imagick
                    $imagick = new \Imagick($sourcePath);
                    
                    // Obtenir les dimensions originales
                    $originalWidth = $imagick->getImageWidth();
                    $originalHeight = $imagick->getImageHeight();
                    $this->logScaleOperation('Dimensions originales: ' . $originalWidth . 'x' . $originalHeight);
                    
                    // Calculer les nouvelles dimensions
                    $newWidth = round($originalWidth * $scaleFactor);
                    $newHeight = round($originalHeight * $scaleFactor);
                    $this->logScaleOperation('Nouvelles dimensions: ' . $newWidth . 'x' . $newHeight);
                    
                    // Redimensionner l'image
                    $imagick->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1);
                    
                    // Sauvegarder l'image redimensionnée
                    $imagick->writeImage($destPath);
                    $imagick->clear();
                    $imagick->destroy();
                    
                    $this->logScaleOperation('Image redimensionnée avec succès');
                    
                    // Ajouter le fichier à la liste des fichiers traités
                    $processed_files[] = [
                        'name' => $file->getName(),
                        'path' => $destPath
                    ];
                    
                } catch (\Exception $e) {
                    throw new \RuntimeException('Erreur lors du redimensionnement: ' . $e->getMessage());
                }

                // Supprimer le fichier temporaire
                if (file_exists($sourcePath)) {
                    unlink($sourcePath);
                    $this->logScaleOperation('Fichier temporaire supprimé: ' . $sourcePath);
                }

                $current++;
                $this->updateScaleProgress($current, $total);
                $this->logScaleOperation('Progression mise à jour: ' . $current . '/' . $total . ' (' . ($current * 100 / $total) . '%)');
            }

            $this->logScaleOperation('Traitement terminé avec succès. ' . count($processed_files) . ' fichier(s) traité(s)');

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Images redimensionnées avec succès',
                'files' => $processed_files
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erreur lors du redimensionnement: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function updateScaleProgress(int $current, int $total): void
    {
        $percentage = ($total > 0) ? round(($current / $total) * 100) : 0;
        // Forcer à 100% si c'est le dernier fichier
        if ($current >= $total) {
            $percentage = 100;
        }
        $this->session->set('scale_progress', [
            'percentage' => $percentage,
            'current' => $current,
            'total' => $total
        ]);
        $this->logScaleOperation("Progression mise à jour: $current/$total ($percentage%)");
    }

    public function getScaleProgress()
    {
        $progress = $this->session->get('scale_progress');
        if ($progress) {
            // Forcer à 100% si le traitement est terminé
            if ($progress['current'] >= $progress['total']) {
                $progress['percentage'] = 100;
            }
            return $this->response->setJSON($progress);
        }
        return $this->response->setJSON(['percentage' => 0, 'current' => 0, 'total' => 0]);
    }

    private function logScaleOperation($message, $type = 'info'): void
    {
        $logFile = WRITEPATH . 'logs/scale_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $logMessage = "[$timestamp] [$type] $message" . PHP_EOL;
        file_put_contents($logFile, $logMessage, FILE_APPEND);
    }

    public function action()
    {
        // Réinitialiser la progression
        $this->session->remove('upload_progress');
        
        // Augmenter les limites de mémoire et de temps d'exécution pour les gros fichiers
        ini_set('memory_limit', '256M');
        set_time_limit(300); // 5 minutes

        $action = $this->request->getVar("action");
        try {
            $message = $this->move($action);
            return $this->response->setJSON([
                "success" => true,
                "message" => $message,
                "btn" => getMessageBtn($action),
                "action" => $action,
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                "success" => false,
                "error" => $e->getMessage(),
                "btn" => getMessageBtn($action),
                "action" => $action,
            ]);
        }
    }

    private function createDirectoryIfNotExists(string $dir): void
    {
        if (!is_dir($dir)) {
            // Créer tous les dossiers parents si nécessaire
            if (!mkdir($dir, 0777, true)) {
                log_message('error', 'Impossible de créer le répertoire: ' . $dir);
                throw new \RuntimeException('Impossible de créer le répertoire: ' . $dir);
            }
            // S'assurer que les permissions sont correctes
            chmod($dir, 0777);
            log_message('info', 'Répertoire créé avec succès: ' . $dir);
        }
    }

    private function validateFiles($uploadedFiles)
    {
        $validFiles = [];
        $this->logScaleOperation("Début de la validation des fichiers");
        
        if (empty($uploadedFiles)) {
            $this->logScaleOperation("Aucun fichier reçu", 'error');
            return $validFiles;
        }
        
        if (!isset($uploadedFiles['files'])) {
            $this->logScaleOperation("Clé 'files' manquante dans les fichiers uploadés", 'error');
            return $validFiles;
        }
        
        $files = $uploadedFiles['files'];
        $this->logScaleOperation("Nombre de fichiers reçus: " . count($files));
        
        foreach ($files as $file) {
            if ($file->isValid() && !$file->hasMoved()) {
                $type = $file->getClientMimeType();
                $size = $file->getSize();
                
                $this->logScaleOperation("Validation du fichier: " . $file->getName());
                $this->logScaleOperation("Type: $type, Taille: $size octets");
                
                if (!in_array($type, self::ALLOWED_TYPES)) {
                    $this->logScaleOperation("Type de fichier non autorisé: $type", 'error');
                    continue;
                }
                
                if ($size > self::MAX_UPLOAD_SIZE) {
                    $this->logScaleOperation("Fichier trop volumineux: " . ($size / 1024 / 1024) . "MB", 'error');
                    continue;
                }
                
                $validFiles[] = $file;
                $this->logScaleOperation("Fichier validé: " . $file->getName());
            } else {
                if (!$file->isValid()) {
                    $this->logScaleOperation("Fichier invalide: " . $file->getName() . ", Erreur: " . $file->getError(), 'error');
                }
                if ($file->hasMoved()) {
                    $this->logScaleOperation("Fichier déjà déplacé: " . $file->getName(), 'error');
                }
            }
        }
        
        $this->logScaleOperation("Nombre de fichiers valides: " . count($validFiles));
        return $validFiles;
    }

    private function moveFileInChunks($file, $targetPath, $totalSize, $currentTotalProgress): int
    {
        $source = fopen($file->getTempName(), 'rb');
        $target = fopen($targetPath, 'wb');

        if (!$source || !$target) {
            throw new \RuntimeException("Erreur lors de l'ouverture des fichiers");
        }

        $currentFileProgress = 0;
        $fileSize = $file->getSize();

        // Transfert par morceaux pour optimiser la mémoire
        while (!feof($source)) {
            $chunk = fread($source, self::CHUNK_SIZE);
            $chunkSize = strlen($chunk);
            fwrite($target, $chunk);

            $currentFileProgress += $chunkSize;
            $this->updateProgress(
                $currentTotalProgress + $currentFileProgress,
                $totalSize
            );
        }

        // Fermeture des fichiers
        fclose($source);
        fclose($target);

        return $currentFileProgress;
    }

    private function upscaleImage($imagick, $scale = 2)
    {
        try {
            // Obtenir les dimensions actuelles
            $width = $imagick->getImageWidth();
            $height = $imagick->getImageHeight();
            
            // Calculer les nouvelles dimensions
            $newWidth = $width * $scale;
            $newHeight = $height * $scale;
            
            // Appliquer l'algorithme Lanczos pour un meilleur upscaling
            $imagick->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1);
            
            // Appliquer un léger sharpening pour améliorer la netteté
            $imagick->unsharpMaskImage(0, 0.5, 1, 0.05);
            
            return true;
        } catch (\Exception $e) {
            log_message('error', 'Erreur lors de l\'upscaling : ' . $e->getMessage());
            return false;
        }
    }

    private function convertImage($file, $format, $options = [])
    {
        try {
            $imagick = new \Imagick($file['tmp_name']);
            
            // Appliquer le redimensionnement si un facteur d'échelle est spécifié
            if (!empty($options['scale_factor'])) {
                $scale = floatval($options['scale_factor']);
                if ($scale != 1) {
                    // Obtenir les dimensions actuelles
                    $width = $imagick->getImageWidth();
                    $height = $imagick->getImageHeight();
                    
                    // Calculer les nouvelles dimensions
                    $newWidth = round($width * $scale);
                    $newHeight = round($height * $scale);
                    
                    // Redimensionner l'image
                    $imagick->resizeImage($newWidth, $newHeight, \Imagick::FILTER_LANCZOS, 1);
                }
            }
            // Appliquer l'upscaling si demandé
            else if (!empty($options['upscale'])) {
                $scale = floatval($options['upscale']);
                if ($scale > 1) {
                    $this->upscaleImage($imagick, $scale);
                }
            }
            
            // Définir le format de sortie
            $imagick->setImageFormat($format);
            
            // Optimiser la qualité selon le format
            switch ($format) {
                case 'jpg':
                case 'jpeg':
                    $imagick->setImageCompression(\Imagick::COMPRESSION_JPEG);
                    $imagick->setImageCompressionQuality(85);
                    break;
                    
                case 'png':
                    $imagick->setImageCompression(\Imagick::COMPRESSION_ZIP);
                    $imagick->setOption('png:compression-level', 9);
                    break;
                    
                case 'webp':
                    $imagick->setImageCompressionQuality(85);
                    break;
            }
            
            // Générer un nom de fichier unique
            $newFileName = uniqid() . '_' . bin2hex(random_bytes(8)) . '.' . strtolower($format);
            $outputPath = self::DIRECTORIES['convert'] . '/' . $newFileName;
            
            // Sauvegarder l'image
            $imagick->writeImage($outputPath);
            
            // Libérer la mémoire
            $imagick->clear();
            $imagick->destroy();
            
            return [
                'success' => true,
                'name' => $newFileName,
                'original' => $file['name'],
                'size' => filesize($outputPath),
                'type' => 'image/' . $format
            ];
            
        } catch (\Exception $e) {
            log_message('error', 'Erreur de conversion : ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function move(string $action): array
    {
        try {
            if (!isset(self::DIRECTORIES[$action])) {
                throw new \InvalidArgumentException("Action non valide");
            }

            // Création des répertoires nécessaires
            $this->createDirectoryIfNotExists(UPLOAD_DIR);
            $this->createDirectoryIfNotExists(self::DIRECTORIES[$action]);

            // Validation et traitement des fichiers
            $files = $this->request->getFiles();
            $formats = $this->request->getPost('formats');
            $upscale = $this->request->getPost('upscale');
            $scaleFactor = $this->request->getPost('scale_factor');
            
            $uploadedFiles = $files['uploadfiles'] ?? [];
            if (empty($uploadedFiles)) {
                throw new \RuntimeException('Aucun fichier n\'a été uploadé');
            }

            // Traitement des fichiers
            $processed_files = [];
            $errors = [];

            foreach ($uploadedFiles as $index => $file) {
                try {
                    if (!$file->isValid()) {
                        throw new \RuntimeException($file->getErrorString());
                    }

                    $originalName = $file->getName();
                    $format = $formats[$index] ?? 'jpg';
                    
                    // Options de conversion
                    $options = [];
                    if ($upscale) {
                        $options['upscale'] = $upscale;
                    }
                    if ($scaleFactor) {
                        $options['scale_factor'] = $scaleFactor;
                    }

                    // Déplacer le fichier vers le répertoire temporaire
                    $tempName = $file->getRandomName();
                    $file->move(UPLOAD_DIR, $tempName);

                    // Préparer le fichier pour la conversion
                    $fileData = [
                        'name' => $originalName,
                        'tmp_name' => UPLOAD_DIR . '/' . $tempName
                    ];

                    // Convertir l'image
                    $result = $this->convertImage($fileData, $format, $options);

                    if ($result['success']) {
                        $processed_files[] = $result;
                    } else {
                        $errors[] = "Erreur lors du traitement de {$originalName}: {$result['error']}";
                    }

                    // Supprimer le fichier temporaire
                    unlink(UPLOAD_DIR . '/' . $tempName);

                } catch (\Exception $e) {
                    $errors[] = "Erreur lors du traitement de {$originalName}: " . $e->getMessage();
                }
            }

            if (empty($processed_files)) {
                if (!empty($errors)) {
                    throw new \RuntimeException(implode("\n", $errors));
                }
                throw new \RuntimeException('Aucun fichier n\'a pu être traité');
            }

            return [
                'files' => $processed_files,
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            throw new \RuntimeException($e->getMessage());
        }
    }

    public function convertAction()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'message' => 'Invalid request']);
        }

        $file = $this->request->getFile('uploadfile');
        $format = $this->request->getPost('format');
        $index = $this->request->getPost('index');

        if (!$file->isValid() || $file->hasMoved()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Invalid file']);
        }

        // Créer les répertoires s'ils n'existent pas
        $uploadPath = FCPATH . 'uploads/images/converted';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }

        try {
            // Générer un nom unique pour le fichier converti
            $newFileName = pathinfo($file->getName(), PATHINFO_FILENAME) . '_' . uniqid() . '.' . $format;
            $targetPath = $uploadPath . '/' . $newFileName;

            // Déplacer le fichier temporaire
            $file->move($uploadPath, $newFileName);

            // Convertir l'image avec ImageMagick
            $image = new \Imagick($targetPath);
            $image->setImageFormat($format);
            
            // Optimiser la qualité en fonction du format
            switch($format) {
                case 'jpg':
                case 'jpeg':
                    $image->setImageCompressionQuality(85);
                    break;
                case 'png':
                    $image->setImageCompressionQuality(95);
                    break;
                case 'webp':
                    $image->setImageCompressionQuality(80);
                    break;
            }

            // Sauvegarder l'image convertie
            $image->writeImage($targetPath);
            $image->clear();
            $image->destroy();

            // Mettre à jour le fichier de progression
            $this->updateFileProgress($index, 100);

            // Construire l'URL de téléchargement
            $downloadUrl = base_url("uploads/images/converted/{$newFileName}");

            return $this->response->setJSON([
                'success' => true,
                'download_url' => $downloadUrl,
                'filename' => $newFileName
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Conversion error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error during conversion: ' . $e->getMessage()
            ]);
        }
    }

    private function updateFileProgress($index, $progress)
    {
        $progressDir = WRITEPATH . 'progress';
        if (!is_dir($progressDir)) {
            mkdir($progressDir, 0777, true);
        }
        
        $progressFile = $progressDir . '/' . $index . '.txt';
        file_put_contents($progressFile, $progress);
    }

    public function improved()
    {
        $improvedDir = 'uploads/images/improved';
        $files = [];
        
        if (is_dir($improvedDir)) {
            $items = scandir($improvedDir);
            foreach ($items as $item) {
                if ($item != '.' && $item != '..') {
                    $filePath = $improvedDir . '/' . $item;
                    if (is_file($filePath)) {
                        $files[] = [
                            'name' => $item,
                            'size' => filesize($filePath),
                            'date' => date('Y-m-d H:i:s', filemtime($filePath)),
                            'url' => 'uploads/images/improved/' . $item
                        ];
                    }
                }
            }
        }
        
        return view('home/improved', [
            'title' => 'Images Améliorées',
            'files' => $files
        ]);
    }
}
