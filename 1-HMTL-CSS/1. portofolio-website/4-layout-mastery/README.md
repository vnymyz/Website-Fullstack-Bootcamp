# Layout Mastery — Level 4

Builds on `../3-responsive-portfolio`. Same portfolio, same sections, but now the *page itself* uses CSS Grid for layout, plus deeper flexbox control and positioning. Built **mobile-first**: base CSS in `style.css` targets phones, and `@media (min-width: ...)` queries layer on bigger-screen layout — opposite direction from folder 3's `max-width` query.

## What's new here (vs folder 3)

**Flexbox deep dive**
- `justify-content` controls spacing along the **main axis** (horizontal in a row, vertical in a column).
- `align-items` controls alignment along the **cross axis** (the other one).
- `flex-direction: column` (mobile nav) vs `row` (tablet+ nav) — same markup, different axis.
- `flex: grow shrink basis` shorthand on `.navbar li` — mobile uses `flex: 0 1 auto` (don't grow), tablet+ switches to `flex: 1 1 auto` (share space equally).

**Grid deep dive**
- `grid-template-areas` on `.layout`: names regions (`"about"`, `"skills"`, `"projects"`, `"contact"`) and assigns them with `grid-area`, instead of just counting columns. Much easier to read than tracking column numbers.
- **Gotcha (fixed)**: an earlier version nested About+Skills inside one `<aside>` and Projects+Contact inside one `<main>`, both as a single `"sidebar main"` row. That put both columns at the *same starting Y position*, so clicking the `#about` nav link and the `#projects` nav link scrolled to the same spot — About got visually buried under the taller Projects column. Fix: each section is now a **direct grid item** with its own named area (`about` / `skills` / `projects` / `contact`), so every nav anchor lands at a distinct row. Skills intentionally pairs beside Projects (common sidebar pattern), but About and Contact always get their own full-width row.
- **Gotcha #2 (fixed)**: even with distinct rows, the sticky header was overlapping each heading right after the jump — the heading scrolled to *exactly* the top of the viewport, which is right where the sticky header sits. Fixed with `scroll-margin-top: var(--header-offset)` on `.section` — this tells the browser to leave extra space above the section when scrolling it into view via an anchor. `--header-offset` changes per breakpoint (taller on mobile since the nav stacks into 4 lines, shorter on tablet+ where nav is one row). Also added `scroll-behavior: smooth` so the jump animates instead of snapping.
- **Explicit grid**: `.project-grid`'s `grid-template-columns` is something *you* defined (1 column on mobile, 2 on desktop).
- **Implicit grid**: you never defined how many *rows* exist — grid auto-generates them (`grid-auto-rows`) as cards overflow the columns. Add a 5th project card, a new row appears automatically.

**Positioning + stacking context**
- `position: relative` on `.avatar-wrap` — doesn't move itself, but becomes the anchor for...
- `position: absolute` on `.status-badge` — positioned relative to the nearest `position: relative` ancestor, not the whole page.
- `position: fixed` on `.back-to-top` — glued to the viewport corner, ignores scrolling entirely (different from `sticky` in folder 3, which moves with the page until it hits the top).
- `z-index` — controls stacking order when elements overlap (badge above avatar: `z-index: 1`; back-to-top above sticky header: `z-index: 20` > header's `z-index: 10`).

**Mobile-first media queries**
- No query = phone styles (the default now).
- `@media (min-width: 600px)` = tablet: nav goes horizontal.
- `@media (min-width: 900px)` = desktop: two-column grid layout via `grid-template-areas`, project grid goes 2 columns.
- Contrast with folder 3, which was **desktop-first** (`max-width: 600px` to shrink down for mobile). Both approaches exist in the real world — mobile-first is generally considered best practice today since most traffic is mobile.

## Try this

1. Resize the browser from narrow to wide slowly — watch nav go column→row at 600px, then the whole page go one-column→two-column at 900px.
2. Add a 5th `<article class="card">` in the HTML — see it land in a new row automatically (implicit grid in action).
3. In DevTools, hover over `.status-badge` and `.back-to-top` in the inspector to see their computed `position` and `z-index` values.

## What to learn next

This was the last *pure layout* stage. Recommended next steps from the curriculum in `../CLAUDE.md`:

- **Stage 5 — Polish**: `@keyframes` animations, `clamp()`/`min()`/`max()` for fluid type, CSS specificity rules, custom fonts, favicons/meta tags.
- **Stage 6 — Accessibility**: ARIA basics, focus states, keyboard nav, color contrast, semantic correctness.
- **Stage 7 — Workflow**: DevTools mastery, Git basics, W3C HTML validator.
- **Then JavaScript**: the contact form still doesn't submit anywhere, the nav has no mobile hamburger toggle — both need JS to actually function.
