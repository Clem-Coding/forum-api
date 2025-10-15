# Forum API

Application web de forum avec authentification. Les utilisateurs connectés peuvent créer des topics et y ajouter des commentaires.

## Stack technique

## Architecture

L'API a été développée avec Symfony 7 et API Platform. Elle suit les principes REST et fournit une documentation interactive via Swagger UI.

## Technologies utilisées Backend

- Symfony 7.3.4
- API Platform 4.2
- PHP 8.4 + Doctrine ORM
- MariaDb
- Authentification JWT

### Technologies utilisées Front

- Vue.js 3.5 (Composition API)
- TypeScript
- Vue Router
- Axios (requêtes HTTP)
- ESLint

## Conception

[Voir le schéma de la base de données sur dbdiagram.io](https://dbdiagram.io/d/Forum-API-68e9428fd2b621e42249a36a)

## Installation et démarrage

1. Cloner le dépôt

2. Copiez les fichiers d'environnement :

```bash
cp .env.example .env
cp backend/.env.example backend/.env
cp backend/.env.test.example backend/.env.test
```

3. Lancez les conteneurs :

   ```bash
   docker-compose up --build -d

   ```

4. Générer les clés JWT :

   ```bash
   docker-compose exec app php bin/console lexik:jwt:generate-keypair --skip-if-exists
   ```

5. Lancer les migrations et les fixtures :

   ```bash
   docker-compose exec app php bin/console doctrine:migrations:migrate --no-interaction
   docker-compose exec app php bin/console doctrine:fixtures:load --no-interaction

   ```

   **Note :** Si problème pour afficher correctement les assets du Swagger d'API Platform :

```bash
docker-compose exec app php bin/console assets:install public
```

## Accès aux URLS

- **Frontend** : [http://localhost:5173](http://localhost:5173/)

- **Documentation de l'API** : [http://localhost:8000/docs](http://localhost:8000/docs)

- **Backend API** : [http://localhost:8000](http://localhost:8000)

- **PHPMyAdmin** : [http://localhost:8080/](http://localhost:8080/)

## Rôles et permissions

### Accès public

Les utilisateurs non authentifiés peuvent consulter les topics et accéder à la page de connexion.

### Utilisateurs authentifiés

Les utilisateurs authéntifiés disposent de fonctionnalités différentes selon leur rôle :

**Rôle User**

- Créer de nouveaux topics
- Modifier et supprimer ses propres topics
- Créer de nouveaux commentaires
- Modifier et supprimer ses propres commentaires

**Rôle Admin**

- Créer, modifier et supprimer tous les topics (y compris ceux créés par d'autres utilisateurs)
- Créer, modifier et supprimer tous les commentaires (y compris ceux créés par d'autres utilisateurs)

### Comptes de test

Deux comptes sont disponibles pour tester l'application :

| Nom d'utilisateur | Rôle  | Mot de passe |
| ----------------- | ----- | ------------ |
| Bob               | User  | password     |
| FoxMaster         | Admin | password     |

## Les tests

- Lancer les tests unitaires et fonctionnels (backend) :

```bash
docker compose exec app php bin/phpunit --testdox

```

- Lancer les tests Vitest (frontend) :

```bash
cd frontend
npm run test
```

## Commandes utiles

- Démarrer l'application

```bash
docker-compose up -d
```

- Arrêter l'application

```bash
docker-compose down
```

- Vider le cache :

```bash
docker-compose exec app bash
php bin/console cache:clear
```

- Relancer les conteneurs :

```bash
docker-compose restart
```
