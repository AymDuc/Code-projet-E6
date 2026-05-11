# Documentation technique - Planning Hotel

## Installation locale
1. Installer XAMPP.
2. Copier le contenu de `Code_Source/` dans `htdocs/planning_hotel/`.
3. Lancer Apache et MySQL.
4. Ouvrir phpMyAdmin.
5. Importer `Base_De_Donnees/planning_hotel.sql`.
6. Modifier `Code_Source/db.php` si necessaire.
7. Acceder a `http://localhost/planning_hotel/login.php`.

## Configuration base de donnees
Le fichier `db.php` contient les parametres de connexion. Pour une installation locale XAMPP standard :

```php
$DB_HOST = 'localhost';
$DB_NAME = 'hotel';
$DB_USER = 'root';
$DB_PASS = '';
```

## Structure principale
```text
Code_Source/
├── api/
│   ├── add.php
│   ├── update.php
│   ├── delete.php
│   ├── list.php
│   └── export_csv.php
├── db.php
├── login.php
├── planning.php
├── script.js
├── style_glow.css
└── schema_hotel_v2_1.sql
```

## Demonstration conseillee
1. Se connecter.
2. Afficher le planning.
3. Ajouter une reservation test.
4. Modifier la reservation.
5. Supprimer la reservation.
6. Exporter en CSV.
7. Montrer la table `reservations` dans phpMyAdmin.

## Points de securite
- La page `planning.php` est protegee par session.
- Les appels a la base utilisent PDO.
- Les erreurs de connexion ne revelent pas le mot de passe.
- Les identifiants de production ne doivent pas etre stockes dans le depot public.
