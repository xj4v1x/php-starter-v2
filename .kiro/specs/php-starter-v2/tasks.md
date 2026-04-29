# Implementation Plan: PHP Starter v2

## Overview

Five targeted file changes to clean up the entry point, scaffold two empty directories, and document all environment variables. No routing, controller, model, or view logic changes.

## Tasks

- [x] 1. Update `public/index.php` — replace hardcoded debug block with APP_ENV-driven error control
  - Remove the `// ERROR MODE (solo dev)` comment and the two hardcoded calls: `error_reporting(E_ALL)` and `ini_set('display_errors', 1)`
  - Add the APP_ENV error control block immediately after `$dotenv->load()` and before the `require` calls for `config/database.php` and `config/config.php`
  - Block must default to `'production'` when `APP_ENV` is absent: `($_ENV['APP_ENV'] ?? 'production') === 'local'`
  - `local` branch: `error_reporting(E_ALL)` + `ini_set('display_errors', '1')`
  - `else` branch: `error_reporting(0)` + `ini_set('display_errors', '0')` + `ini_set('log_errors', '1')`
  - Remove the `// ROUTES` comment; keep all routing logic unchanged
  - _Requirements: 1.1, 1.2, 1.3, 1.4, 2.1, 2.2, 2.3, 2.4, 2.5, 2.6, 2.7_

- [x] 2. Create `src/Services/.gitkeep`
  - Create the file `src/Services/.gitkeep` with empty content
  - Do not add any PHP files or class stubs
  - _Requirements: 9.1, 9.2, 9.3_

- [x] 3. Create `storage/logs/.gitkeep`
  - Create the file `storage/logs/.gitkeep` with empty content
  - Do not add any log files or other content
  - _Requirements: 10.1, 10.2_

- [x] 4. Update `.env.example` — add APP_ENV comment and ensure all required variables are present
  - Ensure the file contains exactly these variables in order: `APP_NAME`, `APP_ENV`, `APP_BASE_PATH`, `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`
  - Add an inline comment on the `APP_ENV` line documenting accepted values: `# local | production`
  - No real credentials or secrets
  - _Requirements: 12.1, 12.2, 12.3_

- [x] 5. Checkpoint — verify all changes are consistent
  - Confirm `public/index.php` contains no bare `error_reporting(E_ALL)` or `ini_set('display_errors', 1)` outside the APP_ENV block
  - Confirm `src/Services/.gitkeep` and `storage/logs/.gitkeep` exist
  - Confirm `.gitignore` contains `storage/logs/*.log` (already present — no change needed)
  - Confirm `.env.example` has all seven variables and the APP_ENV comment
  - Ask the user if any questions arise before closing out.
