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

Browse, breadcrumb navigation, upload files, **upload folders** (button or drag and drop, subfolders and
empty folders included), download, **download folders and multiple selections**, new folder, rename, delete,
copy and move (select items, Copy/Move, open the destination, Paste here), preview (images, PDF, text),
search by name, file sizes and folder sizes (calculated one by one).

- Uploads are sent in 5 MB chunks (3 files at a time), so large files do not hit proxy time limits.
- Downloading **one file** gives you that file as is. Downloading **a folder or several items** gives one ZIP.
- ZIPs are streamed while they are created (stored without compression, ZIP64 for very large data), so a big
  folder starts downloading immediately and nothing is written to the server's disk.
- Symbolic links are skipped when zipping, and files that cannot be read are left out of the ZIP.
- If a folder with the same name already exists when you upload a folder, the contents are merged into it;
  files that already exist are kept and the new ones are saved as `name (copy).ext`.

## Video streaming

Double-click (or tap) a video to play it in the browser. Supported containers: `mp4`, `m4v`, `webm`, `ogv`, `mov`, `mkv`.
Whether a file plays depends on the browser's codecs (H.264/AAC mp4 and VP8/VP9 webm play everywhere; HEVC, AVI or
WMV files will show a message with a Download button instead).

- The server supports HTTP Range requests, so playback starts immediately and you can seek anywhere without
  downloading the whole file. Downloads of any file are resumable too.
- Controls: play/pause, back/forward 10 seconds, seek bar (shows what is buffered), volume and mute, speed
  (0.5x - 3x), picture-in-picture and fullscreen.
- **Previous / Next:** plays the neighbouring video of the same folder (natural name order, so `ep2` comes before `ep10`)
  without closing the player, and fullscreen stays on. After the last video, Next goes back to the first one;
  Previous on the first video goes to the last. Disabled when the folder has only one video.
- Keys: `N` next, `P` previous, `Space`/`K` play-pause, `Left`/`J` back 10 s, `Right`/`L` forward 10 s, `Up`/`Down` volume, `M` mute,
  `F` fullscreen, `<` `>` speed.
- The last video and the time you stopped at are saved in the browser's local storage (per user, per video, up to
  200 videos). Opening the video again resumes from there ("Start over" is offered), and a "Continue watching"
  bar appears at the top. A video watched to the end is forgotten. Volume and speed are remembered too.
  Because this is local storage, it belongs to that browser on that device.

## Phones and tablets

The layout adapts to laptops and phones: on small screens the file table becomes a list of cards, and a single tap
opens a folder, file or video (use the checkbox to select items).

## Security notes

- Every path from the browser is validated and must resolve inside the user's root (no `..`, no symlink escapes).
- POST actions require a CSRF token; session id is regenerated at login.
- Files are always downloaded as attachments (only images/PDF/text previews are shown inline).
- Use HTTPS in production. Rotate the DB password in `config.json` if this file is ever shared.
- The root is decided at login; if someone's group changes, they should sign out and in again.
