# Architecture

## Modular Laravel monolith

Shoutly uses the existing official Livewire starter kit. Fortify handles login, registration, password reset, email verification, two-factor authentication and passkeys. Public and member interfaces use Blade and class-based Livewire components. Filament is restricted to internal administration.

No independent services, matching engine, payment gateway or social scraper are introduced.

## Project structure

| Location | Responsibility |
| --- | --- |
| `app/Models` | Relationships, casts, persistence and the public-visibility scope |
| `app/Policies` | Creator ownership, campaign eligibility and collaboration permissions |
| `app/Actions` | Moderation, application review and transactional collaboration transitions |
| `app/Livewire` | Discovery filters, creator onboarding, brand workspace, campaigns, briefs and reports |
| `app/Filament` | Admin resources, moderation actions and database-backed metrics |
| `app/Notifications` | Queued email and database updates |
| `app/Http/Controllers/MarketplaceController.php` | Public homepage/profile and dashboard data |
| `resources/views` | Shared layout, reusable cards/icons and individual pages |
| `resources/css/app.css` | Design tokens, responsive marketplace styles and reduced-motion support |
| `database/migrations` | Relational schema, unique keys, foreign keys and PostgreSQL checks |
| `database/seeders` | Idempotent reference data and explicit development-only demo data |
| `tests/Feature/MarketplaceTest.php` | Marketplace regression and access-control coverage |

## Authorization boundaries

Public queries use `CreatorProfile::publiclyVisible()`. Draft, pending, rejected, nonconsenting, unverified, incomplete and demo profiles are excluded from discovery, public profiles and the sitemap. Approval and verified identity are separate fields.

Public registration accepts only creator or brand roles. Roles are excluded from normal User mass assignment. Admin panel access requires an administrator with a verified email. Member components authorize writes on the server; portfolio/package operations are scoped through the current creator’s relationship. Private briefs and notifications are queried through participant ownership. Campaign applications are visible to the campaign owner and the individual applicant.

## Transactional workflows

`ModerateCreator` locks the profile, validates publication prerequisites, saves the decision, writes an audit record and queues an update after commit.

`ReviewApplication` locks the application and campaign before changing application status. Accepting a proposal creates a brand-confirmed brief with its agreed quote and scope. Creator confirmation is still required before creating a collaboration.

`ManageCollaboration` locks each request or collaboration before changing state. It rejects unauthorized actors and invalid transitions. Scope revisions are allowed before acceptance; accepted terms remain fixed. Work progresses through submission, revision and completion. A unique request foreign key prevents duplicate collaboration records.

## Design system

Background `#FAFAF8`, text `#171717`, violet `#6D4AFF`, pale violet `#F0EBFF`, light borders and warm supporting paper tones. Inter handles interface text; an italic serif provides editorial emphasis. Content is limited to 1280px. Reusable layout, creator card, icons and validation feedback keep pages consistent.

The homepage uses original CSS artwork. It does not impersonate registered creators. Actual portfolio cards only render stored approved creator content.

## Extension points and current limits

- The filesystem media disk can switch from public local storage to an S3-compatible backend without changing templates.
- Laravel’s queue/cache abstractions permit Redis later. Notifications currently use the database queue.
- Commercial terms are stored on collaboration requests; a future payment provider can attach its own records. No payment status is inferred from collaboration acceptance.
- Reviews, online payments, subscriptions, scraping and automated identity verification are outside this release.
- Applications are unique per campaign/creator, including withdrawn applications; the UI does not currently resubmit withdrawn applications.
- Video work and deliverables use HTTPS links. Videos are not downloaded, embedded or transcoded.
- Campaigns can be saved as drafts and submitted for moderation; full editing of existing campaign briefs is not yet provided.
- Portfolio items can be added and removed; service packages support editing.
- Media uploads use generated paths and validated raster formats. Public media is intended for portfolios; account deletion removes database records but does not currently run a background storage-purge job.
- Responsive breakpoints, focus states, accessible labels and reduced-motion rules are implemented. Browser-based visual, keyboard and Lighthouse audits remain to be performed.
