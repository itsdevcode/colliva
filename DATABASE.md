# Database

## Engine and conventions

The application uses PostgreSQL. Eloquent supplies bound queries, eager loading and pagination. Monetary amounts are integer paise; forms accept INR with at most two decimal places. Foreign keys enforce relationships, reference data is normalized and explicit enum-backed database checks constrain statuses. No marketplace payload is stored as arbitrary JSON. Laravel’s framework-owned notification payloads and queue records retain their standard representation.

## Tables

| Table | Purpose and important relationships |
| --- | --- |
| `users` | Fortify accounts; role is creator, brand or admin |
| `creator_profiles` | Unique user and public slug, city, primary category, bio, media paths, availability, moderation and publication consent |
| `brand_profiles` | Unique user, business description, website and city |
| `locations` | City, state, country and unique slug; not limited structurally to Haryana |
| `categories`, `languages` | Managed reference data with unique slugs |
| `creator_category`, `creator_language` | Unique creator/taxonomy pairs |
| `social_accounts` | Unique creator/platform, HTTPS URL, reported metrics and independent reporting/verification timestamps |
| `portfolio_items` | Original image/video-link work, rights consent and moderation visibility |
| `service_packages` | Price in paise, description, deliverables, turnaround and revision policy |
| `saved_creators` | Unique brand-user/creator shortlist pair |
| `campaigns` | Owning brand, category, brief, requirements, budget, deadlines and moderation state |
| `campaign_location`, `campaign_platforms` | Normalized campaign targeting |
| `campaign_applications` | Unique campaign/creator pair, proposal, quote and application state |
| `collaboration_requests` | Participants, optional unique application, commercial scope, price, deadline and confirmations |
| `collaborations` | Unique accepted request and delivery lifecycle state |
| `collaboration_deliverables` | Submission history of HTTPS URLs and notes |
| `content_reports` | Reporting account, creator, reason and resolution status |
| `audit_logs` | Actor, action and subject identifiers; no passwords or commercial credentials |
| `platform_settings` | Internal configurable key/value records |
| `notifications` | Standard Laravel database notifications |

The starter kit also includes users, sessions, passkeys, password-reset tokens, cache and queue tables.

## Integrity

Unique foreign keys enforce one profile per user. Unique pairs prevent duplicate shortlists, languages, categories and applications. A unique application on collaboration requests and unique request on collaborations prevent repeat agreement creation.

PostgreSQL check constraints enforce positive monetary values and service turnaround, ordered campaign deadlines, nonnegative reported metrics, portfolio media presence and both confirmation timestamps on accepted briefs. Server validation adds ownership, role and lifecycle checks. Row locks serialize sensitive transitions. SQLite tests retain server validation; PostgreSQL tests exercise the actual production engine and its extra checks.

User-owned records cascade on account deletion. Deleted taxonomy nulls optional creator references, immediately making an incomplete creator ineligible for discovery. Categories referenced by campaigns are restricted from deletion. Audit actors become null if their account is deleted. A deleted application may leave its accepted brief intact with a null application reference.

## States

- Creator: draft → pending → approved or rejected. Editing returns to draft; unpublishing also revokes publication consent.
- Campaign: draft → pending → published or cancelled after moderation. Published campaigns can close or cancel; closed campaigns can complete only after all accepted collaborations complete.
- Application: submitted → shortlisted, accepted, rejected or withdrawn. Shortlisted applications may be accepted, rejected or withdrawn. Accepted/rejected/withdrawn applications cannot be reviewed again.
- Brief: pending → accepted, rejected, changes requested or cancelled. The brand can revise a request awaiting changes, returning it to pending. Acceptance requires the brand’s prior terms confirmation and creates one collaboration.
- Collaboration: active → submitted → completed or revision requested. Revision requested → submitted. Completed work cannot receive another submission.

Payment coordination is external and is never represented as escrow or secured funds.

## Reference and demo data

`MarketplaceSeeder` idempotently creates nine creator categories, seven Haryana cities and four languages. It creates no member accounts.

`DemoMarketplaceSeeder` is optional and restricted to local/testing. Fictional creator/brand accounts use `@demo.example` addresses and are named as demo records. Demo profiles are excluded from public queries regardless of moderation state. Do not run this seeder on production.
