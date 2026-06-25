# Responsive Portfolio — Level 3

Builds on `../2-html-css`. Same portfolio idea, new HTML elements and CSS techniques layered on top.

## What's new here (vs folder 2)

**HTML**
- `<img>` with `alt` text — images need alt text for accessibility and as fallback if the image fails to load.
- A real `<form>` with `<label>`, `<input>`, `<textarea>`, `<button>` — `for`/`id` pairing links label to input (click label, input gets focus). `required` does basic browser-side validation.
- `type="email"` — browser validates email format automatically.

**CSS**
- **CSS variables** (`:root { --color-primary: ...; }` and `var(--color-primary)`): define a value once, reuse it everywhere. Change a color in one place, the whole page updates.
- **`calc()`**: do math with variables/units directly in CSS, e.g. `calc(var(--spacing) * 2)`.
- **`position: sticky`**: the header stays pinned to the top of the screen while you scroll, instead of scrolling away.
- **Pseudo-elements** (`::after`): generate a fake element from CSS alone (no extra HTML) — used here for the animated underline on nav links.
- **`object-fit: cover`** + `border-radius: 50%`: crop an image to fill a box neatly and turn it into a circle — common avatar pattern.
- **Flexbox `flex-wrap: wrap`**: lets items (skill tags) flow onto multiple lines instead of overflowing or shrinking.
- **Media queries** (`@media (max-width: 600px) { ... }`): apply different CSS rules depending on screen width — this is what makes a page "responsive." Below 600px, the header stacks vertically and the grid becomes one column.
- **`:focus`** pseudo-class: visual feedback when a form field is actively selected, important for usability and accessibility.
- **`scroll-margin-top`** (gotcha fix): since the header is `sticky`, clicking a nav link used to scroll the section heading right underneath it (hidden). `scroll-margin-top: var(--header-offset)` on `.section` reserves space above the section so the heading lands fully visible. `--header-offset` is bigger on mobile (header stacks taller) and smaller on desktop, switched via the media query. Added `scroll-behavior: smooth` too, so the jump animates.

## Try this

1. Resize your browser window (or open dev tools device toolbar) and shrink it below 600px — watch the header and grid restack.
2. Change `--color-primary` in `:root` to a different color — see it update in the nav underline, skill tags, and button all at once.

## What to learn next (beyond this folder)

You now know real HTML/CSS fundamentals. Next steps, roughly in order:

1. **CSS Flexbox & Grid in depth** — `align-items`, `justify-content`, `flex-grow/shrink`, grid areas. You've used the basics; go deeper.
2. **More responsive design** — multiple breakpoints (tablet + desktop), `clamp()` for fluid font sizes, mobile-first vs desktop-first approach.
3. **Accessibility (a11y)** — semantic landmarks, `aria-*` attributes, keyboard navigation, color contrast.
4. **CSS animations** (`@keyframes`) — beyond simple `transition`, build multi-step animations.
5. **JavaScript basics** — this is the big next jump. Forms here don't actually submit anywhere; JS is what makes a mobile hamburger menu toggle, validates forms beyond browser defaults, and makes the page interactive.
6. **CSS preprocessors / methodology** (optional) — Sass, BEM naming convention, or utility frameworks like Tailwind, once you're comfortable with plain CSS.
7. **Deploying the site** — once happy with it, host it for free (GitHub Pages, Vercel, Netlify) so it's a live link you can share.

Suggested immediate next folder: `4-javascript-basics` — add a working mobile nav toggle and form validation with vanilla JS.
