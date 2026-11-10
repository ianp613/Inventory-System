<?php
declare(strict_types=1);

/**
 * All file operations live here. Every path coming from the browser is a path
 * relative to the user's root; it is validated and checked to stay inside it.
 */
final class Storage
{
    private string $root;

    public function __construct(string $root)
    {
        $real = realpath($root);
        if ($real === false || !is_dir($real)) {
            throw new RuntimeException('Storage root not found.');
        }
        $this->root = rtrim(str_replace('\\', '/', $real), '/');
    }

    /* ---------- path helpers ---------- */

    public static function sanitizeName(string $name): string
    {
        $name = preg_replace('/[<>:"\/\\\\|?*\x00-\x1F]/u', '_', $name) ?? '';
        $name = rtrim(trim($name), '. ');
        if (preg_match('/^(con|prn|aux|nul|com[1-9]|lpt[1-9])(\..*)?$/i', $name)) {
            $name = '_' . $name;
        }
        return $name;
    }

    public function clean(string $rel): string
    {
        if (strpos($rel, "\0") !== false) {
            throw new RuntimeException('Invalid path.');
        }
        $out = [];
        foreach (explode('/', str_replace('\\', '/', $rel)) as $seg) {
            if ($seg === '' || $seg === '.') {
                continue;
            }
            if ($seg === '..' || $seg !== self::sanitizeName($seg)) {
                throw new RuntimeException('Invalid path.');
            }
            $out[] = $seg;
        }
        return implode('/', $out);
    }

    private function norm(string $p): string
    {
        $p = str_replace('\\', '/', $p);
        return DIRECTORY_SEPARATOR === '\\' ? strtolower($p) : $p;
    }

    private function inside(string $abs): bool
    {
        $real = (file_exists($abs) || is_link($abs)) ? realpath($abs) : realpath(dirname($abs));
        if ($real === false) {
            return false;
        }
        $p = $this->norm($real);
        $r = $this->norm($this->root);
        return $p === $r || strpos($p, $r . '/') === 0;
    }

    private function abs(string $rel, bool $mustExist = true): string
    {
        $rel = $this->clean($rel);
        $abs = $rel === '' ? $this->root : $this->root . '/' . $rel;
        if (!$this->inside($abs)) {
            throw new RuntimeException('Access denied.');
        }
        if ($mustExist && !file_exists($abs)) {
            throw new RuntimeException('Not found.');
        }
        return $abs;
    }

    public function absFile(string $rel): string
    {
        $abs = $this->abs($rel);
        if (!is_file($abs)) {
            throw new RuntimeException('Not a file.');
        }
        return $abs;
    }

    private function relOf(string $abs): string
    {
        $abs = str_replace('\\', '/', $abs);
        return ltrim(substr($abs, strlen($this->root)), '/');
    }

    private function uniqueName(string $dirAbs, string $name, bool $isDir, string $suffix = ' - copy'): string
    {
        if (!file_exists($dirAbs . '/' . $name)) {
            return $name;
        }
        $ext = '';
        $base = $name;
        if (!$isDir && ($dot = strrpos($name, '.')) !== false && $dot > 0) {
            $base = substr($name, 0, $dot);
            $ext = substr($name, $dot);
        }
        for ($i = 1; $i < 10000; $i++) {
            $cand = $base . $suffix . ($i > 1 ? ' ' . $i : '') . $ext;
            if (!file_exists($dirAbs . '/' . $cand)) {
                return $cand;
            }
        }
        throw new RuntimeException('Could not find a free name.');
    }

    private function item(string $abs, string $name): array
    {
        $isDir = is_dir($abs);
        return [
            'name' => $name,
            'path' => $this->relOf($abs),
            'type' => $isDir ? 'dir' : 'file',
            'size' => $isDir ? null : (int)@filesize($abs),
            'modified' => (int)@filemtime($abs),
            'ext' => $isDir ? '' : strtolower(pathinfo($name, PATHINFO_EXTENSION)),
        ];
    }

    /* ---------- operations ---------- */

    public function list(string $rel): array
    {
        $dir = $this->abs($rel);
        if (!is_dir($dir)) {
            throw new RuntimeException('Not a folder.');
        }
        $items = [];
        foreach (scandir($dir) ?: [] as $n) {
            if ($n === '.' || $n === '..') {
                continue;
            }
            $items[] = $this->item($dir . '/' . $n, $n);
        }
        usort($items, static function ($a, $b) {
            if ($a['type'] !== $b['type']) {
                return $a['type'] === 'dir' ? -1 : 1;
            }
            return strnatcasecmp($a['name'], $b['name']);
        });
        return $items;
    }

    public function folderSize(string $rel): array
    {
        $dir = $this->abs($rel);
        if (!is_dir($dir)) {
            throw new RuntimeException('Not a folder.');
        }
        $size = 0;
        $count = 0;
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::LEAVES_ONLY,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        foreach ($it as $f) {
            if ($f->isFile()) {
                $size += $f->getSize();
                $count++;
            }
        }
        return ['size' => $size, 'files' => $count];
    }

    public function search(string $rel, string $q, int $limit = 200): array
    {
        $q = trim($q);
        if ($q === '') {
            return [];
        }
        $dir = $this->abs($rel);
        $res = [];
        $it = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST,
            RecursiveIteratorIterator::CATCH_GET_CHILD
        );
        foreach ($it as $f) {
            if (mb_stripos($f->getFilename(), $q) !== false) {
                $res[] = $this->item($f->getPathname(), $f->getFilename());
                if (count($res) >= $limit) {
                    break;
                }
            }
        }
        return $res;
    }

    public function mkdir(string $rel, string $name): void
    {
        $dir = $this->abs($rel);
        $name = self::sanitizeName($name);
        if ($name === '') {
            throw new RuntimeException('Invalid folder name.');
        }
        if (file_exists($dir . '/' . $name)) {
            throw new RuntimeException('A file or folder with that name already exists.');
        }
        if (!@mkdir($dir . '/' . $name, 0775)) {
            throw new RuntimeException('Could not create the folder.');
        }
    }

    public function rename(string $rel, string $newName): void
    {
        if ($this->clean($rel) === '') {
            throw new RuntimeException('The root folder cannot be renamed.');
        }
        $src = $this->abs($rel);
        $newName = self::sanitizeName($newName);
        if ($newName === '') {
            throw new RuntimeException('Invalid name.');
        }
        $dst = dirname($src) . '/' . $newName;
        if (file_exists($dst) && $this->norm($dst) !== $this->norm($src)) {
            throw new RuntimeException('A file or folder with that name already exists.');
        }
        if (!@rename($src, $dst)) {
            throw new RuntimeException('Rename failed.');
        }
    }

    public function delete(string $rel): void
    {
        if ($this->clean($rel) === '') {
            throw new RuntimeException('The root folder cannot be deleted.');
        }
        $abs = $this->abs($rel);
        if (is_link($abs) || is_file($abs)) {
            if (!@unlink($abs) && !@rmdir($abs)) {
                throw new RuntimeException('Delete failed.');
            }
            return;
        }
        $this->rrmdir($abs);
    }

    private function rrmdir(string $dir): void
    {
        foreach (new DirectoryIterator($dir) as $f) {
            if ($f->isDot()) {
                continue;
            }
            $p = $f->getPathname();
            if ($f->isDir() && !$f->isLink()) {
                $this->rrmdir($p);
            } elseif (!@unlink($p) && !@rmdir($p)) {
                throw new RuntimeException('Could not delete ' . $f->getFilename());
            }
        }
        if (!@rmdir($dir)) {
            throw new RuntimeException('Could not delete folder.');
        }
    }

    private function intoSelf(string $src, string $destDir): bool
    {
        $s = $this->norm(realpath($src) ?: $src);
        $d = $this->norm(realpath($destDir) ?: $destDir);
        return $d === $s || strpos($d, $s . '/') === 0;
    }

    public function copy(string $rel, string $destRel): string
    {
        if ($this->clean($rel) === '') {
            throw new RuntimeException('The root folder cannot be copied.');
        }
        $src = $this->abs($rel);
        $destDir = $this->abs($destRel);
        if (!is_dir($destDir)) {
            throw new RuntimeException('Destination is not a folder.');
        }
        $isDir = is_dir($src);
        if ($isDir && $this->intoSelf($src, $destDir)) {
            throw new RuntimeException('A folder cannot be copied into itself.');
        }
        $name = $this->uniqueName($destDir, basename($src), $isDir);
        $dst = $destDir . '/' . $name;
        if ($isDir) {
            $this->copyDir($src, $dst);
        } elseif (!@copy($src, $dst)) {
            throw new RuntimeException('Copy failed.');
        }
        return $name;
    }

    private function copyDir(string $src, string $dst): void
    {
        if (!@mkdir($dst, 0775)) {
            throw new RuntimeException('Could not create ' . basename($dst));
        }
        foreach (new DirectoryIterator($src) as $f) {
            if ($f->isDot() || $f->isLink()) {
                continue;
            }
            $to = $dst . '/' . $f->getFilename();
            if ($f->isDir()) {
                $this->copyDir($f->getPathname(), $to);
            } elseif (!@copy($f->getPathname(), $to)) {
                throw new RuntimeException('Could not copy ' . $f->getFilename());
            }
        }
    }

    public function move(string $rel, string $destRel): void
    {
        if ($this->clean($rel) === '') {
            throw new RuntimeException('The root folder cannot be moved.');
        }
        $src = $this->abs($rel);
        $destDir = $this->abs($destRel);
        if (!is_dir($destDir)) {
            throw new RuntimeException('Destination is not a folder.');
        }
        if (is_dir($src) && $this->intoSelf($src, $destDir)) {
            throw new RuntimeException('A folder cannot be moved into itself.');
        }
        $dst = $destDir . '/' . basename($src);
        if ($this->norm($dst) === $this->norm($src)) {
            return; // already there
        }
        if (file_exists($dst)) {
            throw new RuntimeException('"' . basename($src) . '" already exists in the destination.');
        }
        if (!@rename($src, $dst)) {
            throw new RuntimeException('Move failed.');
        }
    }

    public function upload(string $rel, array $files): array
    {
        $dir = $this->abs($rel);
        if (!is_dir($dir)) {
            throw new RuntimeException('Destination is not a folder.');
        }
        $saved = [];
        $names = $files['name'] ?? [];
        if (!is_array($names)) {
            foreach (['name', 'tmp_name', 'error'] as $k) {
                $files[$k] = [$files[$k]];
            }
            $names = $files['name'];
        }
        foreach ($names as $i => $orig) {
            $err = $files['error'][$i];
            if ($err !== UPLOAD_ERR_OK) {
                throw new RuntimeException($err === UPLOAD_ERR_INI_SIZE || $err === UPLOAD_ERR_FORM_SIZE
                    ? '"' . $orig . '" is larger than the server upload limit.'
                    : 'Upload failed for "' . $orig . '".');
            }
            $name = self::sanitizeName(basename(str_replace('\\', '/', (string)$orig)));
            if ($name === '') {
                throw new RuntimeException('Invalid file name.');
            }
            $name = $this->uniqueName($dir, $name, false, ' (copy)');
            if (!move_uploaded_file($files['tmp_name'][$i], $dir . '/' . $name)) {
                throw new RuntimeException('Could not save "' . $orig . '".');
            }
            $saved[] = $name;
        }
        return $saved;
    }
}
