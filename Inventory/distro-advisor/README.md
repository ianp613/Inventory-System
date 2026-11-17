# Linux Install Advisor

A PHP + JavaScript web app that suggests a partition layout and install steps for 39 Linux distributions, based on your computer's specs.

No AI and no database. A rules engine in PHP produces the plan, so the same input always gives the same result.

Copyright (c) 2026 DDPO Tech. All rights reserved. Vibe coded with Claude AI.

## Run it

Requires PHP 8.1 or newer.

```
php -S localhost:8000 -t public
```

Open http://localhost:8000. For a real server, point Apache or Nginx at the `public/` folder only. `src/` and `data/` should stay outside the web root.

## Layout

```
public/index.php        form page (distro list is built from the data files)
public/api.php          POST JSON -> plan JSON
public/assets/          app.js, style.css
src/Planner.php         rules engine (swap sizing, partitions, checks, commands)
data/families.json      6 family profiles (RHEL, Debian, Arch, SUSE, independent, generic)
data/distros.json       the 39 distributions
data/versions.json      per-version overrides (Ubuntu, Debian, Fedora, RHEL family, openSUSE, Mint)
tests/run.php           php tests/run.php  (273 generated plans + input validation)
```

## Versions

Distros that have meaningful versions get a Version dropdown (Ubuntu LTS and interim releases, Ubuntu Server, Debian stable and oldstable, Fedora, RHEL/Alma/Rocky/Oracle 8-10, openSUSE Leap and Tumbleweed, Linux Mint). Other distros are rolling or have one entry.

Each version in `data/versions.json` only lists what differs from its distro: minimum RAM and disk, architectures, installer and steps (`overrides`), extra `notes`, release and end-of-support dates, and `sources`. The planner works out from the real date whether a version is supported, in limited (LTS/maintenance) support, ending within 90 days, or ended, and warns accordingly, so the warnings stay correct as time passes. Support dates still need refreshing when new releases ship.

`verified: true` on a version means it was checked on the `checked` date against the listed `sources`, for the fields named in `checked_note`. Versions marked `false` are from general knowledge. Fedora's end dates are approximate because sources disagree by a few days.

## Adding or fixing a distro

Edit `data/distros.json`. An entry only needs the fields that differ from its family. Use `layout` to override family defaults (EFI size, swap style, default filesystem, root size per use case) and `install_steps` / `steps` to replace the family's steps. Then run `php tests/run.php`.

## Data accuracy

Every distro entry has `"verified": false`. The requirements and notes were written from general knowledge and have not been checked against each distribution's current official documentation. The UI shows a warning for unverified entries. After you check an entry against the official docs, set `"verified": true` to remove the warning.

Entries to double-check first: Navy Linux (almost no data), Clear Linux and Scientific Linux (marked discontinued), and the minimum RAM/disk numbers for every distro.

## Safety

- Whole-disk commands are only shown when no other system is on the disk. Dual boot never produces partitioning commands.
- The device name is validated (`/dev/` plus letters and digits only) before it appears in any command.
- The page builds all result text with `textContent`, never `innerHTML`.
