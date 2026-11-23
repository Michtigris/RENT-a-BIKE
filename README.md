# RENT-a-BIKE

Application PHP MVC de réservation gratuite de vélos, avec espace public et tableau de bord municipal.

## Prérequis

- PHP 8.0 ou supérieur avec PDO MySQL
- MySQL ou MariaDB
- Apache avec `mod_rewrite`, ou le serveur intégré PHP

## Installation avec MAMP

1. Créez la base et les tables en important `script.sql` dans phpMyAdmin.
2. Copiez `config.example.php` vers `config.local.php` et renseignez les identifiants MySQL locaux dans le tableau PHP retourné. Ce fichier est ignoré par Git.
3. Ouvrez `http://localhost/PHP/Nouveau%20dossier/setup.php` une seule fois pour créer les données de démonstration et le premier compte administrateur.
4. Notez l’identifiant et le mot de passe affichés à la première installation.
5. Ouvrez `http://localhost/PHP/Nouveau%20dossier/`.

Le compte initial est un administrateur. Il peut gérer les utilisateurs et les vélos. Les comptes citoyens sont créés lors d’une réservation ; les comptes employés peuvent être ajoutés en base avec le rôle `employee`.

## Serveur PHP intégré

Depuis la racine du projet :

```sh
php -S 127.0.0.1:8000 -t .
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
