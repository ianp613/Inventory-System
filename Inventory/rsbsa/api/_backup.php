<?php
/**
 * Backup and restore (SQLite). A backup is one .zip file holding:
 *   manifest.json                  what it is and when it was made
 *   database/rsbsa.sqlite          a consistent copy of the whole database
 *   files/<folder>/...             the uploaded photos, IDs, tax declarations and supporting documents
 * Backups are kept in the backup folder (default: storage/backups, never public; downloaded through api/backup_download.php).
 */

const BACKUP_APP = 'palo-rsbsa';
const BACKUP_FILE_DIRS = ['photos', 'valid_ids', 'tax_declarations', 'supporting_docs'];
const BACKUP_FREQUENCIES = ['daily' => 86400, 'weekly' => 604800];
const BACKUP_KINDS = ['manual', 'auto', 'before-restore'];

function backup_is_sqlite(): bool
{
    return db()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite';
}

function backup_require_supported(): void
{
    if (!backup_is_sqlite()) {
        fail(400, 'Backup from this page works with the default SQLite database only. For MySQL, back up with mysqldump (see the README).');
    }
    if (!class_exists('ZipArchive')) {
        fail(500, 'The PHP "zip" extension is not enabled, so backups cannot be made. Enable extension=zip in php.ini.');
    }
}

function backup_dir(): string
{
    $dir = (string) cfg('backup_dir', '');
    if ($dir === '') {
        $dir = storage_dir('backups');
    } elseif (!is_dir($dir)) {
        @mkdir($dir, 0775, true);
    }
    if (!is_dir($dir) || !is_writable($dir)) {
        fail(500, 'The backup folder is not writable: ' . $dir);
    }
    return rtrim($dir, '/\\');
}

function backup_settings_path(): string
{
    return backup_dir() . '/settings.json';
}

function backup_settings(): array
{
    $defaults = ['auto' => true, 'frequency' => 'daily', 'keep' => 14, 'last_auto_try' => 0];
    $raw = @file_get_contents(backup_settings_path());
    $s = $raw ? json_decode($raw, true) : null;
    $s = is_array($s) ? array_merge($defaults, $s) : $defaults;
    $s['auto'] = (bool) $s['auto'];
    $s['frequency'] = isset(BACKUP_FREQUENCIES[$s['frequency']]) ? $s['frequency'] : 'daily';
    $s['keep'] = max(1, min(60, (int) $s['keep']));
    return $s;
}

function backup_save_settings(array $s): void
{
    file_put_contents(backup_settings_path(), json_encode($s), LOCK_EX);
}

function backup_db_path(): string
{
    $row = db()->query('PRAGMA database_list')->fetch();
    return (string) ($row['file'] ?? '');
}

function backup_valid_name(string $name): bool
{
    return preg_match('/^rsbsa-backup-\d{8}-\d{6}-(manual|auto|before-restore)\.zip$/', $name) === 1;
}

function backup_path(string $name): string
{
    if (!backup_valid_name($name)) {
        fail(404, 'Backup not found.');
    }
    $path = backup_dir() . '/' . $name;
    if (!is_file($path)) {
        fail(404, 'Backup not found.');
    }
    return $path;
}

/** @return array{name:string,kind:string,size:int,created_at:string,time:int}[] newest first */
function backup_list(): array
{
    $out = [];
    foreach (glob(backup_dir() . '/rsbsa-backup-*.zip') ?: [] as $f) {
        $name = basename($f);
        if (!backup_valid_name($name)) {
            continue;
        }
        preg_match('/-(\d{8})-(\d{6})-(manual|auto|before-restore)\.zip$/', $name, $m);
        $t = DateTime::createFromFormat('Ymd His', $m[1] . ' ' . $m[2]);
        $out[] = [
            'name'       => $name,
            'kind'       => $m[3],
            'size'       => (int) filesize($f),
            'created_at' => $t ? $t->format('Y-m-d H:i:s') : '',
            'time'       => $t ? $t->getTimestamp() : (int) filemtime($f),
        ];
    }
    usort($out, fn($a, $b) => $b['time'] <=> $a['time'] ?: strcmp($b['name'], $a['name']));
    return $out;
}

function backup_rmtree(string $dir): void
{
    if (!is_dir($dir)) {
        return;
    }
    $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
    foreach ($it as $f) {
        $f->isDir() && !$f->isLink() ? @rmdir($f->getPathname()) : @unlink($f->getPathname());
    }
    @rmdir($dir);
}

/** Make a backup zip in the backup folder and return its listing row. */
function backup_create(string $kind): array
{
    backup_require_supported();
    if (!in_array($kind, BACKUP_KINDS, true)) {
        $kind = 'manual';
    }
    @set_time_limit(300);
    $dir = backup_dir();
    $tmpDb = $dir . '/.tmp-' . bin2hex(random_bytes(6)) . '.sqlite';
    $tmpZip = $dir . '/.tmp-' . bin2hex(random_bytes(6)) . '.zip';

    try {
        // consistent snapshot even while the system is in use
        db()->exec('VACUUM INTO ' . db()->quote($tmpDb));

        $counts = [];
        foreach (['farmers', 'parcels', 'duplicate_flags', 'activity_log'] as $t) {
            $counts[$t] = (int) q_val("SELECT COUNT(*) FROM $t");
        }

        $zip = new ZipArchive();
        if ($zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('Could not create the backup file.');
        }
        $files = 0;
        $zip->addFile($tmpDb, 'database/rsbsa.sqlite');
        foreach (BACKUP_FILE_DIRS as $sub) {
            $base = storage_dir($sub);
            if (!is_dir($base)) {
                continue;
            }
            foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($base, FilesystemIterator::SKIP_DOTS)) as $f) {
                if ($f->isFile() && !$f->isLink()) {
                    $rel = str_replace('\\', '/', substr($f->getPathname(), strlen($base) + 1));
                    $zip->addFile($f->getPathname(), "files/$sub/$rel");
                    $files++;
                }
            }
        }
        $zip->addFromString('manifest.json', json_encode([
            'app'        => BACKUP_APP,
            'format'     => 1,
            'kind'       => $kind,
            'created_at' => now(),
            'database'   => 'sqlite',
            'counts'     => $counts + ['files' => $files],
        ], JSON_PRETTY_PRINT));
        if (!$zip->close()) {
            throw new RuntimeException('Could not finish writing the backup file.');
        }

        $name = 'rsbsa-backup-' . date('Ymd-His') . '-' . $kind . '.zip';
        $final = $dir . '/' . $name;
        for ($i = 2; is_file($final); $i++) {   // two backups in the same second
            $name = 'rsbsa-backup-' . date('Ymd-His', time() + $i - 1) . '-' . $kind . '.zip';
            $final = $dir . '/' . $name;
        }
        if (!@rename($tmpZip, $final)) {
            throw new RuntimeException('Could not save the backup file.');
        }
    } finally {
        @unlink($tmpDb);
        @unlink($tmpZip);
    }

    foreach (backup_list() as $b) {
        if ($b['name'] === $name) {
            return $b;
        }
    }
    return ['name' => $name, 'kind' => $kind, 'size' => (int) filesize($final), 'created_at' => now(), 'time' => time()];
}

/** Delete the oldest automatic backups beyond the number to keep (manual and safety backups are never removed automatically). */
function backup_prune(): int
{
    $keep = backup_settings()['keep'];
    $auto = array_values(array_filter(backup_list(), fn($b) => $b['kind'] === 'auto'));
    $removed = 0;
    foreach (array_slice($auto, $keep) as $b) {
        if (@unlink(backup_dir() . '/' . $b['name'])) {
            $removed++;
        }
    }
    return $removed;
}

/** Make an automatic backup when one is due. Called after login and on the dashboard; never throws. */
function backup_auto_if_due(): ?array
{
    try {
        if (!backup_is_sqlite() || !class_exists('ZipArchive')) {
            return null;
        }
        $s = backup_settings();
        if (!$s['auto']) {
            return null;
        }
        $period = BACKUP_FREQUENCIES[$s['frequency']];
        if (time() - (int) $s['last_auto_try'] < 600) {   // do not retry within 10 minutes (a failing disk would otherwise retry on every page)
            return null;
        }
        $last = backup_list()[0] ?? null;               // newest backup of any kind counts
        if ($last && time() - $last['time'] < $period) {
            return null;
        }
        $s['last_auto_try'] = time();
        backup_save_settings($s);
        $b = backup_create('auto');
        $pruned = backup_prune();
        log_activity('System', 'Automatic backup', $b['name'] . ' (' . backup_human_size($b['size']) . ')' . ($pruned ? ", removed $pruned old automatic backup(s)" : ''));
        return $b;
    } catch (Throwable $e) {
        error_log('[RSBSA] auto backup failed: ' . $e->getMessage());
        try {
            log_activity('System', 'Automatic backup failed', mb_substr($e->getMessage(), 0, 300));
        } catch (Throwable $e2) {
        }
        return null;
    }
}

function backup_human_size(int $bytes): string
{
    if ($bytes >= 1048576) {
        return round($bytes / 1048576, 1) . ' MB';
    }
    return max(1, (int) round($bytes / 1024)) . ' KB';
}

/**
 * Check a backup zip without changing anything. Returns [manifest, ZipArchive-opened-path].
 * Fails with a clear message when the file is not a Palo RSBSA backup.
 */
function backup_inspect(string $zipPath): array
{
    $zip = new ZipArchive();
    if ($zip->open($zipPath) !== true) {
        fail(422, 'This file is not a valid backup (it cannot be opened as a zip file).');
    }
    $manifest = json_decode((string) $zip->getFromName('manifest.json'), true);
    if (!is_array($manifest) || ($manifest['app'] ?? '') !== BACKUP_APP) {
        $zip->close();
        fail(422, 'This file is not a Palo RSBSA backup.');
    }
    if ($zip->locateName('database/rsbsa.sqlite') === false) {
        $zip->close();
        fail(422, 'This backup does not contain the database.');
    }
    for ($i = 0; $i < $zip->numFiles; $i++) {
        $n = (string) $zip->getNameIndex($i);
        $ok = $n === 'manifest.json' || $n === 'database/rsbsa.sqlite' || substr($n, -1) === '/'
            || (preg_match('#^files/(' . implode('|', BACKUP_FILE_DIRS) . ')/[A-Za-z0-9._/-]+$#', $n) === 1 && strpos($n, '..') === false);
        if (!$ok) {
            $zip->close();
            fail(422, 'This backup contains unexpected files and was not restored.');
        }
    }
    $zip->close();
    return $manifest;
}

/**
 * Replace everything with the contents of a backup. All-or-nothing for the database; files are swapped in after.
 * Returns a short description of what was restored.
 */
function backup_restore_from(string $zipPath): array
{
    backup_require_supported();
    @set_time_limit(600);
    $manifest = backup_inspect($zipPath);

    $work = backup_dir() . '/.restore-' . bin2hex(random_bytes(6));
    mkdir($work, 0775, true);
    try {
        $zip = new ZipArchive();
        $zip->open($zipPath);
        if (!$zip->extractTo($work)) {
            throw new RuntimeException('Could not unpack the backup.');
        }
        $zip->close();

        $src = $work . '/database/rsbsa.sqlite';
        $chk = new PDO('sqlite:' . $src);
        $chk->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        if ($chk->query('PRAGMA integrity_check')->fetchColumn() !== 'ok') {
            fail(422, 'The database inside this backup is damaged, so nothing was restored.');
        }
        $have = array_column($chk->query("SELECT name FROM sqlite_master WHERE type = 'table'")->fetchAll(), 'name');
        foreach (['admins', 'farmers', 'parcels', 'duplicate_flags'] as $t) {
            if (!in_array($t, $have, true)) {
                fail(422, 'The database inside this backup is not a Palo RSBSA database, so nothing was restored.');
            }
        }
        $chk = null;

        // safety copy of the current data first, so a wrong restore can be undone
        $safety = backup_create('before-restore');

        $pdo = db();
        $pdo->exec('ATTACH DATABASE ' . $pdo->quote($src) . ' AS restored');
        $pdo->exec('PRAGMA foreign_keys = OFF');
        try {
            $pdo->beginTransaction();
            $tables = array_column($pdo->query("SELECT name FROM main.sqlite_master WHERE type = 'table' AND name NOT LIKE 'sqlite_%'")->fetchAll(), 'name');
            $theirs = array_column($pdo->query("SELECT name FROM restored.sqlite_master WHERE type = 'table'")->fetchAll(), 'name');
            foreach ($tables as $t) {
                $pdo->exec("DELETE FROM main.\"$t\"");
                if (!in_array($t, $theirs, true)) {
                    continue;   // table added in a newer version: stays empty
                }
                $mine = array_column($pdo->query("PRAGMA main.table_info(\"$t\")")->fetchAll(), 'name');
                $them = array_column($pdo->query("PRAGMA restored.table_info(\"$t\")")->fetchAll(), 'name');
                $cols = array_values(array_intersect($mine, $them));
                if ($cols) {
                    $list = '"' . implode('","', $cols) . '"';
                    $pdo->exec("INSERT INTO main.\"$t\" ($list) SELECT $list FROM restored.\"$t\"");
                }
            }
            $fk = $pdo->query('PRAGMA main.foreign_key_check')->fetchAll();
            if ($fk) {
                throw new RuntimeException('The backup has records that point to missing records.');
            }
            $pdo->commit();
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $pdo->exec('PRAGMA foreign_keys = ON');
            $pdo->exec('DETACH DATABASE restored');
            throw $e;
        }
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('DETACH DATABASE restored');

        // bring an older backup up to the current structure (adds new columns / history entries)
        require_once dirname(__DIR__) . '/database/_migrate.php';
        rsbsa_migrate($pdo);

        // uploaded files: replace each folder with the one from the backup
        foreach (BACKUP_FILE_DIRS as $sub) {
            $live = storage_dir($sub);
            $new = $work . '/files/' . $sub;
            $old = $live . '.old-' . bin2hex(random_bytes(3));
            if (!@rename($live, $old)) {
                // folder in use: fall back to emptying it
                backup_rmtree($live);
                @mkdir($live, 0775, true);
                $old = null;
            }
            if (is_dir($new)) {
                if (!@rename($new, $live)) {
                    @mkdir($live, 0775, true);
                    foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($new, FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST) as $f) {
                        $to = $live . '/' . substr($f->getPathname(), strlen($new) + 1);
                        $f->isDir() ? @mkdir($to, 0775, true) : @copy($f->getPathname(), $to);
                    }
                }
            } elseif (!is_dir($live)) {
                @mkdir($live, 0775, true);
            }
            if ($old) {
                backup_rmtree($old);
            }
        }
    } finally {
        backup_rmtree($work);
    }

    return ['manifest' => $manifest, 'safety' => $safety];
}
