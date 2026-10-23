# Setup

## Requirements

PHP 8.3+, Composer, Node/npm and PostgreSQL. Enable `pdo_pgsql`, `fileinfo`, `gd`, `intl`, `mbstring`, `openssl`, XML and Laravel’s standard extensions. Enable `pdo_sqlite` and `sqlite3` for the default isolated test database.

This machine already had PostgreSQL running. Separate `shoutly` and `shoutly_testing` databases were created; the original MySQL application database was not migrated or deleted. The local `.env` points to PostgreSQL. Credentials remain in ignored environment configuration.

## Windows PHP extensions

The host PHP configuration did not enable PostgreSQL or SQLite. To avoid changing machine-wide configuration, the implementation uses an ignored scan directory:

```powershell
New-Item -ItemType Directory -Force storage/framework/php
Set-Content storage/framework/php/marketplace.ini "extension=pdo_pgsql`nextension=pdo_sqlite`nextension=sqlite3"
$env:PHP_INI_SCAN_DIR = (Resolve-Path storage/framework/php).Path
php -m
```

Set this environment variable in every terminal that runs Artisan or the development server. For a Laragon-managed web server, enable `pdo_pgsql` in that server’s PHP configuration and restart it. The CLI scan directory does not automatically alter Laragon’s PHP process.

## Installation

```shell
composer install
npm ci
```

Copy `.env.example` to `.env`, set application URL and database credentials, then:

```shell
php artisan key:generate --no-interaction
php artisan migrate --no-interaction
php artisan db:seed --no-interaction
php artisan storage:link --no-interaction
npm run build
php artisan serve --no-interaction
```

Do not run `migrate:fresh` on an application database containing real data.

## Email and queues

Local defaults use `MAIL_MAILER=log`. Verification and password-reset messages appear in `storage/logs/laravel.log`; they are not delivered to a real inbox. Configure SMTP or another Laravel mail transport before onboarding real users.

Marketplace updates queue email and database notifications after transaction commit. Run:

```shell
php artisan queue:work --tries=3 --no-interaction
```

Use a process supervisor in production. Restart workers after deploying new code. Laravel’s existing database queue/cache/session migrations are included.

## Administrator access

No production administrator or shared administrator password is seeded. Register an account, verify its email, then grant access from the server terminal:

```shell
php artisan shoutly:promote-admin your-email@example.com --no-interaction
```

The command refuses unknown or unverified accounts and records the role change in the audit log. Sign in at `/admin`. Subsequent role changes can be made by another administrator through the Users resource. Public registration never accepts an admin role.

## Media storage

Local media uses `MEDIA_DISK=public`; `storage:link` exposes the public media directory. Uploads accept only validated JPG, PNG and WebP files, with 4 MB profile and 8 MB portfolio limits. Portfolio publication requires ownership/authorization consent.

For S3-compatible storage, set `MEDIA_DISK=s3` and configure `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_ENDPOINT`, `AWS_URL` and `AWS_USE_PATH_STYLE_ENDPOINT` as needed. The Flysystem S3 adapter is installed. Ensure the bucket/CDN serves only intended public media, and validate an upload plus its rendered URL in the target environment.

## Deployment readiness

Set production environment, disable debug, serve over HTTPS and enable secure session cookies. Keep secrets outside source control. Configure production mail, storage, database backups and queue supervision. Build assets, run controlled migrations and cache the application:

```shell
npm run build
php artisan migrate --force --no-interaction
php artisan optimize --no-interaction
php artisan queue:restart --no-interaction
```

This task does not deploy or configure a cloud provider. Live mail/S3 delivery, visual browser QA, keyboard navigation and Lighthouse audits remain environment-specific release checks. Content reports and moderation are manual workflows; verified profile badges are manual administrative assertions rather than automated social-provider checks.
