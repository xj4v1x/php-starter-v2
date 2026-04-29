# Design Document — PHP Starter v2

## Overview

PHP Starter v2 is a clean, production-ready refactor of the existing v1 application. The goal is not to add new features but to remove all debug artifacts, harden the entry point, and establish the directory scaffolding that future iterations (auth, roles, middleware) will build on.

The application follows a minimal MVC pattern with no framework dependency beyond Composer autoloading and `vlucas/phpdotenv`. All routing is handled by a simple array-based dispatcher in `public/index.php`. Controllers extend a shared `BaseController` that provides `view()`, `json()`, and `redirect()` helpers. Models are thin static classes that wrap PDO queries via a global `db()` singleton.

The v2 refactor touches exactly four concerns:

1. **Error handling** — replace hardcoded `error_reporting`/`ini_set` calls with `APP_ENV`-driven logic loaded after Dotenv.
2. **Entry point hygiene** — enforce a strict, documented bootstrap order with no dead `require` statements.
3. **Directory scaffolding** — add `src/Services/` and `storage/logs/` with `.gitkeep` files and a `.gitignore` rule for log files.
4. **Environment documentation** — ensure `.env.example` documents every variable the application reads.

No routing logic, controller logic, model logic, view logic, or autoloading configuration changes.

---

## Architecture

The application uses a front-controller pattern: Apache's `mod_rewrite` (via `public/.htaccess`) routes all requests to `public/index.php`, which bootstraps the application and dispatches to the appropriate controller.

```
Browser → Apache → public/.htaccess → public/index.php
                                            │
                              ┌─────────────┼──────────────────┐
                              ▼             ▼                  ▼
                        vendor/         config/           routes/web.php
                        autoload.php    database.php
                                        config.php
                                            │
                                            ▼
                                    Router (foreach loop)
                                            │
                              ┌─────────────┴─────────────┐
                              ▼                           ▼
                        Controller::action()         404 response
                              │
                    ┌─────────┴──────────┐
                    ▼                    ▼
               Model (PDO)          View (PHP)
```

### Bootstrap sequence (v2)

```
1. require vendor/autoload.php          ← Composer PSR-4 autoloader
2. Dotenv::createImmutable()->load()    ← load .env
3. APP_ENV error control block          ← configure error display/logging
4. require config/database.php          ← register db() helper
5. require config/config.php            ← register url() helper
6. $routes = require routes/web.php     ← load route table
7. URI normalisation + dispatch loop    ← match & invoke controller
8. http_response_code(404) fallthrough  ← no match
```

This order is strict: Dotenv must run before the error control block (which reads `APP_ENV`), and both config files must be loaded before any controller is invoked (controllers call `url()` and `db()`).

---

## Components and Interfaces

### public/index.php — Entry Point / Router

Responsibilities:
- Bootstrap the application in the defined sequence.
- Normalise the request URI by stripping `APP_BASE_PATH` and ensuring a leading `/` with no trailing `/`.
- Iterate the route table and dispatch the first matching `path` + `method` pair.
- Respond with 404 if no route matches.

Key logic (v2 version):

```php
// Error control — replaces hardcoded error_reporting/ini_set
if (($_ENV['APP_ENV'] ?? 'production') === 'local') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
```

URI normalisation (unchanged from v1):

```php
$basePath = rtrim($_ENV['APP_BASE_PATH'] ?? '', '/');
$uri      = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri      = $basePath !== '' ? str_replace($basePath, '', $uri) : $uri;
$uri      = '/' . trim($uri, '/');
```

### config/database.php — DB Helper

No changes. Provides the global `db()` function returning a PDO singleton configured from `$_ENV` variables.

### config/config.php — URL Helper

No changes. Provides the global `url(string $path): string` function that prepends `APP_BASE_PATH` to a path.

### routes/web.php — Route Table

No changes. Returns a flat array of route definitions, each with `path`, `method`, and `action` keys.

### src/Core/BaseController — Base Controller

No changes. Provides `view()`, `json()`, and `redirect()` to all controllers.

### src/Controllers/HomeController — Home Controller

No changes. Handles `GET /` → renders `views/home.php`.

### src/Controllers/UserController — User Controller

No changes. Handles all six Users CRUD actions.

### src/Models/User — User Model

No changes. Provides `all()`, `find()`, `create()`, `update()`, `delete()` as static PDO-backed methods.

### src/Services/ — Services Directory

New empty directory. Contains only `.gitkeep`. Reserved for future service classes (e.g. `AuthService`, `EmailService`).

### storage/logs/ — Logs Directory

New empty directory. Contains only `.gitkeep`. The `.gitignore` file gains a `storage/logs/*.log` entry so generated log files are never committed.

---

## Data Models

### Route Definition

Each entry in the route table is a plain PHP associative array:

```
{
  path:   string   // e.g. "/users/store" — always starts with /
  method: string   // HTTP verb in uppercase: "GET" | "POST"
  action: string   // "ControllerName@methodName"
}
```

No changes to this structure in v2.

### User Record

Stored in the `users` MySQL table:

```
{
  id:    int          // AUTO_INCREMENT primary key
  name:  varchar(100) // required, non-empty after trim
  email: varchar(100) // required, non-empty after trim
}
```

No changes to this structure in v2.

### Environment Variables

All variables read by the application, documented in `.env.example`:

| Variable        | Type   | Accepted values              | Purpose                                      |
|-----------------|--------|------------------------------|----------------------------------------------|
| `APP_NAME`      | string | any                          | Application display name                     |
| `APP_ENV`       | string | `local` \| `production`      | Controls error display vs. error logging     |
| `APP_BASE_PATH` | string | URL prefix or empty string   | Subdirectory prefix stripped from request URI |
| `DB_HOST`       | string | hostname                     | MySQL host                                   |
| `DB_NAME`       | string | database name                | MySQL database                               |
| `DB_USER`       | string | username                     | MySQL user                                   |
| `DB_PASS`       | string | password (may be empty)      | MySQL password                               |


---

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system — essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

The features in this refactor that involve non-trivial logic amenable to property-based testing are:

1. The router's path+method matching logic
2. The URI normalisation logic (base-path stripping + format enforcement)
3. The `url()` helper's base-path prepending logic
4. The `UserController`'s input validation guard (write only when inputs are valid)

Infrastructure concerns (directory structure, composer.json, .env.example contents, bootstrap ordering) are verified by smoke tests and are not suitable for property-based testing.

---

### Property 1: Router dispatches on exact path+method match

*For any* route table containing one or more route definitions, and any incoming (path, method) pair, the router SHALL invoke the action of the first route whose `path` AND `method` both match exactly — and SHALL NOT invoke any action when no route has both matching simultaneously.

**Validates: Requirements 3.1, 3.2, 3.3**

---

### Property 2: URI normalisation strips base path and enforces format

*For any* non-empty `APP_BASE_PATH` string and any raw `REQUEST_URI` string, the normalised URI produced by the router SHALL (a) not begin with the base-path prefix, (b) begin with exactly one `/`, and (c) not end with a trailing `/` unless the result is exactly `/`.

When `APP_BASE_PATH` is empty or unset, the same format invariants (b) and (c) SHALL hold for any raw `REQUEST_URI`.

**Validates: Requirements 4.1, 4.2, 4.3**

---

### Property 3: url() helper prepends base path correctly

*For any* `APP_BASE_PATH` value (including empty string) and any path string, `url($path)` SHALL return a string equal to `rtrim(APP_BASE_PATH, '/') . '/' . ltrim($path, '/')`, ensuring no double slashes and no missing leading slash.

**Validates: Requirements 5.3, 5.4**

---

### Property 4: UserController only writes to the database when all inputs are valid

*For any* combination of `name` and `email` values passed to `store()` or `update()`, the controller SHALL call `User::create()` / `User::update()` if and only if both `trim($name) !== ''` and `trim($email) !== ''` (and, for `update()`, `$id > 0`). When any of those conditions fails, no database write SHALL occur and the controller SHALL redirect to `/users`.

**Validates: Requirements 7.2, 7.3, 7.5**

---

### Property 5: UserController delete removes the record for any valid id

*For any* positive integer `$id`, calling `UserController::delete()` with that id SHALL invoke `User::delete($id)` exactly once and then redirect to `/users`. When `$id` is zero or negative, no database call SHALL occur.

**Validates: Requirements 7.4**

---

## Error Handling

### APP_ENV-based error control

The error control block in `public/index.php` runs immediately after Dotenv loads, so `APP_ENV` is always available. The safe default is `production` (errors hidden):

```php
if (($_ENV['APP_ENV'] ?? 'production') === 'local') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
```

This means a missing or misspelled `APP_ENV` value silently falls into the production-safe path — no error details are ever leaked by accident.

### Database connection errors

`config/database.php` sets `PDO::ATTR_ERRMODE` to `PDO::ERRMODE_EXCEPTION`. Any connection failure or query error throws a `PDOException`. In `local` mode this exception will be displayed; in `production` mode it will be logged (once `log_errors` is on) and the user will see a generic PHP fatal error page. A future iteration can add a global exception handler.

### 404 fallthrough

When no route matches, `public/index.php` sets HTTP status 404 and echoes `404 Not Found`. This is intentionally minimal — a future iteration can render a proper error view.

### UserController validation

`UserController` silently redirects to `/users` when inputs are invalid (empty name, empty email, or zero/negative id). No error message is shown to the user in v2. This is consistent with the existing v1 behaviour and is acceptable for a starter kit baseline.

---

## Testing Strategy

### PBT applicability assessment

This feature is a refactor of PHP application logic. The router dispatch logic, URI normalisation, `url()` helper, and controller input validation are all pure or near-pure functions with clear input/output behaviour and wide input spaces. Property-based testing is appropriate for these components.

Infrastructure concerns (directory structure, composer.json, .env.example, bootstrap ordering) are one-time structural checks that do not benefit from randomised input — these use smoke tests.

### Recommended PBT library

**[PHPUnit](https://phpunit.de/) + [eris](https://github.com/giorgiosironi/eris)** (PHP property-based testing library). Alternatively, **[PHPCheck](https://github.com/nikic/PHP-Parser)** or a simple custom generator layer on top of PHPUnit is acceptable given the simplicity of the generators needed here.

Each property test MUST run a minimum of **100 iterations**.

### Unit / smoke tests (PHPUnit)

| Test | Type | Covers |
|------|------|--------|
| `index.php` contains no `error_reporting`/`ini_set` hardcoded calls | Smoke | Req 1.1 |
| Bootstrap sequence order in `index.php` | Smoke | Req 2.1–2.7 |
| `url()` defined in `config/config.php` | Smoke | Req 5.1 |
| `UserController` has all six methods | Smoke | Req 7.1 |
| `src/Services/` directory and `.gitkeep` exist | Smoke | Req 9.1–9.2 |
| `storage/logs/` directory and `.gitkeep` exist | Smoke | Req 10.1–10.2 |
| `.gitignore` contains `storage/logs/*.log` | Smoke | Req 10.3 |
| `composer.json` has `"App\\\\"` → `"src/"` mapping | Smoke | Req 11.1 |
| `.env.example` contains all required keys + APP_ENV comment | Smoke | Req 12.1–12.3 |
| `APP_ENV=local` → `display_errors` on | Example | Req 1.2 |
| `APP_ENV=production` → `display_errors` off, `log_errors` on | Example | Req 1.3 |
| `HomeController::index()` renders `home` view | Example | Req 8.1 |

### Property-based tests

Each test MUST be tagged with:
`// Feature: php-starter-v2, Property {N}: {property_text}`

| Property | Tag | Covers |
|----------|-----|--------|
| Router dispatches on exact path+method match | `Property 1` | Req 3.1, 3.2, 3.3 |
| URI normalisation strips base path and enforces format | `Property 2` | Req 4.1, 4.2, 4.3 |
| url() helper prepends base path correctly | `Property 3` | Req 5.3, 5.4 |
| UserController only writes when all inputs are valid | `Property 4` | Req 7.2, 7.3, 7.5 |
| UserController delete removes record for any valid id | `Property 5` | Req 7.4 |

### Generator notes

- **Route table generator**: produce arrays of 1–10 route definitions with random paths (alphanumeric segments), random methods from `['GET', 'POST', 'PUT', 'DELETE']`, and random action strings.
- **URI generator**: produce strings like `/seg1/seg2` with 0–5 random alphanumeric segments, optionally with trailing slashes.
- **Base path generator**: produce strings like `/prefix/sub` with 0–3 segments, or empty string.
- **Name/email generator**: produce strings ranging from empty, whitespace-only, to valid non-empty values.
- **Id generator**: produce integers from -10 to 1000 (covering zero, negative, and positive cases).
