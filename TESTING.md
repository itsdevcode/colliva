# Testing

## Automated checks

Run the focused marketplace regression suite first:

```shell
php artisan test --compact tests/Feature/MarketplaceTest.php tests/Feature/MarketplaceEditingTest.php
```

Run the full application suite and static checks:

```shell
php artisan test --compact
php vendor/bin/phpstan analyse --no-progress
php vendor/bin/pint --dirty --format agent
npm run build
php artisan view:cache
```

This workspace has no `.git` metadata, so Pint’s `--dirty` option is unavailable here. Use `php vendor/bin/pint --format agent` instead. No test files were removed.

The default PHPUnit configuration uses an isolated SQLite in-memory database. It does not change application data. Existing starter-kit tests cover Fortify login, registration, verification, password reset and account settings/deletion.

## PostgreSQL regression run

Use a dedicated disposable test database. Never point the tests at the application database: the test runner refreshes database tables.

```powershell
$env:PHP_INI_SCAN_DIR = (Resolve-Path storage/framework/php).Path
$env:DB_CONNECTION = 'pgsql'
$env:DB_HOST = '127.0.0.1'
$env:DB_PORT = '5432'
$env:DB_DATABASE = 'shoutly_testing'
$env:DB_USERNAME = 'postgres'
$env:DB_PASSWORD = ''
php artisan test --compact tests/Feature/MarketplaceTest.php tests/Feature/MarketplaceEditingTest.php
```

These process variables are local development examples. Use the credentials of your own test database. Close the terminal or remove the DB overrides before running application migrations or starting the application server.

## Marketplace coverage

- Honest public empty states, profile rendering and sitemap.
- Creator/brand registration, verification notifications and admin role escalation prevention.
- Verified workspace access and role restrictions.
- Discovery visibility, city/category/budget/platform/follower/language/availability/verification filtering and pagination.
- Creator onboarding with real raster upload validation, content rights, service pricing and publication consent.
- Cross-account portfolio/package isolation, editing with renewed rights confirmation, cover validation and unpublishing.
- Business profile persistence and creator shortlisting.
- Campaign creation, draft editing, ownership and stale-editor status checks, moderation, application eligibility, withdrawal/resubmission, duplicate prevention and proposal acceptance.
- Creator confirmation, brief negotiation, delivery, revisions, completion and invalid transitions.
- Private brief/deliverable isolation and escaped user-supplied content.
- Admin panel access, moderation audit records and content reporting.

PostgreSQL checks additionally protect positive amounts, ordered deadlines and accepted-brief confirmations. No external mail or S3 service is contacted by the tests.

## Manual browser review

No browser was connected to this execution session. Automated server rendering does not establish visual quality or browser accessibility. Review desktop and mobile layouts, keyboard focus order, mobile menus/filters, loading feedback and Livewire uploads in a browser. Check contrast, reduced motion, slow connections, Lighthouse results and browser/network errors. Validate provider mail delivery and production storage separately.

## Recorded verification

- Earlier foundation milestone, full SQLite suite: 65 tests, 64 passed, one PostgreSQL-only check skipped; 228 assertions.
- Current PostgreSQL marketplace suites: 45 passed; 214 assertions, including draft editing, ownership checks, cover uploads, portfolio rights confirmation, application resubmission and the database price constraint.
- Current SQLite marketplace suites: 44 passed, one PostgreSQL-only check skipped; 213 assertions.
- PHPStan level 7: zero errors.
- Pint formatting completed. `--dirty` was attempted but is unavailable without Git metadata; the full formatter was used.
- Vite production build, route caching and Blade view compilation passed.
- The local PostgreSQL preview returned HTTP 200 with the Shoutly homepage and intentional creator empty state.

These results do not replace the manual browser and live-provider checks described above.
