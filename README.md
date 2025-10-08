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

> Ce projet sert de **template pour démarrer un nouveau projet API sous Symfony**, incluant la gestion utilisateur **from scratch**.  
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
$repoUrl = "https://github.com/fabienlamotte487/user-bundle.git"
$tempDir = New-Item -ItemType Directory -Path ([System.IO.Path]::GetTempPath() + [System.IO.Path]::GetRandomFileName())
git clone --depth 1 $repoUrl $tempDir
Get-ChildItem -Path $tempDir -Force | ForEach-Object { Move-Item $_.FullName . -Force }
Remove-Item -Recurse -Force $tempDir
composer install
php bin/console doctrine:database:create
php bin/console make:migration
php bin/console doctrine:migrations:migrate
php bin/console doctrine:database:create --env=test
php bin/console doctrine:migrations:migrate --env=test
symfony serve
php bin/phpunit --testdox
