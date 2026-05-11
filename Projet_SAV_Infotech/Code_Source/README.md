# Infotech SAV Manager - V1

Application desktop Windows développée en C# WinForms pour gérer les interventions SAV d'une boutique informatique.

## Objectif

Résoudre un problème client : centraliser le suivi des appareils déposés en réparation afin d'éviter les oublis et de suivre l'état des interventions.

## Fonctionnalités V1

- Ajout d'une intervention SAV
- Modification d'une intervention existante
- Suppression d'une intervention
- Recherche par client, appareil, marque, problème ou statut
- Stockage local avec SQLite
- Export CSV
- Statuts modifiables facilement dans `Config.cs`

## Technologies

- C#
- Windows Forms
- SQLite
- .NET 8

## Installation

1. Installer Visual Studio Community
2. Ajouter la charge de travail : Développement .NET Desktop
3. Ouvrir le fichier `InfotechSAVManager.csproj`
4. Lancer le projet avec le bouton Démarrer

## Modification facile pour l'épreuve

Pour ajouter un statut, ouvrir `Config.cs` et modifier :

```csharp
public static readonly string[] Statuts =
{
    "Reçu",
    "Diagnostic",
    "En attente client",
    "Pièce commandée",
    "Réparation",
    "Terminé",
    "Restitué"
};
```

Exemple : ajouter `"En attente pièce"`.

## Base de données

Le fichier SQLite `sav_manager.db` est créé automatiquement au premier lancement dans le dossier de l'application.

## Champs gérés

- Client
- Téléphone
- Appareil
- Marque
- Problème constaté
- Diagnostic
- Statut
- Date de dépôt
