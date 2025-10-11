# Forum API

## Conception

[Voir le schéma de la base de données sur dbdiagram.io](https://dbdiagram.io/d/Forum-API-68e9428fd2b621e42249a36a)

## Installation et démarrage

1. Copiez les fichiers d'environnement :

```bash
cp .env.example .env
cp backend/.env.example backend/.env
```

2. Lancez les conteneurs :

   ```bash
   docker-compose up --build
   ```

3. Lancer les migrations et les fixtures :
   ```bash
   docker-compose exec app bash
   php bin/console doctrine:migrations:migrate --no-interaction
   php bin/console doctrine:fixtures:load --no-interaction
   ```

## Accès aux URLS

- **Frontend** : [http://localhost:3000](http://localhost:3000)

- **Documentation de l'API** : [http://localhost:8000/docs](http://localhost:8000/docs)

- **Backend API** : [http://localhost:8000](http://localhost:8000)

- **PHPMyAdmin** : [http://localhost:8080/](http://localhost:8080/)
