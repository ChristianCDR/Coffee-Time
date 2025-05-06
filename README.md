**Documentation : Coffee-Time**

**Coffee-Time** est une application qui simule une machine à café connectée. Ce projet permet d'interagir avec une machine à café virtuelle via une interface web. 
Pour ça, on utilise des technologies modernes comme NextJS, Docker, Symfony et RabbitMQ pour la gestion des messages.

**Prérequis**

Avant de commencer, assure-toi d'avoir installé les outils suivants sur ta machine :

    Git : Pour cloner le repository.

    Docker et Docker Compose : Pour faciliter la gestion des conteneurs.

    Node.js et npm : Pour la gestion des dépendances et l'exécution de l'application frontend.
    
    PHP et Symfony : Pour le développement et l'exécution du backend de l'application.

**Étapes pour démarrer**

    1 - Cloner le projet depuis le dépôt GitHub :
        
        git clone https://github.com/ChristianCDR/Coffee-Time.git

    2 - Configurer l'environnement de l'application :

        Une fois le projet cloné, copie le fichier .env dans le dossier back

    3 - Installer les dépendances:
        # Backend
        
        Navigue dans le répertoire back, puis installe les dépendances nécessaires en exécutant :

        composer install 

        # Frontend

        Navigue dans le répertoire front, puis installe les dépendances nécessaires en exécutant :

        npm install    

    4 - Ajouter les clés JWT dans Caddyfile

        Accéder à docker/mercure/Caddyfile et ajouter le secret JWT:

        publisher_jwt "!ChangeMe!"
        subscriber_jwt "!ChangeMe!"

    5 - Lancer les conteneurs Docker :

        Une fois les étapes précédentes terminées, tu peux maintenant démarrer l'ensemble des services à l'aide de Docker Compose. Utilise cette commande dans le répertoire /docker :

        docker compose -f docker-compose.yml --env-file ../back/.env up --build

    Cette commande va :

        Construire les images Docker nécessaires.

        Démarrer les conteneurs avec les variables d'environnement définies dans le fichier .env.

**Accéder à l'application**

    Une fois que tous les services sont lancés, tu peux accéder à l'application depuis ton navigateur à l'adresse suivante :

    http://localhost:3000

**Accéder à la documentation des routes**

     Une fois que tous les services sont lancés, tu peux accéder à la documentation des routes depuis ton navigateur à l'adresse suivante :

    http://localhost:8001/api/doc

**Résolution des problèmes**

    Problèmes de permissions avec RabbitMQ : Si tu rencontres des erreurs liées aux permissions du dossier rabbitmq/logs
    
        Configurer les droits du dossier RabbitMQ :

        Le dossier rabbitmq/logs doit avoir les bons droits pour être utilisé correctement par RabbitMQ dans le conteneur. Utilise les commandes suivantes pour attribuer les permissions nécessaires :

        # 999:999 correspond à l'UID/GID de RabbitMQ dans le conteneur
        sudo chown -R 999:999 rabbitmq/logs
        sudo chmod -R 750 rabbitmq/logs

    Dépendances manquantes : Si des erreurs apparaissent lors de l'exécution de npm install, vérifie que ton fichier package.json est correct et que tu as une version de Node.js compatible.

Bonne utilisation de Coffee-Time et que ton café soit toujours parfait ! ☕🚀
