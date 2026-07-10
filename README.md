# G5 E-Learning

A small PHP + MySQL (PDO) e-learning platform with three roles: **admin**,
**trainer**, and **student**. No framework — a hand-rolled MVC behind a single
front controller.

## Directory structure

```
g5-Elearning/
├── public/                 ← web root (point your server's document root here)
│   ├── index.php           ← single front controller + route table
│   ├── .htaccess           ← rewrites non-file requests to index.php
│   ├── assets/             ← images, icons, site CSS
│   ├── vendor/             ← front-end libraries (bootstrap, apexcharts, quill…)
│   ├── uploading/          ← user-uploaded images (written at runtime)
│   ├── studentprofile/     ← static profile images
│   └── trainerprofile/
├── app/                    ← application code (NOT web-accessible)
│   ├── controllers/        ← request handlers, grouped by area
│   ├── models/             ← PDO query functions (use the global $connection)
│   ├── views/              ← page templates (*.view.php)
│   ├── layouts/            ← shared header/navbar/footer (public + admin)
│   ├── core/               ← helpers (url.php, debug.php)
│   └── database/
│       └── database.php    ← builds the shared PDO connection from config
├── config/
│   └── database.php        ← DB credentials (reads env vars, has defaults)
├── database/
│   └── learning.sql        ← schema + seed data
├── .env.example            ← copy to .env and set your DB credentials
└── .gitignore
```

## How routing works

`public/index.php` is the only entry point. It:

1. defines `BASE_PATH` / `APP_PATH` / `PUBLIC_PATH`,
2. sets PHP's `include_path` to `app/` so controllers/views/models keep using
   root-relative requires like `require 'models/user.model.php'`,
3. `chdir()`s into `public/` so uploads are written under the web root,
4. matches `$_SERVER['REQUEST_URI']` against three route tables:
   - **`$publicPages`** — wrapped in the public site layout,
   - **`$adminPages`** — wrapped in the admin dashboard layout,
   - **`$standalonePages`** — rendered as-is (auth flows + form handlers that
     redirect via `header()`, which must not be wrapped in a layout).

To add a page: create the controller under `app/controllers/…`, then add one
line to the appropriate table in `public/index.php`.

## Local setup

```bash
# 1. Create the database and import the schema
mysql -u root -e "CREATE DATABASE e_learning_db"
mysql -u root e_learning_db < database/learning.sql

# 2. Configure credentials (optional — defaults are root / no password)
cp .env.example .env        # then edit, and export the vars before serving

# 3. Serve with the document root pointed at public/
php -S localhost:8000 -t public
```

Then open <http://localhost:8000/>.

## Known issues carried over from the original code

These predate the restructure and are left as-is (behaviour preserved):

- Passwords are stored in **plaintext**; the sign-in check in
  `app/controllers/students/signin/access.controller.php` re-hashes the stored
  value and is effectively broken.
- `admin.model.php`, `student.model.php` etc. mix concerns and there are many
  filename typos (`passwrod`, `addmin`, `lesson.mode`, `hom.controller`).

See the migration notes / your reviewer for suggested follow-ups.
