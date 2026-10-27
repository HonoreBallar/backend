# Déploiement : Vercel (frontend) + Render (backend Docker + PostgreSQL)

```
Visiteur ──► Vercel : Next.js (frontend/)
                │  lit /api/portfolio, envoie /api/contact
                ▼
             Render : conteneur Docker FrankenPHP + Symfony (backend/)
                │
                ▼
             Render : PostgreSQL 16

Admin modifié ──► Symfony appelle https://<vercel>/api/revalidate ──► le site se met à jour
```

## 1. Développement local (Docker)

Depuis `backend/` :

```bash
docker compose up -d --build      # PostgreSQL + backend sur http://localhost:8000
docker compose logs -f backend    # logs du backend
docker compose down               # arrêt (les données restent dans le volume)
```

Pour que le backend en Docker déclenche la revalidation du frontend local, lance-le avec le secret :
`REVALIDATE_SECRET=<même valeur que frontend/.env.local> docker compose up -d`.

Puis le frontend, depuis `frontend/` : `npm run dev` (ou `npm run build && npm start`).

Lancer Symfony sans Docker reste possible (`php -S 127.0.0.1:8000 -t public`) : il utilise
alors le PostgreSQL du conteneur, exposé sur le port **5433** de ta machine.

## 2. Mettre le backend sur GitHub

Le dossier `backend/` n'est pas encore un dépôt Git. Crée un dépôt vide `backend` sur GitHub, puis :

```bash
cd backend
git init -b main
git add .
git commit -m "Backend Symfony dockerisé"
git remote add origin https://github.com/HonoreBallar/backend.git
git push -u origin main
```

`.env.local` (tes secrets locaux) et `var/` sont ignorés par `.gitignore`.

## 3. Render : base PostgreSQL + backend

1. Génère un secret partagé, à garder pour l'étape 5 : `openssl rand -hex 32`.
2. Render → **New → Blueprint** → choisis le dépôt `backend`. Render lit `render.yaml` et crée :
   `portfolio-db` (PostgreSQL) et `portfolio-api` (Docker).
3. Render demande les variables marquées `sync: false`. Comme l'URL Vercel n'existe pas encore, mets :

   | Variable | Valeur pour l'instant |
   |---|---|
   | `DEFAULT_URI` | `https://portfolio-api.onrender.com` (l'URL réelle s'affiche après création) |
   | `FRONTEND_URL` | `https://example.com` (corrigée à l'étape 6) |
   | `CORS_ALLOW_ORIGIN` | `^https://example\.com$` (corrigée à l'étape 6) |
   | `REVALIDATE_SECRET` | le secret généré au point 1 |

4. Au démarrage, le conteneur applique les migrations (`docker/entrypoint.sh`).
   Vérifie : `https://<ton-api>.onrender.com/api/portfolio` doit répondre (contenu vide pour l'instant).

## 4. Copier ton contenu et ton compte admin dans la base Render

Dans Render → `portfolio-db` → **Connect**, copie l'**External Database URL**. Puis, depuis `backend/` :

```bash
docker compose exec -T database pg_dump -U app -d app --data-only --no-owner \
  --exclude-table=doctrine_migration_versions \
  | psql "<EXTERNAL_DATABASE_URL>"
```

Cela copie tout : projets, parcours, liens et ton compte admin (même mot de passe).

## 5. Vercel : le frontend

1. Vercel → **Add New → Project** → importe le dépôt `HonoreBallar/frontend` (Next.js est détecté tout seul).
2. Variables d'environnement (voir `frontend/.env.example`) :

   | Variable | Valeur |
   |---|---|
   | `NEXT_PUBLIC_API_URL` | `https://<ton-api>.onrender.com` |
   | `REVALIDATE_SECRET` | le même secret que sur Render |

3. **Deploy**. Note l'URL obtenue, par exemple `https://honore-amounan.vercel.app`.

## 6. Relier Render à Vercel

Render → `portfolio-api` → **Environment**, remplace :

| Variable | Valeur |
|---|---|
| `FRONTEND_URL` | `https://honore-amounan.vercel.app` |
| `CORS_ALLOW_ORIGIN` | `^https://honore-amounan\.vercel\.app$` |

Enregistre (Render redéploie). Ensuite :
- l'admin est sur `https://<ton-api>.onrender.com/admin` ;
- chaque modification dans l'admin met à jour le site Vercel automatiquement ;
- le formulaire de contact enregistre les messages dans PostgreSQL (visibles dans l'admin).

Avec un nom de domaine personnalisé, ajoute-le dans Vercel puis mets-le dans `FRONTEND_URL` et `CORS_ALLOW_ORIGIN`
(plusieurs domaines : `^https://(honore-amounan\.vercel\.app|www\.mondomaine\.com)$`).

## 7. Mises à jour

- **Contenu** : via l'admin, rien à redéployer.
- **Code frontend** : `git push` sur `frontend` → Vercel redéploie.
- **Code backend** : `git push` sur `backend` → Render reconstruit l'image et applique les nouvelles migrations.
  Après une modification d'entité : `php bin/console doctrine:migrations:diff` en local, puis commit de la migration.

## Limites des offres gratuites

- **Render web gratuit** : le service s'endort après 15 min sans visite ; la requête suivante prend environ 1 min.
  Le site Vercel reste rapide car il affiche le contenu mis en cache ; seul le premier envoi du formulaire
  de contact ou l'ouverture de l'admin est lent. Plan payant le moins cher : pas de mise en veille.
- **PostgreSQL gratuit de Render** : il expire après une durée limitée (30 jours au moment de l'écriture,
  vérifie les conditions actuelles). Pour un portfolio durable : passe la base en plan payant, ou utilise
  une base PostgreSQL gratuite externe (par exemple Neon) et mets son URL dans `DATABASE_URL` sur Render
  à la place de `fromDatabase` dans `render.yaml`.
