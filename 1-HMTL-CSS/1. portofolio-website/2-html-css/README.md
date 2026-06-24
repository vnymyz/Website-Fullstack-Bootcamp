# HTML + CSS

This folder has the same content as `../1-html-only`, but now `index.html` is linked to `style.css`. Compare both to see exactly what CSS adds on top of plain HTML.

## What this teaches

- **Connecting CSS to HTML**: `<link rel="stylesheet" href="style.css">` in `<head>` — this is what makes the browser load and apply the stylesheet.
- **Selectors** — how CSS targets HTML elements:
  - Element selector: `body { }` → every `<body>` tag
  - Class selector: `.card { }` → any tag with `class="card"` (reusable, can apply to many elements)
  - ID selector: `#about { }` → the one tag with `id="about"` (unique, only one per page)
  - Combinators: `.navbar a { }` → only `<a>` tags inside something with class `navbar`
- **The box model**: every element is a box with `padding` (inside spacing), `border`, and `margin` (outside spacing). `box-sizing: border-box` makes width calculations predictable.
- **Reset styles**: `* { margin: 0; padding: 0; }` removes inconsistent browser defaults so you start from a clean baseline.
- **Colors & typography**: `color`, `background-color`, `font-family`, `font-size`, `line-height`.
- **Flexbox** (`.navbar ul`): `display: flex` lines up list items in a row instead of stacking vertically — used here for the nav menu.
- **Grid** (`.project-grid`): `display: grid` with `repeat(auto-fit, minmax(...))` creates a responsive multi-column layout for project cards without manual breakpoints.
- **Pseudo-classes**: `:hover` changes styling when the mouse is over an element (e.g. link color change, card lift effect).
- **Transitions**: `transition: transform 0.2s` animates property changes smoothly instead of snapping instantly.
- **Layout centering**: `margin: 0 auto` with a `max-width` centers a block horizontally on the page.

## Try this

1. Open `index.html` in a browser, then open `../1-html-only/index.html` next to it — same markup, completely different look.
2. In `style.css`, change a value (e.g. `background-color`, `gap`, or `grid-template-columns`) and refresh the browser to see the effect immediately.
