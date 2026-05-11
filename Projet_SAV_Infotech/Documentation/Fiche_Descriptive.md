# Fiche descriptive - Realisation professionnelle 2

## Nom du projet
Infotech SAV Manager

## Contexte
Le projet consiste a developper une application desktop pour suivre les interventions SAV d'une boutique informatique. L'objectif est de centraliser les appareils deposes, les diagnostics, les statuts et les informations client.

## Besoin initial
- Enregistrer une intervention SAV.
- Retrouver rapidement une fiche client.
- Suivre l'etat d'avancement d'une reparation.
- Exporter les interventions.
- Utiliser une base locale simple et portable.

## Technologies utilisees
- C#
- Windows Forms
- .NET 8
- SQLite

## Fonctionnalites principales
- Ajout d'une intervention.
- Modification d'une intervention.
- Suppression d'une intervention.
- Recherche rapide.
- Filtrage par statut.
- Export CSV.
- Numero de dossier automatique au format `SAV-AAAA-000`.
- Date de restitution prevue.
- Statistiques simples par statut.

## Ameliorations ajoutees pour rendre le projet plus credible
- Ajout d'un numero de dossier SAV.
- Ajout d'une date de restitution.
- Ajout d'un filtre par statut.
- Ajout de statistiques en haut de l'interface.
- Migration automatique des anciennes bases SQLite.

## Modalites d'acces aux elements techniques
- Code source : `Projet_SAV_Infotech/Code_Source/`
- Schema SQL : `Projet_SAV_Infotech/Base_De_Donnees/sav_infotech_schema.sql`
- Fichier principal interface : `MainForm.cs`
- Fichier gestion base : `Database.cs`

## Competences mobilisees
- Developper une solution applicative.
- Exploiter une base de donnees locale.
- Repondre a un besoin metier.
- Mettre en place une interface utilisateur.
- Produire une documentation technique.
