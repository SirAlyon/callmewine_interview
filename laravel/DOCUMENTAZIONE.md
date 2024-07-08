# Guida per Avviare l'Applicazione Laravel con Docker

## Prerequisiti

- Docker

## Comandi per Avviare l'Applicazione

### 1. Costruire e Avviare i Container Docker

Nella radice del tuo progetto, eseguire i seguenti comandi Docker:

```
docker compose build
```
```
docker compose up -d
```

### Copiare il file .env.example in .env 

Duplicare il file .env.example in .env, verificare che le seguenti variabili siano correttamente settate:

```
SESSION_DRIVER=redis
REDIS_CLIENT=predis
REDIS_HOST=redis
REDIS_PASSWORD=null
REDIS_PORT=6379
CMC_API_KEY=c3f841e7-0fff-4d8e-985f-248d29747571

```

### Installazione delle Dipendenze PHP

Una volta avviati i cointainer, procedere ad installare le varie dipendenze.
Accedere al container PHP: (in alternativa accedere da Docker Desktop)

```
docker exec -it php sh
```

ed eseguire i comandi necessari per le dipendenze e l'avvio di Laravel:

```
composer install
npm install
php artisan key:generate

//opzionale il db in questo caso non è utilizzato
php artisan migrate 

chown -R www-data:www-data /var/www/storage
chown -R www-data:www-data /var/www/bootstrap/cache

//opzionale
chmod -R 777 storage bootstrap/cache 

npm run dev
```

### Accesso all'Applicazione

E' possibile accedere all'applicazione all'indirizzo: http://localhost

(testato su MacOS e Ubuntu)


