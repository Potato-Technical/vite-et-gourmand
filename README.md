# Vite & Gourmand

Application web de restauration développée en PHP avec une architecture MVC.

## Prérequis

- Docker
- Docker Compose
- Make

## Installation

Cloner le dépôt puis se placer dans le dossier du projet.

Créer le fichier d’environnement à partir de l’exemple :

```bash
cp .env.example .env
```

Adapter ensuite les variables présentes dans `.env` si nécessaire.

Construire et démarrer les conteneurs :

```bash
docker compose up -d --build
```

Installer les dépendances PHP :

```bash
docker compose exec web composer install
```

Réinitialiser complètement la base de données :

```bash
make db-full
```

Lancer les vérifications du projet :

```bash
make check
```

## Accès à l’application

L’application est disponible à l’adresse suivante :

```text
http://localhost:8080
```

## Arrêt des conteneurs

```bash
docker compose down
```