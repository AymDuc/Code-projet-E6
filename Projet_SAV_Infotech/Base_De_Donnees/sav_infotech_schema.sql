-- Infotech SAV Manager - schema SQLite
-- Base creee automatiquement par l'application C# au premier lancement.

CREATE TABLE IF NOT EXISTS interventions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    numero_dossier TEXT,
    client TEXT NOT NULL,
    telephone TEXT,
    appareil TEXT NOT NULL,
    marque TEXT,
    probleme TEXT NOT NULL,
    diagnostic TEXT,
    statut TEXT NOT NULL,
    date_depot TEXT NOT NULL,
    date_restitution TEXT
);

INSERT INTO interventions
(numero_dossier, client, telephone, appareil, marque, probleme, diagnostic, statut, date_depot, date_restitution)
VALUES
('SAV-2026-001', 'Client Test', '87000000', 'PC portable', 'Lenovo ThinkPad', 'PC lent au demarrage', 'Nettoyage logiciels inutiles et verification disque.', 'Diagnostic', '11/05/2026 09:00', ''),
('SAV-2026-002', 'Mairie Test', '40500000', 'Unité centrale', 'Dell OptiPlex', 'Ne demarre plus', 'Test alimentation et controle RAM a prevoir.', 'En attente client', '11/05/2026 10:30', '15/05/2026');
