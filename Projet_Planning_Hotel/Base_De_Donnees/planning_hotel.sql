-- planning_hotel_pro — schema reconstruction (V2.1)
-- Generated 2025-10-22
-- Charset/Collation chosen to match PHP PDO utf8mb4 usage.

CREATE DATABASE IF NOT EXISTS `hotel` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hotel`;

-- Table: reservations
DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `room` VARCHAR(50) NOT NULL,                         -- ex: 'Chambre 1' à 'Chambre 10'
  `date_start` DATE NOT NULL,                          -- date d'arrivée
  `nights` INT NOT NULL DEFAULT 1,                     -- nb de nuits (>=1)
  `name` VARCHAR(120) NOT NULL DEFAULT '',             -- nom du client
  `phone` VARCHAR(40)  NOT NULL DEFAULT '',            -- téléphone
  `count` INT NOT NULL DEFAULT 0,                      -- nb personnes total (optionnel, ex: 4)
  `occupancy` VARCHAR(20) NOT NULL DEFAULT '',         -- format libre ex: '2+2'
  `breakfast` ENUM('oui','non') NOT NULL DEFAULT 'non',
  `halfboard` ENUM('oui','non') NOT NULL DEFAULT 'non',
  `fullboard` ENUM('oui','non') NOT NULL DEFAULT 'non',
  `breakfast_count` INT NOT NULL DEFAULT 0,            -- compteurs par jour (nouveau système)
  `halfboard_count` INT NOT NULL DEFAULT 0,
  `fullboard_count` INT NOT NULL DEFAULT 0,
  `transfer_arrivee` ENUM('oui','non') NOT NULL DEFAULT 'non',
  `transfer_depart`  ENUM('oui','non') NOT NULL DEFAULT 'non',
  `flight` VARCHAR(40) NOT NULL DEFAULT '',            -- n° de vol (optionnel)
  `invoice` VARCHAR(40) NOT NULL DEFAULT '',           -- facture / ref (optionnel)
  `notes` TEXT,                                        -- remarques libres
  `chambre_demande` TINYINT(1) NOT NULL DEFAULT 0,      -- 1 si le client veut spécialement cette chambre
  `status` ENUM('reservation','hold','maintenance') NOT NULL DEFAULT 'reservation', -- réservation / blocage (option) / maintenance
  `block_reason` VARCHAR(255) NOT NULL DEFAULT '',     -- raison courte du blocage (maintenance, option client, etc.)
  PRIMARY KEY (`id`),
  KEY `idx_room_date` (`room`,`date_start`),
  KEY `idx_date` (`date_start`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Overlap checking is enforced at the API layer (add.php/update.php).
-- If you already have an older table without the new fields, you can run the migration instead:
--
-- ALTER TABLE reservations
--   ADD COLUMN fullboard ENUM('oui','non') DEFAULT 'non' AFTER halfboard,
--   ADD COLUMN breakfast_count INT NOT NULL DEFAULT 0 AFTER fullboard,
--   ADD COLUMN halfboard_count INT NOT NULL DEFAULT 0 AFTER breakfast_count,
--   ADD COLUMN fullboard_count INT NOT NULL DEFAULT 0 AFTER halfboard_count;
--
-- Blocages + chambre demandée :
-- ALTER TABLE reservations
--   ADD COLUMN chambre_demande TINYINT(1) NOT NULL DEFAULT 0,
--   ADD COLUMN status ENUM('reservation','hold','maintenance') NOT NULL DEFAULT 'reservation',
--   ADD COLUMN block_reason VARCHAR(255) NOT NULL DEFAULT '';
--
-- Admin login is cookie/session-based (no DB table). See login.php for test credentials.
