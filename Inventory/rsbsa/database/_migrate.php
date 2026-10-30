<?php
/**
 * Brings an existing database up to date without touching any data. Used by setup.php and migrate.php.
 * Safe to run again and again: it only adds what is missing.
 */

/** Columns added after the first release: table => [column => [sqlite definition, mysql definition]] */
function rsbsa_new_columns(): array
{
    return [
        'farmers' => [
            'civil_status'  => ["TEXT NOT NULL DEFAULT ''", "VARCHAR(20) NOT NULL DEFAULT ''"],
            'photo_path'    => ['TEXT', 'VARCHAR(255) NULL'],
            'photo_name'    => ['TEXT', 'VARCHAR(160) NULL'],
            'valid_id_path' => ['TEXT', 'VARCHAR(255) NULL'],
            'valid_id_name' => ['TEXT', 'VARCHAR(160) NULL'],
        ],
        'parcels' => [
            'parcel_status' => [
                "TEXT NOT NULL DEFAULT 'Active' CHECK (parcel_status IN ('Active','Inactive','Deceased'))",
                "ENUM('Active','Inactive','Deceased') NOT NULL DEFAULT 'Active'",
            ],
        ],
    ];
}

function rsbsa_table_columns(PDO $pdo, string $table): array
{
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
        return array_column($pdo->query("PRAGMA table_info($table)")->fetchAll(PDO::FETCH_ASSOC), 'name');
    }
    return array_column($pdo->query("SHOW COLUMNS FROM $table")->fetchAll(PDO::FETCH_ASSOC), 'Field');
}

/** @return string[] a line for everything that was changed (empty when already up to date) */
function rsbsa_migrate(PDO $pdo): array
{
    $sqlite = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite';
    $done = [];

    if ($sqlite) {
        // creates any table that does not exist yet (existing tables and data are left alone)
        $pdo->exec((string) file_get_contents(__DIR__ . '/schema.sql'));
    }

    foreach (rsbsa_new_columns() as $table => $columns) {
        $have = rsbsa_table_columns($pdo, $table);
        if (!$have) {
            continue; // MySQL: the table does not exist yet, import schema.mysql.sql first
        }
        foreach ($columns as $name => $defs) {
            if (!in_array($name, $have, true)) {
                $pdo->exec("ALTER TABLE $table ADD COLUMN $name " . ($sqlite ? $defs[0] : $defs[1]));
                $done[] = "added $table.$name";
                if ($table === 'farmers' && $name === 'civil_status') {
                    // best guess for records saved before this field existed (the admin confirms it on the next update)
                    $pdo->exec("UPDATE farmers SET civil_status = 'Separated' WHERE spouse_separated = 1");
                    $pdo->exec("UPDATE farmers SET civil_status = 'Married'
                                 WHERE civil_status = '' AND (spouse_surname <> '' OR spouse_first_name <> '')");
                }
            }
        }
    }

    // history + activity tables (SQLite gets them from schema.sql above)
    if (!$sqlite) {
        $pdo->exec("CREATE TABLE IF NOT EXISTS activity_log (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT, admin_id INT UNSIGNED NULL, username VARCHAR(60) NOT NULL DEFAULT '',
            display_name VARCHAR(120) NOT NULL DEFAULT '', category VARCHAR(20) NOT NULL, action VARCHAR(80) NOT NULL,
            details TEXT NOT NULL, ip_address VARCHAR(45) NOT NULL DEFAULT '', created_at DATETIME NOT NULL,
            PRIMARY KEY (id), KEY idx_activity_time (created_at, id), KEY idx_activity_cat (category)
        ) ENGINE=InnoDB");
        $pdo->exec("CREATE TABLE IF NOT EXISTS parcel_history (
            id INT UNSIGNED NOT NULL AUTO_INCREMENT, parcel_id INT UNSIGNED NOT NULL, farmer_id INT UNSIGNED NOT NULL,
            event VARCHAR(40) NOT NULL, changes MEDIUMTEXT NOT NULL, snapshot MEDIUMTEXT NOT NULL, changed_at DATETIME NOT NULL,
            PRIMARY KEY (id), KEY idx_history_parcel (parcel_id, id),
            CONSTRAINT fk_history_parcel FOREIGN KEY (parcel_id) REFERENCES parcels (id) ON DELETE CASCADE
        ) ENGINE=InnoDB");
    }

    // parcels saved before history existed get a starting entry, so the timeline never begins empty
    $missing = $pdo->query('SELECT p.* FROM parcels p WHERE NOT EXISTS (SELECT 1 FROM parcel_history h WHERE h.parcel_id = p.id)')
        ->fetchAll(PDO::FETCH_ASSOC);
    if ($missing) {
        $ins = $pdo->prepare('INSERT INTO parcel_history (parcel_id, farmer_id, event, changes, snapshot, changed_at) VALUES (?, ?, ?, ?, ?, ?)');
        foreach ($missing as $p) {
            $snap = [
                'cadastral_lot_no' => $p['cadastral_lot_no'], 'tax_declarant' => $p['tax_declarant'], 'parcel_status' => $p['parcel_status'],
                'area_ha' => (float) $p['area_ha'], 'tax_declared_area_ha' => (float) $p['tax_declared_area_ha'],
                'farming_role' => $p['farming_role'], 'commodity' => $p['commodity'], 'association' => $p['association'],
                'location' => implode(', ', array_filter([$p['loc_zone_sitio'], $p['loc_barangay'], $p['loc_municipality'], $p['loc_province']], 'strlen')),
            ];
            $ins->execute([$p['id'], $p['farmer_id'], 'Existing record', '[]', json_encode($snap, JSON_UNESCAPED_UNICODE), $p['updated_at']]);
        }
        $done[] = 'started history for ' . count($missing) . ' existing parcel(s)';
    }
    return $done;
}
