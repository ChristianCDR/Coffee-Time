# Coffee-Time
Une app pour simuler une machine à café connectée.

docker compose --env-file ../back/.env up --build

symfony server:start

php bin/console messenger:consume async -vv

Droits  du dossier rabbitmq/logs

sudo chown -R 999:999 rabbitmq/logs
sudo chmod -R 750 rabbitmq/logs

999:999 correspond souvent à l’UID/GID de RabbitMQ dans le conteneur.