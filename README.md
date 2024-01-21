# Convertisseur d'images

## Qu'est-ce que Express Convert IO?

Express Convert IO est un service web qui offre des fonctionnalités de conversion de formats graphiques. Ce service est capable de convertir des images, des graphiques et d'autres éléments visuels entre un large éventail de formats. Il prend en charge une variété de formats d'entrée et de sortie, y compris les plus populaire comme JPG, PNG, SVG et beaucoup d'autres. Le service est facile à utiliser, rapide et efficace, rendant les conversions de formats graphiques simples et sans tracas. De plus, il est largement accessible, avec une interface utilisateur intuitive et des options de conversion flexibles pour s'adapter à divers besoins et préférences.
## Questions techniques

Pour concevoir Express Convert IO avec Codeigniter 4.4.4, nous utiliserons son architecture MVC (Modèle-Vue-Contrôleur). Codeigniter fournit un environnement de développement rapide et sécurisé, idéal pour développer des applications web comme Express Convert IO. En utilisant Codeigniter, nous pouvons facilement gérer la logique de conversion de formats graphiques, tout en séparant les préoccupations de la logique business, la présentation de l'interface utilisateur et le traitement des données.
**Please** read the user guide for a better explanation of how CI4 works!

## Gestion du dépôt

Nous utilisons les problèmes de GitHub, dans notre dépôt principal, pour suivre les BUGS et pour suivre les lots de travail de DÉVELOPPEMENT approuvés. Nous utilisons notre forum pour fournir du SOUTIEN et pour discuter des DEMANDES DE NOUVELLES FONCTIONNALITÉS.
Ce dépôt est une "distribution", construite par notre script de préparation de la sortie. Les problèmes avec ce dépôt peuvent être soulevés sur notre forum, ou en tant que problèmes dans le dépôt principal.

## Exigences Serveur 

Une version de PHP 8.2 ou supérieure est requise, avec les extensions suivantes installées:

- [intl](http://php.net/manual/en/intl.requirements.php)
- [mbstring](http://php.net/manual/en/mbstring.installation.php)

Php devra activer les extensions GD et Intl pour le bon fonctionnement de l'application.

De plus, assurez-vous que les extensions suivantes sont activées dans votre PHP:

- GD : Un standard pour le traitement d'image avec PHP.
- intl : Une extension pour les fonctionnalités de localisation (i18n) dans PHP.

## Sous Linux (si nécessaire)

### Exécutez le script suivant si les extensions ne sont pas présentes par défaut:

sudo apt-get update
sudo apt-get install php8.2-gd
sudo apt-get install php8.2-intl


### Localiser le fichier :

php -i | grep 'php.ini'

### Modifier le fichier php.ini:

sudo nano /chemin/vers/votre/php.ini
enlever les points virgule pour activer les extensions
extension=gd
extension=intl

### Pour terminer relancer le serveur web:

sudo service apache2 restart

