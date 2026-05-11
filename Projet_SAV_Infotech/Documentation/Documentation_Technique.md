# Documentation technique - Infotech SAV Manager

## Installation
1. Installer .NET 8 SDK ou Visual Studio avec la charge de travail `Developpement .NET Desktop`.
2. Ouvrir `Code_Source/InfotechSAVManager.csproj`.
3. Restaurer les packages NuGet si necessaire.
4. Lancer le projet.

## Base de donnees
La base SQLite `sav_manager.db` est creee automatiquement au premier lancement dans le dossier d'execution de l'application.

Le schema est disponible dans :
`Base_De_Donnees/sav_infotech_schema.sql`

## Structure du code
```text
Code_Source/
├── Program.cs
├── Config.cs
├── Database.cs
├── MainForm.cs
├── InfotechSAVManager.csproj
└── README.md
```

## Role des fichiers
- `Program.cs` : point d'entree de l'application.
- `Config.cs` : configuration simple des statuts et types d'appareils.
- `Database.cs` : creation de la base, requetes SQL et CRUD.
- `MainForm.cs` : interface graphique et interactions utilisateur.

## Demonstration conseillee
1. Lancer l'application.
2. Ajouter une fiche intervention.
3. Modifier son statut.
4. Utiliser la recherche.
5. Filtrer par statut.
6. Exporter en CSV.
7. Montrer le fichier `Database.cs` et le schema SQLite.

## Remarque
Le projet n'a pas ete compile dans l'environnement ChatGPT car .NET n'est pas installe ici. Le code a ete organise et ameliore textuellement pour etre ouvert dans Visual Studio.
