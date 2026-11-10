<?php
declare(strict_types=1);

final class Auth
{
    /** Privileges that can see everything under storage_location. */
    private const FULL_ACCESS = ['administrator', 'supervisor'];

    public static function attempt(string $username, string $password): ?array
    {
        $st = db()->prepare('SELECT id, name, privileges, username, password FROM `user` WHERE username = ? LIMIT 1');
        $st->execute([$username]);
        $row = $st->fetch();
        if (!$row || !password_verify($password, $row['password'])) {
            return null;
        }
        return $row;
    }

    public static function hasFullAccess(string $privileges): bool
    {
        return in_array(strtolower(trim($privileges)), self::FULL_ACCESS, true);
    }

    /**
     * First group (lowest id) where the user's id appears in `supervisors` or `users`
     * (ids are separated by "|"). Ids are compared exactly, never as substrings.
     */
    public static function findGroup(int $userId): ?array
    {
        $groups = db()->query('SELECT id, group_name, supervisors, users FROM user_group ORDER BY id')->fetchAll();
        foreach ($groups as $g) {
            $ids = array_merge(explode('|', (string)$g['supervisors']), explode('|', (string)$g['users']));
            foreach ($ids as $id) {
                $id = trim($id);
                if ($id !== '' && ctype_digit($id) && (int)$id === $userId) {
                    return $g;
                }
            }
        }
        return null;
    }

    /**
     * Decide the user's storage root. Privilege check comes first:
     *  - Administrator / Supervisor => storage_location itself
     *  - anyone else => storage_location/<group name>, created if missing
     * Returns [absoluteRoot, label] or throws RuntimeException.
     */
    public static function resolveRoot(array $user): array
    {
        $base = rtrim(str_replace('\\', '/', (string)config()['storage_location']), '/');
        if (!is_dir($base)) {
            throw new RuntimeException('Storage location does not exist: ' . $base);
        }

        if (self::hasFullAccess((string)$user['privileges'])) {
            return [$base, 'All files'];
        }

        $group = self::findGroup((int)$user['id']);
        if ($group === null) {
            throw new RuntimeException('Your account is not assigned to any group. Please contact an administrator.');
        }

        $folder = Storage::sanitizeName((string)$group['group_name']);
        if ($folder === '') {
            throw new RuntimeException('Your group has an invalid name.');
        }

        $path = $base . '/' . $folder;
        if (!is_dir($path) && !@mkdir($path, 0775, true) && !is_dir($path)) {
            throw new RuntimeException('Could not create the group folder.');
        }
        return [$path, $group['group_name']];
    }

    public static function user(): ?array
    {
        return $_SESSION['user'] ?? null;
    }

    public static function storage(): Storage
    {
        return new Storage($_SESSION['root']);
    }
}
