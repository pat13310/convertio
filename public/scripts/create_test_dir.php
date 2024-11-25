<?php
$dir = __DIR__ . '/assets/img';
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
    echo "Dossier créé : $dir\n";
} else {
    echo "Le dossier existe déjà : $dir\n";
}
