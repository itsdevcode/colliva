# Shoutly

A local influencer and UGC marketplace for Yamunanagar, Jagadhri and nearby Haryana cities. Creators build portfolios and publish service packages. Brands discover approved creators, send briefs and run campaigns. Both parties track agreed work through delivery and completion.

## Stack

PHP 8.3, Laravel 13, Livewire 4, Blade, Alpine (bundled with Livewire), Tailwind 4, Fortify, Filament 5, PostgreSQL, Pest and Pint. Installed versions are pinned in `composer.lock` and `package-lock.json`.

## Start locally

1. Enable PHP extensions `pdo_pgsql`, `pdo_sqlite`, `sqlite3`, `fileinfo`, `gd`, `intl` and the normal Laravel requirements. PostgreSQL is the application database; SQLite supports isolated tests.
2. Run `composer install` and `npm ci`.
3. Copy `.env.example` to `.env`, set PostgreSQL credentials, and run `php artisan key:generate --no-interaction`.
4. Create the `shoutly` database, then run `php artisan migrate --no-interaction` and `php artisan db:seed --no-interaction`.
5. Run `php artisan storage:link --no-interaction` and `npm run build`.
6. Run `php artisan serve --no-interaction`. Run `php artisan queue:work --tries=3 --no-interaction` in another terminal for notifications. Use `npm run dev` while editing frontend files.

On this Windows machine, a project-local PHP extension configuration lives in the ignored `storage/framework/php/marketplace.ini`. Before PHP commands in PowerShell, use:

```powershell
$env:PHP_INI_SCAN_DIR = (Resolve-Path storage/framework/php).Path
```

A fresh checkout should enable the extensions in its PHP configuration instead. See [SETUP.md](SETUP.md) for full setup, mail, storage and administrator access.

## Main journeys

- Public: homepage, `/creators`, city/category filters, `/creator/{slug}`, XML sitemap.
- Creator: register, verify email, open `/studio`, save profile, add social accounts, portfolio and services, consent and submit for review.
- Brand: register, verify email, open `/workspace`, complete business details, save creators, create campaign drafts or submit for moderation.
- Hiring: send a brief from a creator profile; creator accepts, declines or requests changes. Acceptance creates a collaboration. Creator submits a secure link; brand requests revisions or completes the work.
- Admin: a verified administrator signs in at `/admin` to review creators and campaigns, moderate portfolio content and reports, manage taxonomy and inspect activity.

## Data and trust

Default seeding creates categories, cities and languages only. There are no invented public listings, reviews or marketplace statistics. Optional fictional development accounts are available through `php artisan db:seed --class=DemoMarketplaceSeeder --no-interaction`; they remain excluded from public discovery and indexing.

Public profiles require approval, publication consent, a verified creator account, location, category, photograph, service package and visible portfolio item. Editing creator content returns the profile to draft. Social metrics remain self-reported unless manually verified. Creator identity verification is separate from approval.

Money is stored as integer paise. Payments are coordinated directly between the parties. No checkout, escrow, secured-funds claim or sensitive payment credentials are implemented.

## Quality checks

```shell
php artisan test --compact tests/Feature/MarketplaceTest.php
php artisan test --compact
php vendor/bin/phpstan analyse --no-progress
php vendor/bin/pint --format agent
npm run build
php artisan view:cache
```

See [TESTING.md](TESTING.md) for PostgreSQL test commands and the scope of automated coverage.

## Production preparation

Set a real application URL, `APP_ENV=production`, `APP_DEBUG=false`, HTTPS, secure session cookies and a transactional mail provider. Configure a supervised queue worker, database backups and public-media storage. Run migrations during a controlled deployment, build assets, cache configuration/routes/views and restart workers. S3-compatible media support is installed; use `MEDIA_DISK=s3` and configure the storage credentials. Live provider delivery and browser accessibility/performance audits must be validated in the target environment.

[ARCHITECTURE.md](ARCHITECTURE.md) describes the application boundaries. [DATABASE.md](DATABASE.md) describes the schema and state transitions.

## Implementation phases

| Phase | Main files and decisions | Verification |
| --- | --- | --- |
| Foundation | Existing Fortify starter retained; User roles, policies, PostgreSQL schema and Filament provider added | Registration and verification tests passed; PostgreSQL migrations and reference seeding completed |
| Public UI | Shared marketplace layout, original homepage artwork, creator cards, discovery and profile pages | Public rendering, filtering, pagination, escaping and sitemap tests passed; Vite build passed |
| Creator platform | `CreatorOnboarding`, safe uploads, portfolio, package editing, consent and moderation action | Onboarding, publication, ownership, media validation and approval tests passed |
| Brand platform | `BrandWorkspace`, saved creators and participant-scoped briefs | Business persistence, shortlisting, enquiry and isolation tests passed |
| Campaigns and hiring | `CampaignBoard`, `ReviewApplication`, `ManageCollaboration` and delivery history | Eligibility, duplicate prevention, moderation, negotiation, revision and completion tests passed |
| Quality and launch | PostgreSQL checks/indexes, admin resources, reporting, documentation and production storage configuration | Full test suite, PostgreSQL regression suite, static analysis, formatting and build/cache checks passed; browser and provider checks remain |

Current implementation limits and extension points are listed in `ARCHITECTURE.md`. The running preview uses port 8010 because an existing process on port 8000 retained the earlier MySQL environment. Restart any existing Laravel development process after changing database configuration.
