# File Manager (PHP)

Web file manager that stores files on a local disk (`storage_location` in `config.json`)
and uses your existing `user` / `user_group` tables only for login and access.

## Access rules

| User privileges | What they see |
|---|---|
| `Administrator` or `Supervisor` | everything under `storage_location` |
| anything else | only `storage_location/<group_name>` (created automatically on login if missing) |

- The privilege is checked first; the group folder is only looked up/created for non-admin users.
- A user's group is found by their id appearing in `user_group.supervisors` or `user_group.users`
  (`|`-separated, compared exactly). If a user is in several groups, the lowest group id is used.
- Folder names are sanitised for Windows (`< > : " / \ | ? *` become `_`).

## Install

1. PHP 8.0+ with `pdo_mysql` enabled (argon2id support is built into standard PHP builds).
2. Put this folder where your web server can reach it, e.g. `file-browser`, and edit `config.json` if needed.
3. Open `http://localhost:8889/file-browser` - `index.php` is the login page, and after signing in the same
   address shows the file manager.
4. Make sure `config.json` and `src/` cannot be opened from the browser:
   - Apache (XAMPP): `.htaccess` files are included.
   - IIS: `web.config` is included.
   - PHP built-in server: start it with the router, from the folder that contains `file-browser`:
     `php -S localhost:8889 file-browser/router.php`
5. For big uploads raise `upload_max_filesize` and `post_max_size` in `php.ini`.

## Features

Browse, breadcrumb navigation, upload (button or drag and drop), download, new folder,
rename, delete, copy and move (select items, Copy/Move, open the destination, Paste here),
preview (images, PDF, text), search by name, file sizes and folder sizes (calculated one by one).

## Security notes

- Every path from the browser is validated and must resolve inside the user's root (no `..`, no symlink escapes).
- POST actions require a CSRF token; session id is regenerated at login.
- Files are always downloaded as attachments (only images/PDF/text previews are shown inline).
- Use HTTPS in production. Rotate the DB password in `config.json` if this file is ever shared.
- The root is decided at login; if someone's group changes, they should sign out and in again.
