<?php
/**
 * Update an existing database after you install a newer version of the system (command line):
 *
 *     php database/migrate.php
 *
 * Adds the new columns and folders. Your records and files are not changed. Safe to run again.
 */
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Run this script from the command line.');
}

define('RSBSA_SKIP_SCHEMA_CHECK', true);
require __DIR__ . '/../api/_bootstrap.php';
require __DIR__ . '/_migrate.php';

try {
    $pdo = db();
    $changes = rsbsa_migrate($pdo);
} catch (Throwable $e) {
    fwrite(STDERR, 'Update failed: ' . $e->getMessage() . "\n");
    exit(1);
}

foreach (['tax_declarations', 'supporting_docs', 'photos', 'valid_ids'] as $dir) {
    storage_dir($dir);
}

echo $changes ? "Updated the database:\n  - " . implode("\n  - ", $changes) . "\n" : "The database is already up to date.\n";
