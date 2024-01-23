<?php

namespace App\Controllers;

class Home extends BaseController
{

    public function index(): string
    {
        return view('home/index.php', []);
    }

    public function upload()
    {
        helper(['form', 'url']);
        // récupère les fichiers
        $files = $this->request->getFiles();
        //
        $uploaded_files = [];
        foreach ($files['uploadfiles'] as $file) {
            if ($file->isValid()) {
                $newName = $file->getRandomName();
                $file->move('./uploads', $newName);
                $uploaded_files[] = $newName;
            }
        }
        // Aucun fichiers valides
        if (empty($uploaded_files)) {
            $error = 'Aucun fichier valide sélectionné';
            return json_encode(['error' => $error]);
        }
        // Fichiers téléchargés avec succès
        return json_encode(['success' => 'Fichiers téléchargés avec succès', 'file_names' => $uploaded_files]);
    }
}
