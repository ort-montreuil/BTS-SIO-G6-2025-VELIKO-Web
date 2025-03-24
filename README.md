# Projet Veliko

Veliko est une application web de gestion de stations de location de vélos électriques et mécaniques. Ce projet est développé en utilisant Symfony et permet de récupérer, afficher et interagir avec des stations de vélos à travers une interface web moderne.

## 🚀 Installation

Pour installer et configurer le projet, suivez les étapes ci-dessous.

### Étape 1 : Cloner le projet

Commencez par cloner le repository sur votre machine locale :

```bash
git clone git@github.com:ort-montreuil/BTS-SIO-G6-2025-VELIKO-Web.git
```

### Étape 2 : Installation des dépendances

Installez les dépendances du projet à l’aide de Composer :
```bash
composer install
````
### Étape 3 : Configuration du fichier .env

Créez un fichier .env à la racine du projet en copiant le fichier .env.example et en le renommant en .env. Ensuite, modifiez la ligne 3 pour y entrer vos identifiants de base de données (id et mot de passe) :
```plaintext
DATABASE_URL="mysql://<votre-id>:<votre-mot-de-passe>@127.0.0.1:3306/veliko_db"
````

### Étape 4 : Création de la base de données

Lancez la création de la base de données avec Docker :
```bash
docker compose up -d
````

### Étape 5 : Migration de la base de données

Appliquez les migrations pour mettre à jour la structure de la base de données :
```bash
symfony console doctrine:migrations:migrate
````

### Étape 6 : Importation des users dans la base de données

Pour ajouter des users dans votre base de données, utilisez la commande suivante :
```bash
symfony console doctrine:fixtures:load
````

### Étape 7 : Importation des stations dans la base de données

Pour ajouter les stations dans votre base de données, utilisez la commande suivante :
```bash
symfony console app:fetch-stations
````

### Étape 8 : Lancer le serveur

Enfin, lancez le serveur Symfony pour démarrer l’application :
```bash
symfony serve:start
````

### Mode production ###

Dans le .env 

````
APP_ENV=prod
APP_DEBUG=0
````

```` bash
Composer install
````
```` bash
php bin/console d:m:m
````