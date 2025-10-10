<p align="center">
  <img src="docs/banner.png" alt="Nouveau Projet API Symfony" width="600"/>
</p>

# 🚀 Nouveau Projet API Symfony avec Gestion Utilisateur

<p align="center">
  <img src="https://img.shields.io/badge/Symfony-7.3.x-blue?logo=symfony" alt="Symfony Version"/>
  <img src="https://img.shields.io/badge/PHP-8.x-lightgrey?logo=php" alt="PHP Version"/>
  <img src="https://img.shields.io/badge/License-MIT-green" alt="License"/>
  <img src="https://img.shields.io/badge/build-passing-brightgreen" alt="Build Status"/>
</p>

> Ce projet sert de **template pour démarrer un nouveau projet API sous Symfony**, incluant la gestion utilisateur.  
> Suivez ces consignes pour monter votre projet rapidement et proprement.

---

## 📋 Prérequis

- PHP >= 8.x  
- Composer  
- Git  
- Symfony CLI  

---

## 🛠️ Installation d’un nouveau projet Symfony

```bash
symfony new my_project_directory --version="7.3.x"
cd my_project_directory
```

## 🛠️ Intégration du socle de gestion de vie utilisateur
### Clonage du repo (Windows powershell)
```bash
# URL du bundle
$repoUrl = "https://github.com/fabienlamotte487/user-bundle.git"

# Dossier temporaire
$tempDir = Join-Path -Path $env:TEMP -ChildPath ([System.IO.Path]::GetRandomFileName())
New-Item -ItemType Directory -Path $tempDir | Out-Null

# Cloner le repo bundle temporairement
git clone --depth 1 $repoUrl $tempDir

# Déplacer tous les fichiers du bundle vers le projet, sauf le .git
Get-ChildItem -Path $tempDir -Force | Where-Object { $_.Name -ne ".git" } | ForEach-Object { 
    Move-Item $_.FullName . -Force 
}

# Supprimer le dossier temporaire
Remove-Item -Recurse -Force $tempDir
```

### Installation des dépendances

```bash
rm composer.lock
composer install
```
### Génération jwt-keypair (nécessaire pour les connexion par api)

```bash
php bin/console lexik:jwt:generate-keypair
```
### Création des bases de données (principales et de tests)
Je recommande la configuration de test pour valider que tout fonctionne

#### Contribution des variables d'environnement

```bash
MAILER_DSN=smtp://mon-email@mondomaine.fr:password@smtp.mail.ovh.net:587
DATABASE_URL="mysql://root:@127.0.0.1:3306/mabasededonnee"
```
#### Lancement des commandes de créations de base de données
```bash
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
```

### Lancement du serveur

```bash
symfony serve
```
### Lancement des tests
```bash
php bin/phpunit --testdox
```

## 🚀 Si tout les tests sont bons, bravo, vous êtes prêts à développer !!
