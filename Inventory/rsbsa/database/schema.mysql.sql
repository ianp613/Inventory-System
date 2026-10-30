-- OPTIONAL: MySQL 5.7+ / MariaDB 10.3+ version of the schema (the system uses SQLite by default, see schema.sql).
-- To use MySQL: import this file, then in api/config.php set db.driver to 'mysql' and fill in the host/user/password.
-- Import with:  mysql -u root -p < database/schema.mysql.sql

CREATE DATABASE IF NOT EXISTS rsbsa CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE rsbsa;

-- Single administrator account (created with database/create_admin.php)
CREATE TABLE IF NOT EXISTS admins (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  username      VARCHAR(60)  NOT NULL,
  password_hash VARCHAR(255) NOT NULL,
  name          VARCHAR(120) NOT NULL DEFAULT 'Administrator',
  last_login    DATETIME NULL,
  created_at    DATETIME NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY uq_admins_username (username)
) ENGINE=InnoDB;

-- Failed login attempts (rate limiting)
CREATE TABLE IF NOT EXISTS login_attempts (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ip_address   VARCHAR(45)  NOT NULL,
  username     VARCHAR(60)  NOT NULL,
  attempted_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_attempts (ip_address, username, attempted_at)
) ENGINE=InnoDB;

-- Registered farmers (Basic Information, Family Background)
CREATE TABLE IF NOT EXISTS farmers (
  id                 INT UNSIGNED NOT NULL AUTO_INCREMENT,
  rsbsa_no           VARCHAR(40) NULL,
  surname            VARCHAR(60) NOT NULL,
  first_name         VARCHAR(60) NOT NULL,
  middle_name        VARCHAR(60) NOT NULL DEFAULT '',
  suffix             VARCHAR(10) NOT NULL DEFAULT '',
  birth_date         DATE NOT NULL,
  valid_id_type      VARCHAR(60) NOT NULL,
  valid_id_no        VARCHAR(60) NOT NULL,
  addr_zone_sitio    VARCHAR(80) NOT NULL DEFAULT '',
  addr_barangay      VARCHAR(80) NOT NULL,
  addr_municipality  VARCHAR(80) NOT NULL,
  addr_province      VARCHAR(80) NOT NULL,
  spouse_surname     VARCHAR(60) NOT NULL DEFAULT '',
  spouse_first_name  VARCHAR(60) NOT NULL DEFAULT '',
  spouse_middle_name VARCHAR(60) NOT NULL DEFAULT '',
  spouse_separated   TINYINT(1) NOT NULL DEFAULT 0,
  is_married_woman   TINYINT(1) NOT NULL DEFAULT 0,
  maiden_name        VARCHAR(120) NOT NULL DEFAULT '',
  civil_status       VARCHAR(20)  NOT NULL DEFAULT '',   -- Single, Married, Widowed, Separated, Annulled
  photo_path         VARCHAR(255) NULL,                  -- 2x2 picture
  photo_name         VARCHAR(160) NULL,
  valid_id_path      VARCHAR(255) NULL,                  -- scanned / photographed valid ID
  valid_id_name      VARCHAR(160) NULL,
  created_at         DATETIME NOT NULL,
  updated_at         DATETIME NOT NULL,           -- "Date Updated"
  PRIMARY KEY (id),
  UNIQUE KEY uq_farmers_rsbsa_no (rsbsa_no),
  KEY idx_farmers_name (surname, first_name),
  KEY idx_farmers_barangay (addr_barangay),
  KEY idx_farmers_updated (updated_at)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS children (
  id        INT UNSIGNED NOT NULL AUTO_INCREMENT,
  farmer_id INT UNSIGNED NOT NULL,
  name      VARCHAR(120) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_children_farmer (farmer_id),
  CONSTRAINT fk_children_farmer FOREIGN KEY (farmer_id) REFERENCES farmers (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Parcel Particulars + Farming Status (one row per parcel)
CREATE TABLE IF NOT EXISTS parcels (
  id                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  farmer_id            INT UNSIGNED NOT NULL,
  cadastral_lot_no     VARCHAR(40) NOT NULL,
  area_ha              DECIMAL(12,4) NOT NULL,
  tax_declared_area_ha DECIMAL(12,4) NOT NULL,
  cultivated_area_ha   DECIMAL(12,4) NOT NULL DEFAULT 0,
  loc_zone_sitio       VARCHAR(80) NOT NULL DEFAULT '',
  loc_barangay         VARCHAR(80) NOT NULL,
  loc_municipality     VARCHAR(80) NOT NULL,
  loc_province         VARCHAR(80) NOT NULL,
  commodity            ENUM('Rice','Vegetable','Root Crops','Banana','Coconut','Livestock','Fishery') NOT NULL,
  fish_size            VARCHAR(60) NULL,
  farming_role         ENUM('Owner','Tenant','Leasee','Actual Tiller','Farm Worker') NOT NULL,
  tax_declarant        VARCHAR(120) NOT NULL,
  association          VARCHAR(120) NOT NULL DEFAULT '',   -- Name of Association (per parcel)
  parcel_status        ENUM('Active','Inactive','Deceased') NOT NULL DEFAULT 'Active',
  tax_file_path        VARCHAR(255) NULL,
  tax_file_name        VARCHAR(160) NULL,
  duplicate_flag       TINYINT(1) NOT NULL DEFAULT 0,
  created_at           DATETIME NOT NULL,
  updated_at           DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_parcels_farmer (farmer_id),
  KEY idx_parcels_lot (cadastral_lot_no, loc_barangay),
  KEY idx_parcels_commodity (commodity),
  KEY idx_parcels_barangay (loc_barangay),
  CONSTRAINT fk_parcels_farmer FOREIGN KEY (farmer_id) REFERENCES farmers (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Total area in hectares per crop (Rice, Vegetable, Root Crops, Banana, Coconut, Fruit Bearing)
CREATE TABLE IF NOT EXISTS crop_areas (
  parcel_id INT UNSIGNED NOT NULL,
  crop_key  VARCHAR(20)  NOT NULL,
  area_ha   DECIMAL(12,4) NOT NULL DEFAULT 0,
  PRIMARY KEY (parcel_id, crop_key),
  CONSTRAINT fk_crop_parcel FOREIGN KEY (parcel_id) REFERENCES parcels (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- No. of heads (Cow, Carabao, Sheep, Goat, Swine, Poultry, etc.)
CREATE TABLE IF NOT EXISTS parcel_livestock (
  id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  parcel_id   INT UNSIGNED NOT NULL,
  animal_type VARCHAR(30)  NOT NULL,
  heads       INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  KEY idx_livestock_parcel (parcel_id),
  CONSTRAINT fk_livestock_parcel FOREIGN KEY (parcel_id) REFERENCES parcels (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Duplicate flags: a lot + tax declaration that is already registered to another farmer
CREATE TABLE IF NOT EXISTS duplicate_flags (
  id                   INT UNSIGNED NOT NULL AUTO_INCREMENT,
  parcel_id            INT UNSIGNED NULL,
  farmer_id            INT UNSIGNED NOT NULL,          -- the flagged (second) farmer
  registered_farmer_id INT UNSIGNED NULL,              -- the farmer already holding the lot
  lot_no               VARCHAR(40) NOT NULL,
  barangay             VARCHAR(80) NOT NULL DEFAULT '',
  area_ha              DECIMAL(12,4) NOT NULL DEFAULT 0,
  tax_declared_area_ha DECIMAL(12,4) NOT NULL DEFAULT 0,
  status               ENUM('open','resolved') NOT NULL DEFAULT 'open',
  remarks              TEXT NULL,
  flagged_at           DATETIME NOT NULL,
  resolved_at          DATETIME NULL,
  PRIMARY KEY (id),
  KEY idx_flags_status (status, flagged_at),
  KEY idx_flags_parcel (parcel_id),
  CONSTRAINT fk_flags_parcel FOREIGN KEY (parcel_id) REFERENCES parcels (id) ON DELETE SET NULL,
  CONSTRAINT fk_flags_farmer FOREIGN KEY (farmer_id) REFERENCES farmers (id) ON DELETE CASCADE,
  CONSTRAINT fk_flags_registered FOREIGN KEY (registered_farmer_id) REFERENCES farmers (id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS duplicate_documents (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  flag_id    INT UNSIGNED NOT NULL,
  file_path  VARCHAR(255) NOT NULL,
  file_name  VARCHAR(160) NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_docs_flag (flag_id),
  CONSTRAINT fk_docs_flag FOREIGN KEY (flag_id) REFERENCES duplicate_flags (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- History of each parcel: one row when it is registered and one for every update that changed something.
CREATE TABLE IF NOT EXISTS parcel_history (
  id         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  parcel_id  INT UNSIGNED NOT NULL,
  farmer_id  INT UNSIGNED NOT NULL,
  event      VARCHAR(40) NOT NULL,
  changes    MEDIUMTEXT NOT NULL,
  snapshot   MEDIUMTEXT NOT NULL,
  changed_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_history_parcel (parcel_id, id),
  CONSTRAINT fk_history_parcel FOREIGN KEY (parcel_id) REFERENCES parcels (id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Activity log: who did what and when. Rows are only ever added.
CREATE TABLE IF NOT EXISTS activity_log (
  id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
  admin_id     INT UNSIGNED NULL,
  username     VARCHAR(60)  NOT NULL DEFAULT '',
  display_name VARCHAR(120) NOT NULL DEFAULT '',
  category     VARCHAR(20)  NOT NULL,
  action       VARCHAR(80)  NOT NULL,
  details      TEXT NOT NULL,
  ip_address   VARCHAR(45)  NOT NULL DEFAULT '',
  created_at   DATETIME NOT NULL,
  PRIMARY KEY (id),
  KEY idx_activity_time (created_at, id),
  KEY idx_activity_cat (category)
) ENGINE=InnoDB;
