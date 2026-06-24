# HTML Only — No CSS

This folder has a single page (`index.html`) with **zero CSS**. It exists to show what a browser renders by default when you only write HTML.

## What this teaches

- **HTML = structure, not style.** Tags describe what content *is* (a heading, a paragraph, a list), not how it should *look*.
- **Semantic tags**: `<header>`, `<nav>`, `<section>`, `<article>`, `<footer>` — each tag has a meaning, which helps browsers, screen readers, and search engines understand the page.
- **Headings hierarchy**: `<h1>` to `<h6>` should go in order (don't skip levels) to represent importance, not size — size is a CSS concern.
- **Lists**: `<ul>` (unordered/bullet list) vs `<ol>` (ordered/numbered list), both made of `<li>` items.
- **Links**: `<a href="...">` for navigation, including in-page anchors like `href="#about"` that jump to an element with `id="about"`.
- **Comments**: `<!-- like this -->` — ignored by the browser, used to leave notes for yourself.
- **Default browser styling**: notice headings are bold/large, links are blue and underlined, lists have bullets/numbers, spacing is inconsistent — none of that is something you wrote, it's the browser's built-in stylesheet.

## Try this

Open `index.html` directly in a browser and compare it against `../2-html-css/index.html`. Same content, same structure — the only difference is CSS.
