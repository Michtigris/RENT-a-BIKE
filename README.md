# RENT-a-BIKE

Application PHP MVC de réservation gratuite de vélos, avec espace public et tableau de bord municipal.

## Organisation MVC

- `src/Models` : accès aux données et connexion Singleton à la base
- `src/Controllers` : traitement des requêtes et choix des vues
- `src/Views` : pages affichées
- `public/index.php` : point d’entrée et routes
- `config/` : configuration locale de la base
- `database/` : script SQL
- `scripts/` : installation en ligne de commande
- `docs/` : consigne PDF

## Prérequis

- PHP 8.0 ou supérieur avec PDO MySQL
- MySQL ou MariaDB
- Apache avec `mod_rewrite`, ou le serveur intégré PHP

## Installation avec MAMP

1. Démarrez Apache et MySQL dans MAMP.
2. Pour une première installation, copiez `config/config.example.php` vers `config/config.local.php` et renseignez les identifiants MySQL. Ce fichier est ignoré par Git.
3. Depuis la racine du projet, exécutez `php scripts/setup.php`. Le script crée la base, les tables, trois vélos de démonstration et le premier compte administrateur. Notez l’identifiant et le mot de passe affichés à la création du compte.
4. Ouvrez [Rent a Bike dans MAMP](http://localhost:81/PHP/Nouveau%20dossier/). Si le port Apache de MAMP est différent, utilisez celui affiché dans MAMP.

Les employés peuvent consulter les comptes, enregistrer des vélos et traiter les réservations. L’administrateur peut aussi supprimer les réservations, les vélos et les comptes. Pour supprimer un vélo ou un compte lié à une réservation, supprimez d’abord cette réservation. Un compte citoyen est créé lors de sa première demande.

## Serveur PHP intégré

Depuis la racine du projet :

```sh
php -S 127.0.0.1:8000 -t public
```

Puis ouvrez `http://127.0.0.1:8000/`.

## Styles Sass

Le navigateur utilise `public/assets/style.css`, généré depuis `public/assets/style.scss` :

```sh
npx sass public/assets/style.scss public/assets/style.css --no-source-map
```

## Fonctionnalités

- Catalogue public et demandes de réservation avec vérification des chevauchements de dates
- Authentification employé et administrateur
- Traitement et recherche dynamique des réservations
- Gestion des vélos, comptes et réservations par les administrateurs
- Mots de passe hachés avec `password_hash()`

## Notions de cours utilisées

- Formulaires GET et POST et validation des champs, comme dans `3-formulaires`.
- Sessions PHP pour la connexion, comme dans `4-sessions`.
- Classes, requêtes préparées PDO et connexion Singleton, comme dans `7-bdd`, `8-poo` et `9-mvc`.
- Routage et découpage en modèles, vues et contrôleurs, comme dans le TP Librairie.
- Sass pour les styles et AJAX pour la recherche de réservations, conformément au sujet du TP.
