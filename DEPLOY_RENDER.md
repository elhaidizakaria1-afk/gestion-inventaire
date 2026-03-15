# Déploiement sur Render

## Ce que fait cette version
- déploiement de l'app PHP via Docker
- lecture des secrets depuis les variables d'environnement
- création automatique de la table `Produits` si elle n'existe pas
- correction du fichier manquant `auth.php`

## Option A — Web service Render + base MySQL externe
Utilise le fichier `render.yaml`.

### Variables à définir dans Render
- `ADMIN_LOGIN`
- `ADMIN_PASSWORD`
- `DB_HOST`
- `DB_PORT` (3306)
- `DB_NAME`
- `DB_USER`
- `DB_PASS`
- `DB_CHARSET` (utf8mb4)

### Étapes
1. Pousse le dossier du projet sur GitHub.
2. Dans Render, crée un **Web Service** depuis ce repo.
3. Render détectera le `Dockerfile`.
4. Renseigne les variables d'environnement ci-dessus.
5. Déploie.
6. Ouvre l'URL Render : la page `/` doit afficher l'écran de connexion.

## Option B — Full stack sur Render (web + MySQL Render)
Le service MySQL privé sur Render est **payant**. Les plans `free` ne sont pas disponibles pour les private services.

Utilise le contenu de `render.fullstack.yaml` comme blueprint principal si tu veux héberger aussi MySQL sur Render.

### Étapes
1. Renomme `render.fullstack.yaml` en `render.yaml` (ou copie son contenu dans `render.yaml`).
2. Push sur GitHub.
3. Dans Render, crée un **Blueprint** à partir du repo.
4. Renseigne `ADMIN_LOGIN` et `ADMIN_PASSWORD` quand Render te les demande.
5. Déploie.
6. La table `Produits` sera créée automatiquement au premier accès.

## Important — ta base InfinityFree actuelle
La base actuelle codée dans l'ancien projet pointait vers InfinityFree. Les bases MySQL gratuites InfinityFree ne sont pas accessibles depuis une application externe hébergée ailleurs.

Donc pour Render, il faut soit :
- une autre base MySQL accessible depuis Internet ;
- soit un MySQL privé hébergé sur Render ;
- soit exporter les données InfinityFree puis les réimporter ailleurs.

## Export / import des anciennes données
Si tu veux conserver tes produits actuels :
1. exporte la table `Produits` depuis phpMyAdmin sur InfinityFree ;
2. importe ce dump dans la nouvelle base MySQL ;
3. sinon l'application démarrera avec une table vide.
