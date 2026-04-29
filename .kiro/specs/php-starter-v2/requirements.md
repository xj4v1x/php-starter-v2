# Requirements Document

## Introduction

PHP Starter v2 is a clean, production-ready refactor of the existing v1 PHP starter application. The goal is to remove all debug code, experimental artifacts, and development-only configuration from the codebase while preserving all working functionality. The result is a minimal, well-structured base that is ready to grow with authentication, roles, middleware, and an admin panel in future iterations.

## Glossary

- **App**: The PHP starter v2 application as a whole.
- **Router**: The component in `public/index.php` that matches incoming HTTP requests to controller actions using the route table defined in `routes/web.php`.
- **Route_Table**: The array of route definitions returned by `routes/web.php`, each containing a `path`, `method`, and `action`.
- **BaseController**: The class `src/Core/BaseController.php` providing `view()`, `json()`, and `redirect()` helper methods to all controllers.
- **UserController**: The class `src/Controllers/UserController.php` handling CRUD operations for users.
- **HomeController**: The class `src/Controllers/HomeController.php` rendering the home view.
- **User_Model**: The class `src/Models/User.php` providing static methods for database access on the `users` table.
- **DB_Helper**: The global `db()` function defined in `config/database.php` that returns a singleton PDO instance.
- **URL_Helper**: The global `url()` function that generates absolute URLs respecting `APP_BASE_PATH`.
- **Dotenv**: The `vlucas/phpdotenv` library used to load environment variables from `.env`.
- **APP_ENV**: An environment variable (`local` or `production`) that controls error display behaviour.
- **APP_BASE_PATH**: An environment variable that holds the URL prefix when the app is served from a subdirectory.
- **Services_Directory**: The directory `src/Services/`, reserved for future service classes such as `AuthService`.
- **Logs_Directory**: The directory `storage/logs/`, reserved for future log files.

---

## Requirements

### Requirement 1: Remove Debug Code from Entry Point

**User Story:** As a developer, I want the entry point to contain no debug or development-only statements, so that the application does not expose error details in production.

#### Acceptance Criteria

1. THE App SHALL NOT contain `error_reporting()` or `ini_set('display_errors', ...)` calls in `public/index.php`.
2. WHERE `APP_ENV` is set to `local` in the environment, THE App SHALL display PHP errors to the browser.
3. WHERE `APP_ENV` is set to `production` in the environment, THE App SHALL suppress PHP error display and log errors instead.
4. THE App SHALL read `APP_ENV` exclusively from the `.env` file via Dotenv, with no hardcoded fallback that enables error display.

---

### Requirement 2: Clean Entry Point Structure

**User Story:** As a developer, I want `public/index.php` to contain only the essential bootstrap sequence, so that the file is easy to read and maintain.

#### Acceptance Criteria

1. THE App SHALL load the Composer autoloader as the first statement in `public/index.php`.
2. THE App SHALL load environment variables via Dotenv immediately after the autoloader.
3. THE App SHALL require `config/database.php` to register the `DB_Helper`.
4. THE App SHALL require `config/config.php` to register the `URL_Helper`.
5. THE App SHALL load the `Route_Table` from `routes/web.php`.
6. THE App SHALL dispatch the incoming request through the `Router` immediately after loading the `Route_Table`.
7. THE App SHALL contain no `require` statements beyond those listed in criteria 1–5.

---

### Requirement 3: Preserve HTTP Method Matching in the Router

**User Story:** As a developer, I want the router to match both path and HTTP method, so that GET and POST routes with the same path can coexist without conflict.

#### Acceptance Criteria

1. WHEN an incoming request matches both the `path` and `method` of a route in the `Route_Table`, THE Router SHALL invoke the corresponding controller action.
2. WHEN an incoming request matches the `path` of a route but not the `method`, THE Router SHALL continue checking remaining routes rather than invoking that action.
3. WHEN no route in the `Route_Table` matches the incoming request, THE Router SHALL respond with HTTP status 404 and the body `404 Not Found`.

---

### Requirement 4: Preserve APP_BASE_PATH Support

**User Story:** As a developer, I want the router to strip the `APP_BASE_PATH` prefix from the request URI, so that the app works correctly when served from a subdirectory.

#### Acceptance Criteria

1. WHEN `APP_BASE_PATH` is set to a non-empty string in the environment, THE Router SHALL strip that prefix from the request URI before route matching.
2. WHEN `APP_BASE_PATH` is empty or unset, THE Router SHALL use the raw request URI path for route matching.
3. THE Router SHALL normalise the URI to always begin with `/` and never end with a trailing `/` (except for the root path `/`).

---

### Requirement 5: Consolidate URL Helper

**User Story:** As a developer, I want the `url()` helper to remain available to all controllers and views, so that redirects and links respect `APP_BASE_PATH` without duplication.

#### Acceptance Criteria

1. THE App SHALL define the `URL_Helper` function `url(string $path): string` in `config/config.php`.
2. THE App SHALL require `config/config.php` in `public/index.php` so that `url()` is globally available.
3. WHEN `url('/users')` is called and `APP_BASE_PATH` is `/myapp/public`, THE URL_Helper SHALL return `/myapp/public/users`.
4. WHEN `url('/users')` is called and `APP_BASE_PATH` is empty, THE URL_Helper SHALL return `/users`.

---

### Requirement 6: Remove Unused Configuration File Dependency

**User Story:** As a developer, I want `public/index.php` to only load files that provide real application functionality, so that there are no dead or misleading dependencies.

#### Acceptance Criteria

1. THE App SHALL NOT require any file in `public/index.php` that contains only commented-out code, `var_dump` calls, or experimental statements.
2. IF `config/config.php` contains only the `URL_Helper` function and no debug or experimental code, THEN THE App SHALL retain the `require` for `config/config.php` in `public/index.php`.

---

### Requirement 7: Preserve Full Users CRUD

**User Story:** As a developer, I want all Users CRUD operations to continue working in v2, so that no existing functionality is lost during the cleanup.

#### Acceptance Criteria

1. THE UserController SHALL provide `index`, `create`, `store`, `edit`, `update`, and `delete` actions.
2. WHEN a POST request is received at `/users/store` with valid `name` and `email` fields, THE UserController SHALL create a new user record and redirect to `/users`.
3. WHEN a POST request is received at `/users/update` with a valid `id`, `name`, and `email`, THE UserController SHALL update the matching user record and redirect to `/users`.
4. WHEN a GET request is received at `/users/delete` with a valid `id`, THE UserController SHALL delete the matching user record and redirect to `/users`.
5. IF a `store` or `update` request contains an empty `name` or `email`, THEN THE UserController SHALL redirect to `/users` without writing to the database.

---

### Requirement 8: Preserve HomeController

**User Story:** As a developer, I want the home route to continue rendering the home view, so that the application has a working landing page.

#### Acceptance Criteria

1. WHEN a GET request is received at `/`, THE HomeController SHALL render the `views/home.php` view.

---

### Requirement 9: Create Services Directory

**User Story:** As a developer, I want a `src/Services/` directory to exist in the project, so that future service classes such as `AuthService` have a designated location.

#### Acceptance Criteria

1. THE App SHALL contain a `src/Services/` directory in the repository.
2. THE App SHALL include a `.gitkeep` file inside `src/Services/` so that the empty directory is tracked by Git.
3. THE App SHALL NOT contain any service class files in `src/Services/` at the v2 baseline.

---

### Requirement 10: Create Logs Directory

**User Story:** As a developer, I want a `storage/logs/` directory to exist in the project, so that future logging infrastructure has a designated location.

#### Acceptance Criteria

1. THE App SHALL contain a `storage/logs/` directory in the repository.
2. THE App SHALL include a `.gitkeep` file inside `storage/logs/` so that the empty directory is tracked by Git.
3. THE App SHALL add `storage/logs/*.log` to `.gitignore` so that generated log files are not committed.

---

### Requirement 11: PSR-4 Autoloading Remains Intact

**User Story:** As a developer, I want PSR-4 autoloading to continue resolving all `App\` namespaced classes from `src/`, so that no manual `require` statements are needed for application classes.

#### Acceptance Criteria

1. THE App SHALL define the PSR-4 mapping `"App\\": "src/"` in `composer.json`.
2. WHEN a class under the `App\` namespace is referenced, THE App SHALL resolve it automatically via the Composer autoloader without any explicit `require`.

---

### Requirement 12: Environment Variable Documentation

**User Story:** As a developer, I want `.env.example` to document all required environment variables, so that a new developer can set up the project without reading source code.

#### Acceptance Criteria

1. THE App SHALL include `APP_NAME`, `APP_ENV`, `APP_BASE_PATH`, `DB_HOST`, `DB_NAME`, `DB_USER`, and `DB_PASS` in `.env.example`.
2. THE App SHALL include a comment in `.env.example` describing the accepted values for `APP_ENV` (`local` or `production`).
3. THE App SHALL NOT include any real credentials or secrets in `.env.example`.
