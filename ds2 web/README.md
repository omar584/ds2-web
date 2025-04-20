# FocusMap

Une application web interactive pour visualiser et suivre vos objectifs personnels sous forme de carte mentale.

## 🎯 Fonctionnalités

- Création d'objectifs personnels avec visualisation sur carte
- Suivi de progression avec étapes intermédiaires
- Intégration de carte interactive (LeafletJS + OpenStreetMap)
- Système de timeline et de motivation
- Partage d'objectifs (public/privé)
- Système de badges et gamification

## 🛠️ Technologies

- Laravel 10.x
- Bootstrap 5
- LeafletJS
- MySQL
- OpenStreetMap API

## 📋 Prérequis

- PHP >= 8.1
- Composer
- Node.js & NPM
- MySQL

## 🚀 Installation

1. Cloner le repository
```bash
git clone https://github.com/votre-username/focusmap.git
cd focusmap
```

2. Installer les dépendances PHP
```bash
composer install
```

3. Installer les dépendances JavaScript
```bash
npm install
```

4. Configurer l'environnement
```bash
cp .env.example .env
php artisan key:generate
```

5. Configurer la base de données dans le fichier .env

6. Lancer les migrations
```bash
php artisan migrate
```

7. Lancer le serveur de développement
```bash
php artisan serve
npm run dev
```

## 📝 License

Ce projet est sous licence MIT. 