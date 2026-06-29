# Polish — Level 5

Builds on `../4-layout-mastery`. Same portfolio structure, same layout — this stage is purely about making it look and feel more professional.

## What's new here (vs folder 4)

**`@keyframes` animations**
- `@keyframes name { from { } to { } }` defines a reusable named animation with as many steps as you want (use `0% / 50% / 100%` for more than two steps).
- Apply with: `animation: fadeInUp 0.6s ease both;`
  - `0.6s` = duration
  - `ease` = timing function (how it speeds/slows)
  - `both` = keeps end state after animation finishes
- Three demos here: `fadeInUp` (sections load in), `pulse` (status badge glows), `spin` (could be used for a loading spinner).
- Compare to `transition` (folder 2): transition only moves between two states triggered by hover/focus. `@keyframes` runs automatically and supports unlimited steps.

**`clamp(min, preferred, max)`**
- `font-size: clamp(1.2rem, 3vw, 2rem)` — font scales with viewport width (`3vw`) but is clamped: never below `1.2rem`, never above `2rem`.
- Replaces writing separate media queries just for font sizes.
- `min()` and `max()` work similarly: `width: min(800px, 100%)` means "whichever is smaller."

**CSS specificity**
- Every selector has a score: `(id count, class count, element count)`
  - `p` → `(0,0,1)`
  - `.card` → `(0,1,0)`
  - `.card.featured` → `(0,2,0)` ← wins over `.card` for same property
  - `#about` → `(1,0,0)` ← id always beats any number of classes
- Demo: `.card.featured { border-color: blue; }` overrides `.card { border-color: transparent; }` because two classes beat one.
- Rule of thumb: avoid `!important` — if you need it, you have a specificity problem. Fix the selector instead.

**Custom font — Google Fonts**
- Three `<link>` tags in `<head>`: two `preconnect` (speeds up DNS), one actual font URL.
- In CSS: `font-family: "Inter", Arial, sans-serif` — Inter loads first, Arial is fallback if it fails.
- `display=swap` in the Google URL = `font-display: swap` behavior, prevents invisible text while font loads.

**Favicon**
- `<link rel="icon" href="favicon.svg" type="image/svg+xml">` in `<head>`.
- SVG favicons work in all modern browsers and scale perfectly at any size.
- No Photoshop needed — a 3-line SVG file is enough (see `favicon.svg`).

**Open Graph meta tags**
- `<meta property="og:title">`, `og:description`, `og:type` in `<head>`.
- These are read by WhatsApp, Discord, Twitter, LinkedIn when someone pastes a link — controls the preview card (title, description, image).
- No visible effect locally; visible when page is deployed publicly.

## Try this

1. Open DevTools → Network tab → reload page. Watch `Inter` font load separately.
2. Change `@keyframes fadeInUp` — try `from { opacity: 0; transform: scale(0.8); }` for a zoom-in effect.
3. Add a third class to `.card.featured` in HTML — check if specificity still wins with three classes vs one.
4. Change `clamp(1.2rem, 3vw, 2rem)` on `h2` — resize window slowly and watch heading size respond fluidly.

## What to learn next

- **Stage 6 — Accessibility**: ARIA basics, `:focus-visible`, color contrast, semantic correctness (`<button>` not `<div onclick>`).
- **Stage 7 — Workflow**: DevTools mastery, Git basics, W3C HTML validator.
- **Then JavaScript**: nav hamburger toggle, real form submission, DOM manipulation — all the things that currently need JS to work.
