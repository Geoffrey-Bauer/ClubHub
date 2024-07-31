# Gestion des Clubs de Sport

![PHP](https://img.shields.io/badge/PHP-7.4-blue)
![Symfony](https://img.shields.io/badge/Symfony-5.3-black)
![Docker](https://img.shields.io/badge/Docker-20.10.8-blue)
![Node.js](https://img.shields.io/badge/Node.js-14.17.3-green)
![Git](https://img.shields.io/badge/Git-2.31.1-orange)

## Description

Bienvenue dans le projet **Gestion des Clubs de Sport**. Ce backoffice vous permet de gérer efficacement les clubs de sport avec les fonctionnalités suivantes :
- ⚽ **Gestion des matchs**
- 🗓️ **Planification des entraînements**
- 📊 **Classement des équipes**
- 📈 **Statistiques des équipes**
- 🏅 **Statistiques des joueurs**
- 🔍 **Détails des équipes et des joueurs**

Développé dans le cadre de notre formation, ce projet utilise :
- **Symfony** pour le backend et frontend
- **Docker** pour la conteneurisation

## Prérequis

Avant d'installer le projet, assurez-vous que les outils suivants sont installés sur votre machine :
- [Docker](https://www.docker.com/) 🐳
- [Git](https://git-scm.com/) 🐙

## 🚀 Installation

Suivez ces étapes pour installer et démarrer le projet en local :

1. **Build l'application avec Docker**
   ```bash
   docker compose up --build

3. **Installer les composants packages :**
   ```bash
   docker exec -it phpclub composer install

4. **Démarrer le conteneur Node**
   ```
   docker exec -it nodeclub sh

5. **Une fois dans le conteneur, installer les dépendances npm**
   ```bash
   npm install

6. **Démarrer le rendu visuel avec npm**
   ```bash
   npm run watch

7. **Exécuter les migrations de la base de données**
   ```bash
   docker exec -it phpclub php bin/console d:m:m

8. **Charger les fixtures dans la base de données**
   ```bash
   docker exec -it phpclub php bin/console d:f:l

 ## Auteurs
 Ce projet a été réalisé par :
   - Fouad Taibi 👨‍💻
   - Odelin Raffault 👨‍💻
   - Geoffrey Bauer 👨‍💻
