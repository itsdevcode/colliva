# Colliva interface system

## Design audit and direction

The previous public interface used warm paper surfaces, serif emphasis, ornamental camera artwork, tiny 8–12px labels and multiple unrelated card treatments. Mobile filters expanded into the page without a modal interaction. Authentication and marketplace branding diverged. The redesign replaces those patterns with Inter typography, white and neutral gray surfaces, consistent controls, clearer hierarchy, and portfolio-led content.

No Apple-oriented design skill was installed. The implementation uses the project’s Tailwind CSS, Livewire, Flux and Laravel conventions, with Apple’s public Human Interface Guidelines as a reference for clarity, familiar interactions, readable type, comfortable controls and keyboard accessibility. It does not reproduce Apple branding or layouts.

References: https://developer.apple.com/design/human-interface-guidelines/accessibility and https://developer.apple.com/design/human-interface-guidelines/.

## Tokens

The shared source is `resources/css/app.css`.

| Token | Value / use |
| --- | --- |
| `--surface` | `#FFFFFF`, primary surfaces |
| `--surface-secondary` | `#F5F5F7`, grouped content and empty states |
| `--ink` | `#1D1D1F`, primary text |
| `--ink-secondary` | `#6E6E73`, supporting text |
| `--line` | `#D2D2D7`, subtle separators; form boundaries use stronger `#86868B` |
| `--purple` / `--color-brand` | `#6D4AFF`, primary actions, focus and selected states |
| `--radius` | 16px cards; 8–10px controls; 20–24px large surfaces |
| Font | Locally bundled Inter, 400/500/600/700 |
| Type | Hero 44–80px; page 36–56px; section 28–44px; card 17–23px; body 15–19px; labels/support 12–14px |
| Spacing | 4/8px rhythm; 20px mobile gutters, 32px tablet, 48px desktop; 1280px maximum content width |
| Controls | Minimum 44px interactive height; primary buttons 48px; visible 3px focus outline |
| Motion | Short color/border transitions, loading shimmer; disabled under reduced-motion preferences |

Purple is reserved for meaningful actions and states. Marketing surfaces stay neutral. Shadows are limited to subtle elevation on dialogs, hiring panels and hover states.

## Reusable components and states

| Component | Interface and behavior |
| --- | --- |
| `x-colliva-button` | `href` renders a link; otherwise a button. `variant`: purple (default), outline, dark, white. `small`: 44px variant. Pass `type="submit"`, `wire:click`, `wire:loading.attr="disabled"`, and other attributes normally. Hover, keyboard focus, disabled and loading styling are shared. |
| `x-colliva-heading` | `title`, optional `eyebrow` / `description`, action slot. Stacks on mobile. |
| `x-colliva-empty` | `title`, `description`, optional existing icon name, action slot. Used for launch and filtered discovery empties. |
| `x-colliva-drawer` | `name`, `title`; native dialog opened from an Alpine scope with `$refs.name.showModal()`. Supports focus containment, Escape, explicit close and backdrop dismissal. Mobile bottom sheet; desktop side drawer. Use `wire:ignore.self` when Livewire owns its contents. |
| `x-discovery-picker` | Homepage location/category field using the installed Flux radio menu, Alpine state and a named hidden GET input. Shared labels, rounded popup, selected state, keyboard navigation and responsive sizing replace the browser-native dropdown. |
| `x-creator-filters` | Receives locations/categories/languages; shares existing URL-bound Livewire filters between desktop and mobile. Search debounced 300ms, numeric inputs 400ms. |
| `x-creator-card` | Receives eager-loaded creator data. Original portfolio preview, category, avatar, name, location, social platforms, verification, availability, real starting price and profile link. Missing pricing says “Enquire for pricing”. |
| `x-portfolio-tile` | `item`: original uploaded image, title, description and external original-video link. Responsive aspect ratio and lazy loading. |
| `x-workspace-errors` | Persistent success notification with explicit dismiss button; validation errors use `role="alert"`. No timed dismissal. |
| Existing Flux controls | Authentication/settings inputs, select menus, badges, buttons, breadcrumbs, modals and toast components remain available from the installed free Flux library. |
| Shared semantic patterns | `.pill`, `.status-badge`, `.workspace-card`, `.studio-tabs`, `.breadcrumb`, `.dashboard-metrics`, `.skeleton-grid`, `.section-heading`, shared site/workspace navigation. Studio buttons identify the current step rather than claiming ARIA tab semantics. |

Native marketplace form elements retain existing Livewire bindings, labels and validation. Checkbox hit areas, search/select controls, loading skeletons and validation notices use the same system. No new runtime dependency is introduced.

## Page coverage

- Homepage: exact requested headline, two primary journeys, discovery search, real featured creators or intentional launch empty state, categories, locations, three-step explanation, brand/creator benefits, FAQ and footer.
- Discovery: desktop filtering sidebar, mobile search and modal sheet, results count, sorting, saved-creator action, URL filters, skeletons and empty state.
- Public profile: original imagery, introduction, social/audience information, portfolio tiles, services, pricing and availability; desktop sticky hiring panel and persistent mobile services shortcut.
- Creator workspace: overview metrics, creator studio with six progressive steps, portfolio, services, campaign applications, briefs and collaboration delivery flows.
- Brand workspace: overview, business profile, saved creators, campaign drafting/publishing and collaboration flows.
- Authentication/settings: Colliva branding, Inter typography, neutral surfaces and existing accessible Flux controls. Internal Filament branding updated; its administration behavior is preserved.

## Data and review boundaries

### Editorial imagery

The homepage uses original AI-created decorative imagery, generated with the built-in image generation tool and encoded as WebP at quality 82. These images are labelled editorial illustrations and are separate from real creator portfolio records. No people or fictitious creator listings are depicted.

- `public/images/creative-studio.webp`: Use case: photorealistic-natural. Asset type: original editorial photograph for Colliva, a premium Indian creator collaboration marketplace. Create a beautiful wide landscape 3:2 photograph of a sunlit creative studio tabletop, sophisticated art direction: black mirrorless camera in foreground, open creative notebook with blank pages, sculptural ivory ceramic coffee cup, a single branch of delicate flowers, linen fabric and subtle lilac paper color swatch. Warm natural sunlight from a large window, tactile cream plaster and pale oak, gentle shadows, quiet lived-in realism, expensive independent creative journal aesthetic. Camera low three-quarter angle, layered depth, asymmetric balanced composition with space around objects. Soft warm ivory, muted lavender, olive accents, rich black camera. No people, no lettering or logos, no watermarks, no UI, no borders. Photography should feel inviting, authentic and beautifully composed, not a sterile stock flat lay.
- `public/images/beauty-still-life.webp`: Use case: product-mockup. Asset type: original editorial brand photograph for a premium creator collaboration website. A beautiful magazine-quality landscape 3:2 still life of a generic unlabelled frosted glass perfume bottle and curved translucent amber glass on a pale travertine plinth, one fresh peach cut in half and a soft folded lilac linen fabric flowing across a warm cream plaster studio. Sophisticated tactile materials, soft late afternoon sunlight, artful but plausible product photography, gentle shadow geometry, refined warm color grading and crisp natural texture. Close composed three-quarter view with balanced negative space, premium beauty-and-lifestyle campaign mood. No people, no words, no labels, no logos, no watermarks, no UI. This is an aspirational decorative editorial image, not a real creator portfolio.

Only approved, consenting, non-demo creator profiles appear publicly. No fabricated testimonials, statistics, portraits or portfolio work are added. The local launch state currently has no approved creators, so public screenshots intentionally show empty states. Profile/card rendering with populated data is exercised by the existing marketplace tests.

Visual review artifacts are local, ignored files under `storage/framework/testing/`; generated assets, `.env` and PostgreSQL credentials remain ignored. Workspace screenshots use HTML rendered from isolated in-memory SQLite factory fixtures, without creating accounts in the real database.

## Verification

- Production build: `npm run build` passed. Existing optional Fontaine fallback notice remains; no package was added.
- Marketplace coverage: 44 passed, 1 PostgreSQL-specific skip under SQLite, 213 assertions.
- Full `composer ci:check` on PostgreSQL: Pint and PHPStan passed; all 79 tests passed with 297 assertions. SQLite also passed with 78 tests, one PostgreSQL-specific skip and 296 assertions.
- Chrome public review at 375/768/1024/1440px: home, discovery, registration and login; no horizontal overflow or JavaScript exceptions. Filter sheet confirmed modal, focus inside, and Escape close.
- Chrome workspace review at all four widths: creator overview/studio/campaigns/collaborations/settings and brand overview/business/campaigns/collaborations; no horizontal overflow or JavaScript exceptions in rendered fixture screens.
- Live Livewire interaction review: platform selection updates both controls and URL, reset clears both controls and URL while retaining the open dialog, and closing restores focus to the trigger.
- Visual review corrected the desktop menu-icon visibility, hero caption scale, neutral notice surfaces and inconsistent emoji arrows.
- Remaining: final populated-portfolio review requires genuine approved creator media; no substitute listings were published. Screen-reader/device testing remains a manual release check.
