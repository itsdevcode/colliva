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
- Applications are unique per campaign/creator. Creators may resubmit withdrawn applications with an updated proposal and quote; active or final applications cannot be duplicated.
- Video work and deliverables use HTTPS links. Videos are not downloaded, embedded or transcoded.
- Campaign drafts support editing and resubmission for moderation. Pending, published and final campaigns cannot be edited; the state is rechecked under a database lock when saving.
- Portfolio items and service packages support editing. Existing portfolio images are retained unless replaced, and portfolio edits require fresh rights confirmation and another publication review. Profile covers can be uploaded during onboarding.
- Media uploads use generated paths and validated raster formats. Public media is intended for portfolios; account deletion removes database records but does not currently run a background storage-purge job.
- Responsive breakpoints, focus states, accessible labels and reduced-motion rules are implemented. Browser-based visual, keyboard and Lighthouse audits remain to be performed.

## Proposed account cleanup — pending approval

After a user confirms account deletion with their password, delete that account's notification history and queue removal of its stored profile photograph, cover image and portfolio images. Dispatch storage cleanup only after the database deletion commits. The job would receive the exact stored file keys and selected media disk, restrict deletion to creator-upload directories, and retry transient storage failures through the existing queue.

This permanently removes those files and notification records. Automatic approval review rejected the implementation because this destructive extension had not been specifically approved. The existing account-deletion behavior is preserved; no cleanup job is currently installed or dispatched. Approval is required before implementing this proposal.
