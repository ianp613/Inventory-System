-- Palo RSBSA database schema (SQLite 3.x). Created by:  php database/setup.php
-- Every statement is safe to run again (IF NOT EXISTS).

-- Single administrator account (created with database/setup.php)
CREATE TABLE IF NOT EXISTS admins (
  id            INTEGER PRIMARY KEY AUTOINCREMENT,
  username      TEXT NOT NULL UNIQUE COLLATE NOCASE,
  password_hash TEXT NOT NULL,
  name          TEXT NOT NULL DEFAULT 'Administrator',
  last_login    TEXT,
  created_at    TEXT NOT NULL
);

-- Failed login attempts (rate limiting)
CREATE TABLE IF NOT EXISTS login_attempts (
  id           INTEGER PRIMARY KEY AUTOINCREMENT,
  ip_address   TEXT NOT NULL,
  username     TEXT NOT NULL,
  attempted_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_attempts ON login_attempts (ip_address, username, attempted_at);

-- Registered farmers (Basic Information, Family Background)
CREATE TABLE IF NOT EXISTS farmers (
  id                 INTEGER PRIMARY KEY AUTOINCREMENT,
  rsbsa_no           TEXT UNIQUE COLLATE NOCASE,
  surname            TEXT NOT NULL,
  first_name         TEXT NOT NULL,
  middle_name        TEXT NOT NULL DEFAULT '',
  suffix             TEXT NOT NULL DEFAULT '',
  birth_date         TEXT NOT NULL,
  valid_id_type      TEXT NOT NULL,
  valid_id_no        TEXT NOT NULL,
  addr_zone_sitio    TEXT NOT NULL DEFAULT '',
  addr_barangay      TEXT NOT NULL,
  addr_municipality  TEXT NOT NULL,
  addr_province      TEXT NOT NULL,
  spouse_surname     TEXT NOT NULL DEFAULT '',
  spouse_first_name  TEXT NOT NULL DEFAULT '',
  spouse_middle_name TEXT NOT NULL DEFAULT '',
  spouse_separated   INTEGER NOT NULL DEFAULT 0,
  is_married_woman   INTEGER NOT NULL DEFAULT 0,
  maiden_name        TEXT NOT NULL DEFAULT '',
  civil_status       TEXT NOT NULL DEFAULT '',     -- Single, Married, Widowed, Separated, Annulled
  photo_path         TEXT,                         -- 2x2 picture
  photo_name         TEXT,
  valid_id_path      TEXT,                         -- scanned / photographed valid ID
  valid_id_name      TEXT,
  created_at         TEXT NOT NULL,
  updated_at         TEXT NOT NULL                -- "Date Updated"
);
CREATE INDEX IF NOT EXISTS idx_farmers_name     ON farmers (surname, first_name);
CREATE INDEX IF NOT EXISTS idx_farmers_barangay ON farmers (addr_barangay);
CREATE INDEX IF NOT EXISTS idx_farmers_updated  ON farmers (updated_at);

CREATE TABLE IF NOT EXISTS children (
  id        INTEGER PRIMARY KEY AUTOINCREMENT,
  farmer_id INTEGER NOT NULL REFERENCES farmers (id) ON DELETE CASCADE,
  name      TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_children_farmer ON children (farmer_id);

-- Parcel Particulars + Farming Status (one row per parcel)
CREATE TABLE IF NOT EXISTS parcels (
  id                   INTEGER PRIMARY KEY AUTOINCREMENT,
  farmer_id            INTEGER NOT NULL REFERENCES farmers (id) ON DELETE CASCADE,
  cadastral_lot_no     TEXT NOT NULL,
  area_ha              REAL NOT NULL,
  tax_declared_area_ha REAL NOT NULL,
  cultivated_area_ha   REAL NOT NULL DEFAULT 0,
  loc_zone_sitio       TEXT NOT NULL DEFAULT '',
  loc_barangay         TEXT NOT NULL,
  loc_municipality     TEXT NOT NULL,
  loc_province         TEXT NOT NULL,
  commodity            TEXT NOT NULL CHECK (commodity IN ('Rice','Vegetable','Root Crops','Banana','Coconut','Livestock','Fishery')),
  fish_size            TEXT,
  farming_role         TEXT NOT NULL CHECK (farming_role IN ('Owner','Tenant','Leasee','Actual Tiller','Farm Worker')),
  tax_declarant        TEXT NOT NULL,
  association          TEXT NOT NULL DEFAULT '',   -- Name of Association (per parcel)
  parcel_status        TEXT NOT NULL DEFAULT 'Active' CHECK (parcel_status IN ('Active','Inactive','Deceased')),
  tax_file_path        TEXT,
  tax_file_name        TEXT,
  duplicate_flag       INTEGER NOT NULL DEFAULT 0,
  created_at           TEXT NOT NULL,
  updated_at           TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_parcels_farmer    ON parcels (farmer_id);
CREATE INDEX IF NOT EXISTS idx_parcels_commodity ON parcels (commodity);
CREATE INDEX IF NOT EXISTS idx_parcels_barangay  ON parcels (loc_barangay);

-- Total area in hectares per crop (Rice, Vegetable, Root Crops, Banana, Coconut, Fruit Bearing)
CREATE TABLE IF NOT EXISTS crop_areas (
  parcel_id INTEGER NOT NULL REFERENCES parcels (id) ON DELETE CASCADE,
  crop_key  TEXT NOT NULL,
  area_ha   REAL NOT NULL DEFAULT 0,
  PRIMARY KEY (parcel_id, crop_key)
);

-- No. of heads (Cow, Carabao, Sheep, Goat, Swine, Poultry, etc.)
CREATE TABLE IF NOT EXISTS parcel_livestock (
  id          INTEGER PRIMARY KEY AUTOINCREMENT,
  parcel_id   INTEGER NOT NULL REFERENCES parcels (id) ON DELETE CASCADE,
  animal_type TEXT NOT NULL,
  heads       INTEGER NOT NULL DEFAULT 0
);
CREATE INDEX IF NOT EXISTS idx_livestock_parcel ON parcel_livestock (parcel_id);

-- Duplicate flags: a lot + tax declaration that is already registered to another farmer
CREATE TABLE IF NOT EXISTS duplicate_flags (
  id                   INTEGER PRIMARY KEY AUTOINCREMENT,
  parcel_id            INTEGER REFERENCES parcels (id) ON DELETE SET NULL,
  farmer_id            INTEGER NOT NULL REFERENCES farmers (id) ON DELETE CASCADE,   -- the flagged (second) farmer
  registered_farmer_id INTEGER REFERENCES farmers (id) ON DELETE SET NULL,           -- the farmer already holding the lot
  lot_no               TEXT NOT NULL,
  barangay             TEXT NOT NULL DEFAULT '',
  area_ha              REAL NOT NULL DEFAULT 0,
  tax_declared_area_ha REAL NOT NULL DEFAULT 0,
  status               TEXT NOT NULL DEFAULT 'open' CHECK (status IN ('open','resolved')),
  remarks              TEXT,
  flagged_at           TEXT NOT NULL,
  resolved_at          TEXT
);
CREATE INDEX IF NOT EXISTS idx_flags_status ON duplicate_flags (status, flagged_at);
CREATE INDEX IF NOT EXISTS idx_flags_parcel ON duplicate_flags (parcel_id);

CREATE TABLE IF NOT EXISTS duplicate_documents (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  flag_id    INTEGER NOT NULL REFERENCES duplicate_flags (id) ON DELETE CASCADE,
  file_path  TEXT NOT NULL,
  file_name  TEXT NOT NULL,
  created_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_docs_flag ON duplicate_documents (flag_id);

-- History of each parcel: one row when it is registered and one for every update that changed something.
-- changes  = JSON list of {field, label, old, new};  snapshot = JSON of the main values after that event.
CREATE TABLE IF NOT EXISTS parcel_history (
  id         INTEGER PRIMARY KEY AUTOINCREMENT,
  parcel_id  INTEGER NOT NULL REFERENCES parcels (id) ON DELETE CASCADE,
  farmer_id  INTEGER NOT NULL,
  event      TEXT NOT NULL,
  changes    TEXT NOT NULL DEFAULT '[]',
  snapshot   TEXT NOT NULL DEFAULT '{}',
  changed_at TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_history_parcel ON parcel_history (parcel_id, id);

-- Activity log: who did what and when. Rows are only ever added (the system has no way to edit or delete them).
-- username / display_name are copied at the time, so renaming the user later does not change old rows.
CREATE TABLE IF NOT EXISTS activity_log (
  id           INTEGER PRIMARY KEY AUTOINCREMENT,
  admin_id     INTEGER,                       -- NULL for failed logins and command-line actions
  username     TEXT NOT NULL DEFAULT '',
  display_name TEXT NOT NULL DEFAULT '',
  category     TEXT NOT NULL,                 -- Login, Farmer, Duplicate, Report, Print, Document, Account, System
  action       TEXT NOT NULL,                 -- short title, e.g. "Updated farmer record"
  details      TEXT NOT NULL DEFAULT '',
  ip_address   TEXT NOT NULL DEFAULT '',
  created_at   TEXT NOT NULL
);
CREATE INDEX IF NOT EXISTS idx_activity_time ON activity_log (created_at, id);
CREATE INDEX IF NOT EXISTS idx_activity_cat  ON activity_log (category);
