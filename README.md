# Coffee-Time
Une app pour simuler une machine à café connectée.

docker compose --env-file back/.env up --build

symfony server:start

php bin/console messenger:consume async -vv
