# G5 E-Learning

A small PHP + MySQL (PDO) e-learning platform with three roles: **admin**,
**trainer**, and **student**. No framework — a hand-rolled MVC with Composer
PSR-4 autoloading behind a single front controller.

> **Migration in progress.** The app is moving from the original
> `require`-based `app/` layout to the standard structure below. See
> [MIGRATION.md](MIGRATION.md) for what's done, the full route map, and the
> old-function → new-class reference. The legacy `app/` directory is kept as a
> reference until every route is migrated.

## Directory structure

```
g5-Elearning/
├── composer.json           ← PSR-4: App\ => src/  (+ src/Core/helpers.php)
├── public/                 ← web root (point your server's document root here)
│   ├── index.php           ← bootstrap: autoload → session → Router::dispatch
│   ├── .htaccess           ← rewrites non-file requests to index.php
│   ├── assets/ vendor/ uploading/ studentprofile/ trainerprofile/
├── routes/
│   └── web.php             ← route table: path → [Controller::class, 'action']
├── src/                    ← application code (App\ namespace, NOT web-accessible)
│   ├── Core/               ← Database, Router, Controller, View, Session, Auth,
│   │                          Validation, helpers.php
│   ├── Controllers/        ← App\Controllers\* request handlers
│   └── Models/             ← App\Models\* PDO query classes
├── resources/
│   └── views/              ← page templates (*.view.php) + layouts/{public,admin}
├── config/
│   └── database.php        ← DB credentials (reads env vars, has defaults)
├── database/
│   └── learning.sql        ← schema + seed data
├── tests/                  ← PHPUnit (AuthTest guards the password fix)
├── app/                    ← LEGACY reference (being removed as migration lands)
└── .env.example            ← copy to .env and set your DB credentials
```

## How routing works

`public/index.php` is the only entry point. It:

1. defines `BASE_PATH` and loads Composer's PSR-4 autoloader,
2. `chdir()`s into `public/` so uploads are written under the web root,
3. starts the session,
4. hands the request to the `Router` built in `routes/web.php`, which maps a
   path + HTTP method to a `[Controller::class, 'action']` pair.

Each controller action chooses its own layout: `$this->public(...)` wraps the
public site chrome, `$this->admin(...)` wraps the admin dashboard, and
`$this->view(...)` renders standalone (auth screens, redirecting form handlers).

To add a page: create a controller under `src/Controllers/…`, then register one
route in `routes/web.php`.

## Local setup

```bash
# 1. Install PHP dependencies (autoloader + PHPUnit)
composer install

# 2. Create the database and import the schema
mysql -u root -e "CREATE DATABASE e_learning_db"
mysql -u root e_learning_db < database/learning.sql

# 3. Configure credentials (optional — defaults are root / no password)
cp .env.example .env        # then edit, and export the vars before serving

# 4. Serve with the document root pointed at public/
php -S localhost:8000 -t public
```

Then open <http://localhost:8000/>.

## Tests

```bash
composer install            # once, to fetch PHPUnit
vendor/bin/phpunit
```

`tests/AuthTest.php` guards the password-handling fix (hash on write, verify on
read, legacy plaintext still authenticates once).

## Fixed during the restructure

- **Password handling.** New accounts are hashed with `password_hash()`
  (`App\Core\Auth::hash`) and verified with `password_verify()`
  (`Auth::verify`). The old "re-hash the stored value then verify" trick is
  gone. Existing plaintext rows still authenticate once and are flagged by
  `Auth::needsRehash()` for transparent upgrade. Covered by `tests/AuthTest.php`.
- **Colliding model functions** (identical `accountExist`, `payments`,
  `getStudent`, the duplicated `lesson.mode.php`/`manage.model.php`) are
  resolved by namespaced `App\Models\*` classes.
- **Typo'd filenames** (`passwrod`, `addmin`, `lesson.mode`, `hom.controller`,
  `comfirm`) are replaced with correctly-named classes/actions as each area is
  migrated.
- `deleteCourse()` referenced a non-existent `course` table with a mismatched
  bind parameter; `Course::delete()` fixes it.

See [MIGRATION.md](MIGRATION.md) for the remaining per-route work.
