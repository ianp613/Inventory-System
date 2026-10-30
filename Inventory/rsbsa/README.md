# Palo RSBSA

Registry System for Basic Sectors in Agriculture. Single administrator account.
PHP 8.1+ with the extensions pdo_sqlite, fileinfo, mbstring and zip. The database is one SQLite file:
no database server to install.

## Setup

1. Copy this folder to your web server (for example `htdocs/rsbsa`).
2. Create the database and the administrator account (password: at least 8 characters):

       php database/setup.php admin "YourPassword" "Full Name"

   Running it again keeps your data and only resets that account's password.
3. Make sure PHP (the web server user) can write to the `database/` folder and to `storage/`.
4. Open `index.php` and log in.

Optional settings are in `api/config.php`: timezone, upload size limit, login lockout, and the location of the
database file (`db.sqlite_path`). On a live server put the database file outside the public web folder,
for example `/var/data/rsbsa.sqlite`.

## Updating from an older version

Copy the new files over the old ones (keep your database and `storage/`), then run once:

    php database/migrate.php

It adds the new columns and tables (civil status, 2x2 picture, valid ID file, parcel remark, parcel history, activity log). Records are kept. Older
farmers are given a best-guess civil status (Married if a spouse is recorded, Separated if they were marked
separated); check it the next time you update them. The system refuses to run until this is done.

## Backup and restore

Settings > Backup & restore (SQLite + the PHP `zip` extension):

- **Back Up Now** makes one `.zip` with the whole database and every uploaded photo, ID and document, saves it in
  `storage/backups/` and downloads a copy. Keep copies on another drive or flash disk.
- **Automatic backup** (daily or weekly, keeping the last 1-60 automatic backups) runs the first time someone logs in or opens the
  dashboard after it is due, so the computer must be on and the system opened. Manual and safety backups are never removed automatically.
- **Restore** from the saved list or by choosing a `.zip` file. It replaces all current data, asks for your password,
  and first saves a safety copy ("before-restore") so a wrong restore can be undone. You are logged out afterwards.
  Backups made by an older version are brought up to date automatically.
- A very large backup may exceed `upload_max_filesize` / `post_max_size` in php.ini; copy the file into `storage/backups/`
  instead and restore it from the list.
- MySQL: use `mysqldump` and copy the `storage/` folder.

## Files

    index.php            login
    dashboard.php        summary and charts (commodity share, farmers per barangay, registrations per month)
    farmers.php          list, search, details
    register.php         register / update a farmer  (register.php?id=123 updates)
    duplicates.php       duplicate flags review
    reports.php          Excel / CSV export, printable forms
    assets/js/script.js  all front-end JavaScript (endpoint names are in CONFIG at the top)
    assets/css/style.css
    api/                 JSON endpoints used by script.js
    database/schema.sql  tables (SQLite)
    database/setup.php   creates the database and the administrator
    database/create_admin.php   resets the administrator password
    database/schema.mysql.sql   optional MySQL version (set db.driver to 'mysql' in api/config.php)
    storage/             uploaded files (blocked from direct web access, served by api/file.php)

## Settings and password

**Settings** in the left menu has three parts:

- **Your name:** the name shown in the left menu (the login username does not change).
- **Change password** (below).
- **Auto-logout:** turn it on and pick 1, 2, 5, 10, 15, 30 or 60 minutes. After that long without using the
  system a 30-second warning appears (Stay logged in / Log out now), then you are logged out. It is off by default.
- **Theme:** Default (light) or Dark mode. Printing and printed forms always use the default colors.

Auto-logout and theme are saved in the browser on that computer (not in the database), so each computer or
browser keeps its own choice. The server also ends sessions after 120 idle minutes (`session_idle_minutes` in api/config.php).

### Password

- Change it in the system: **Settings > Change password** (current password, then the new one twice,
  at least 8 characters). Nothing is sent by email, so it works fully offline.
- If you forget it, run this on the computer that hosts the system (it resets the password of that account
  and keeps all data):

      php database/setup.php admin "NewPassword" "Full Name"

## Deleting

- **Delete a farmer:** the Delete button on the Farmers list, or Delete Farmer on the Update page.
  It permanently removes the farmer, all parcels, crop and livestock data, children, duplicate flags
  of those parcels and every attached file (tax declarations and supporting documents).
  Flags of OTHER farmers that name the deleted farmer as the registered owner are kept (the owner is shown
  blank) and the page tells you how many to review.
- **Delete a parcel:** on the Update page click Remove Parcel, then Save Changes. The parcel, its duplicate
  flags and all its attached files are deleted. A farmer must keep at least one parcel (delete the farmer instead).
- Replacing a tax declaration also deletes the old file. Deletions cannot be undone, so back up first
  (see Backup).

## Notes

- Parcel history: every saved parcel has a History button (Update page, and the farmer's View window). It lists
  each change newest first (previous value -> new value) for lot number, tax declarant, parcel remark, farming
  status, areas, commodity, association, location and replaced tax declaration files, plus the values at that
  time. Use Print, Export Excel or Export CSV in that window. Parcels that existed before this feature start
  with an "Existing record" entry after `php database/migrate.php`. History is deleted with the parcel.
- Activity Logs (left menu): every action is recorded with the user, date and time, and IP: logins (including
  failed ones), logouts and auto-logouts, farmers registered / updated / deleted / opened, parcels added or removed
  and which fields changed, duplicate flags resolved or reopened, tax declarations / supporting documents / valid IDs
  opened, reports exported, forms and lists printed, and account or settings changes. Search and filter by
  category, user and dates; print or export to Excel / CSV. The system has no way to edit or delete log rows, and
  passwords are never written to it. Old rows keep the user's name as it was at the time. To archive or trim the
  log, work on the database file directly (table `activity_log`) after making a backup.
- Name of Association is saved per parcel.
- Civil status: Single, Married, Widowed, Separated, Annulled. Spouse duplicate rules apply only to Married.
  Single hides the spouse section.
- The 2x2 picture (JPG/PNG) and valid ID file (PDF/JPG/PNG) are required for a new farmer. On update they are
  optional and only replace the old file when a new one is chosen. Both are removed when the farmer is deleted.
- Parcel remark: Active (default), In-active or Deceased. Parcels that are not Active stay on record but their
  area is not counted in the farmer's total, the dashboard totals, or the totals shown in the farmer list.
  The lot rules (shared-lot area limit, duplicate flag) still consider them, because the lot is still registered.
- A parcel is flagged as a duplicate only when the same lot (and barangay) was registered by another
  farmer before it. The flagged farmer must attach supporting documents.
- Rules enforced on the server (api/save_farmer.php): shared-lot total cannot exceed the area on the
  tax declaration; a spouse cannot enroll the same lot or register separately unless the civil status is not Married.
- Names and searches ignore upper/lower case including Ñ/ñ ("PEÑA" = "Peña"). They do not treat "Pena"
  and "Peña" as the same name.
- Failed logins: 5 per IP and username within 15 minutes, then locked for the rest of the window.
- The `.htaccess` files in `database/` and `storage/` block direct downloads on Apache. On Nginx or IIS
  add the equivalent rule, or move both folders outside the web root.
- On a live server use HTTPS.
