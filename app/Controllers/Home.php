<?php

namespace App\Controllers;

//use function App\Helpers\getMessageBtn;

class Home extends BaseController
{
    public function infos(){
        return phpinfo();
    }
    public function index(): string
    {
        helper('me_helper');
        return view('home/index.php', [
            "btn" => getMessageBtn("convert"),
            "action" => "convert",
        ]);
    }

    public function action()
    {
        helper(['form', 'url', 'me_helper']);

        $action = $this->request->getVar("action");
        $message = $this->move($action);
        // Fichiers téléchargés avec succès
        return view('home/index.php', [
            "message" => $message,
            "btn" => getMessageBtn($action),
            "action" => $action,
        ]);
    }

    function move($action)
    {

        $uploaded_files = [];
        // récupère les fichiers
        $files = $this->request->getFiles();
        $message = "";

        if (!is_dir(UPLOAD_DIR)) {
            mkdir(UPLOAD_DIR, 0777, true);
        }
        foreach ($files['uploadfiles'] as $file) {
            if ($file->isValid()) {
                $newName = $file->getRandomName();
                $file->move(UPLOAD_DIR, $newName);
                if ($action == "convert") {
                    if (!is_dir(CONVERT_DIR)) {
                        mkdir(CONVERT_DIR, 0777, true);
                    }
                } elseif ($action == "improve") {
                    if (!is_dir(IMPROVE_DIR)) {
                        mkdir(IMPROVE_DIR, 0777, true);
                    }
                } elseif ($action == "erase") {
                    if (!is_dir(BACKGROUND_DIR)) {
                        mkdir(BACKGROUND_DIR, 0777, true);
                    }
                }
                $uploaded_files[] = $newName;
            }
        }
        // Aucun fichiers valides
        if (empty($uploaded_files)) {
            $error = 'Aucun fichier valide';
            return ['error' => $error];
        }
        return $message;
    }
}
