@echo off
echo Installation d'ImageMagick...

:: Créer le dossier d'installation
mkdir "C:\xampp\imagemagick" 2>nul

:: Télécharger ImageMagick
powershell -Command "& {Invoke-WebRequest -Uri 'https://imagemagick.org/archive/binaries/ImageMagick-7.1.1-21-Q16-HDRI-x64-dll.exe' -OutFile 'C:\xampp\imagemagick\imagemagick_setup.exe'}"

:: Installer ImageMagick
echo Installation en cours...
"C:\xampp\imagemagick\imagemagick_setup.exe" /SILENT /DIR="C:\xampp\imagemagick"

echo Installation terminée !
pause
