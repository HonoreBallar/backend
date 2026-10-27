# Portfolio — backend

Symfony 7.4 + PostgreSQL + EasyAdmin 5, servi par FrankenPHP dans Docker. Sert le contenu du portfolio au frontend Next.js (`../frontend`) et reçoit les messages du formulaire de contact.

## Démarrage

Prérequis : Docker.

```bash
docker compose up -d --build                                      # PostgreSQL + backend sur http://localhost:8000
docker compose exec backend php bin/console app:create-admin vous@exemple.com
```

Les migrations sont appliquées au démarrage du conteneur (`docker/entrypoint.sh`).
Pour charger le contenu initial (celui de `frontend/src/lib/data.ts`), il faut PHP 8.2+ (avec `pdo_pgsql`) et Composer sur la machine :
`composer install` puis `php bin/console doctrine:fixtures:load -n` (utilise PostgreSQL sur le port 5433).

Sans Docker pour Symfony : `docker compose up -d database` puis `php -S 127.0.0.1:8000 -t public`.

Administration : http://localhost:8000/admin (connexion sur `/login`).

> ⚠️ `doctrine:fixtures:load` **vide toute la base** (contenu, messages et comptes admin). À n'utiliser qu'à l'initialisation.

## Variables d'environnement

À définir dans `.env.local` (non versionné) :

| Variable | Rôle |
|---|---|
| `APP_SECRET` | Secret Symfony |
| `DATABASE_URL` | Par défaut le PostgreSQL de `compose.yaml` (`127.0.0.1:5433`) ; fourni par Render en production |
| `TRUSTED_PROXIES` | Prod uniquement : proxys de confiance (HTTPS et IP réelle derrière Render) |
| `CORS_ALLOW_ORIGIN` | Regex des origines autorisées à appeler `/api` (ex. `^https://mon-domaine\.fr$`) |
| `FRONTEND_URL` | URL du site Next.js, appelée après chaque modification |
| `REVALIDATE_SECRET` | Secret partagé, **identique** à `REVALIDATE_SECRET` dans `frontend/.env.local`. Vide = webhook désactivé |

Côté frontend (`frontend/.env.local`) : `NEXT_PUBLIC_API_URL` (URL de ce backend) et `REVALIDATE_SECRET`.

## API

| Méthode | Route | Description |
|---|---|---|
| GET | `/api/portfolio` | Tout le contenu du site, au format de `frontend/src/lib/data.ts` |
| POST | `/api/contact` | `{name, email, message, website}` — `website` est un piège à robots. 201, 422 (validation) ou 429 (plus de 5 messages/heure/IP) |

## Mise à jour du site

Chaque création, modification ou suppression d'un contenu dans l'admin déclenche
`FrontendRevalidationListener`, qui appelle `POST {FRONTEND_URL}/api/revalidate`.
Next.js invalide alors son cache (`revalidateTag('portfolio')`) : le visiteur suivant voit la nouvelle version.
Si le frontend est injoignable, l'enregistrement réussit quand même (un avertissement est journalisé).

Si l'API est indisponible, le frontend affiche le contenu de secours de `frontend/src/lib/data.ts` et réessaie quelques minutes plus tard.

## Structure

- `src/Entity/` — une entité par section du site ; celles qui implémentent `PortfolioContent` déclenchent la revalidation
- `src/Controller/Admin/` — tableau de bord et CRUD EasyAdmin
- `src/Controller/Api/` — `/api/portfolio` et `/api/contact`
- `src/EventListener/FrontendRevalidationListener.php` — webhook vers Next.js
- `src/Command/CreateAdminCommand.php` — `app:create-admin`

## Production

Image Docker (`Dockerfile`) déployée sur Render avec `render.yaml` (base PostgreSQL + service web).
Étapes complètes : [`../DEPLOIEMENT.md`](../DEPLOIEMENT.md).

- Restreindre `CORS_ALLOW_ORIGIN` au domaine du frontend.
- Le conteneur écoute sur `$PORT` (fourni par Render) et applique les migrations à chaque démarrage.
