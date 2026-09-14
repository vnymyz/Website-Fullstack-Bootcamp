# React JS Curriculum — Zero to Deep (Pre-Golang)

Stack: React (JS, no TS yet) + Vite + Tailwind + react-router-dom + json-server → small Laravel connect demo.
Student: finished HTML/CSS/JS, PHP, MySQL, Bootstrap/Tailwind, Laravel. New to React and SPA thinking, not new to programming.

**Format (updated per tutor request):** all 10 sessions are written as standalone Markdown guides directly under `5-React/react-journey/` (flat, no subfolder — `01-kenapa-react.md` … `10-laravel-connect-demo.md`, indexed by `react-journey/00-daftar-isi.md`), not as separate scaffolded Vite projects per stage. All hands-on work happens in one single project, `5-React/learn-react/`, which the student builds up incrementally by following the guides in order. This keeps her working in one real, continuously-growing codebase instead of 10 disconnected sandboxes.

**Big-picture sequencing (agreed with tutor):**
1. React deep, on its own, against a fake REST API (`json-server`) — this file.
2. A **small, connect-only** Laravel demo (stage 10) — proves React can talk to a backend she already knows. Not a feature project.
3. Golang stage (separate `6-GOLANG/go-journey/`, outlined at the bottom of this doc) — the **serious** project (Task Management, real DB design, auth) happens there, on Postgres.
4. **One** closed-book fullstack exam, after both backends have been connected — not a React-only exam. No exam is written in this pass.
5. **Deploy & hosting is taught once, at the very end**, after the exam project is done — for the real fullstack project (React + Go + Postgres), not piecemeal per stage. The only exception: an **optional, light** note in this React stage about hosting the frontend-only build (Stage 9, with `json-server`/local data) on Vercel, for a quick motivational "see it live" moment — not a full deploy lesson.

---

## Stage 0 — Environment Setup
**Goal:** Node.js + npm working, first Vite React app rendering.

- Install Node.js LTS, verify `node -v` / `npm -v`.
- Explain: unlike PHP (server reads file, sends HTML per request), React ships JS to the browser; the browser itself builds the page. New mental model — "the kitchen moves into the customer's house."
- `npm create vite@latest` → pick React → `npm install` → `npm run dev`.

**Checkpoint:** she explains why `npm run dev` starts a server locally even though React "runs in the browser."

---

## Sesi 1 — Kenapa React
**Goal:** feel the problem React solves before learning its syntax.

Topics:
- Side-by-side: a small vanilla-JS DOM update (reuse a piece of `2-JAVASCRIPT/simple-crud`) vs. the same feature in React — same result, different amount of manual DOM work.
- Analogy (continuing waiter/chef): vanilla JS = repainting the wall by hand every time something changes; React = describe how the wall should look, React repaints only the changed bricks (virtual DOM, one sentence, not a deep dive).
- Project anatomy: `index.html`, `main.jsx`, `App.jsx`, `package.json`, `node_modules` (why gitignored), `vite.config.js`.
- `npm run dev` vs `npm run build` + `npm run preview` — dev server vs a real static build.

Guide: `react-journey/01-kenapa-react.md` — all code inline in the guide, built in `learn-react/`.

**Checkpoint:** in her own words, why does React need a build step and plain HTML/CSS doesn't?

---

## Sesi 2 — JSX & Component
**Goal:** JSX syntax rules, function components, composition.

Topics:
- JSX is not HTML: `className` not `class`, `htmlFor` not `for`, self-closing tags (`<img />`), `{}` for embedding JS expressions, one root element or `<>...</>` Fragment.
- Function components: PascalCase naming, `export default` vs named exports, import paths.
- Composition: split one page into `Navbar`, `Card`, `Footer` components. Tie explicitly to PHP `include`/`require` she already knows — same reuse idea, different mechanism (function call vs file include).
- Styling: quick tour of options (plain CSS, CSS modules, Tailwind) — **pick Tailwind** since she already knows it, set it up once here and use it for the rest of the curriculum.

Guide: `react-journey/02-jsx-dan-component.md`.

**Latihan:** rebuild her static portfolio homepage as composed components + Tailwind.

---

## Sesi 3 — Props & Rendering List
**Goal:** passing data down, rendering arrays, conditional rendering.

Topics:
- Props = function parameters for components. Destructuring in the parameter list, default values, the special `children` prop.
- `.map()` to render a list of components; the `key` prop and **why using array index as key breaks** (live demo: reorder or delete an item, watch state jump to the wrong row).
- Conditional rendering: `&&`, ternary `? :`, early `return null`. The classic `0 && <Thing />` footgun (renders a stray `0`).

Guide: `react-journey/03-props-dan-rendering-list.md`.

**Latihan:** product/task card grid driven by a plain JS array of objects.

**Bug hunt (`latihan/bug-hunt-list.jsx`):** broken list with 3 planted bugs — index used as key on a reorderable list, missing `return` inside `.map`, an object rendered directly as a JSX child (`{item}` instead of `{item.name}`). Answer key in `latihan/bug-hunt-list-jawaban.md` explaining each fix.

---

## Sesi 4 — State & Event
**Goal:** the single most important stage — state, events, immutability. Do not rush; 2 sessions minimum.

Topics:
- `useState`: state vs. a plain variable — why reassigning a normal `let` doesn't trigger a re-render.
- Event handlers: `onClick={fn}` vs `onClick={fn()}` (the second calls it immediately during render — classic first mistake).
- **Immutability**: updating arrays/objects via spread (`[...arr]`, `{...obj}`), `.map`, `.filter` — never `.push`, never `state.x = y` directly.
- Functional updates: `setCount(c => c + 1)`, and why state updates are asynchronous/batched (demo: two `setCount(count + 1)` calls in a row don't add 2).
- Lifting state up — "state lives in the nearest common parent that needs it."
- Derived state — compute a value during render from existing state; don't store a second copy of something you can calculate.

Guide: `react-journey/04-state-dan-event.md`.

**Latihan:** counter → todo list (add/toggle/delete) → filterable todo list (all/active/done).

**Bug hunt (`latihan/bug-hunt-state.jsx`):** a component that mutates state directly (`todos.push(...)` then `setTodos(todos)`) and renders stale; a stale-closure counter inside `setTimeout`.

**Checkpoint (closed-book):** from a blank Vite project, no AI, no notes, she builds a working add/delete/toggle list.

---

## Sesi 5 — Form & Controlled Input
**Goal:** controlled forms, the SPA way of handling input vs. PHP's page-reload POST.

Topics:
- Controlled inputs: `value` + `onChange`, one state object for a whole form using the `e.target.name` pattern to keep one handler for every field.
- `onSubmit` + `e.preventDefault()` — explicitly contrast with the PHP form flow she knows (page reload, `$_POST`, `header("Location: ...")`).
- Validation and error messages, disabling submit while invalid, resetting the form after a successful submit.
- Other input types: checkbox, radio, select, textarea, all controlled the same way.

Guide: `react-journey/05-form-dan-controlled-input.md`.

**Latihan:** sticky, validating task form with the **same validation requirements** as the PHP sticky form from `3-PHP/php-journey/2-Forms-and-Superglobals/` — so she can compare the two approaches directly, side by side.

---

## Sesi 6 — useEffect & Fetch
**Goal:** talking to a real (fake) backend — `json-server`, `useEffect`, async data.

Topics:
- Install & run `json-server` against a `db.json` file — a real REST API with no backend code.
- `useEffect`: what a "side effect" is, the dependency array (`[]` vs `[id]` vs omitted), the cleanup function.
- **The three states of every request** — loading / error / success — always rendered, from the very first fetch she ever writes. No shortcuts here.
- `fetch`: GET first, then POST/PUT/DELETE, then refetch-after-write pattern.
- Why the `useEffect` callback itself cannot be `async` (and the `async function inside` workaround).

Guide: `react-journey/06-useeffect-dan-fetch.md`.

**Bug hunt (`latihan/bug-hunt-effect.jsx`):** the classic infinite-loop effect — an object/array recreated every render inside a missing or wrong dependency array, causing endless refetches.

**Checkpoint:** given a dependency array, she predicts correctly when the effect re-runs.

---

## Sesi 7 — Router Multi Halaman
**Goal:** multi-page feel in a single-page app.

Topics:
- `react-router-dom` v7: `BrowserRouter`, `Routes`, `Route`, `Link` vs `<a>` (demo the full-page-reload difference).
- `useParams` for detail pages (`/tasks/:id`), `useNavigate` for redirect-after-submit.
- Nested routes and a shared layout via `Outlet`; a catch-all 404 route.
- Protected route pattern — stubbed auth check for now (`isLoggedIn` boolean), real token check comes in stage 10.

Guide: `react-journey/07-router-multi-halaman.md`.

---

## Sesi 8 — Custom Hook & Context
**Goal:** extracting reusable logic, sharing state without prop-drilling everywhere.

Topics:
- Extracting `useFetch` and `useLocalStorage` custom hooks from earlier stages' code; rules of hooks (top-level only, no conditionals).
- `useContext` for things like auth/theme that many components need — and an explicit note on when **not** to reach for it (it is not a blanket replacement for props).
- `useRef`: focusing an input, holding a mutable value that must not trigger a re-render.
- Brief honest note on `useMemo` / `useCallback`: measure a real slowdown first, don't sprinkle them by habit.

Guide: `react-journey/08-custom-hook-dan-context.md`.

---

## Sesi 9 — Project TaskFlow Lite ⭐ (React practice project, not the portfolio piece)
**Goal:** consolidate stages 2–8 into one coherent from-zero build against `json-server`.

Explicitly **not** the serious/portfolio project — that is built later in the Golang stage on a real backend. This one exists purely to cement React fundamentals.

Features:
- Projects list → project detail page → tasks inside a project.
- Full CRUD on tasks: create, edit, delete, toggle status, priority, due date.
- Search, filter, sort.
- Reusable `Modal`, `Button`, `EmptyState`, `Toast` components.
- Loading skeletons, confirm-before-delete.
- Optimistic UI update on status toggle, with rollback if the request fails.

Guide: `react-journey/09-project-taskflow-lite.md`, written as **numbered build steps** (same shape as the PHP CRUD app's guide) — never a finished code dump.

**Checkpoint (small, closed-book):** she adds one new field end-to-end (form input → state → json-server → showing in the list) without help.

---

## Sesi 10 — Laravel Connect Demo (small, connect-only — not a feature project)
**Goal:** prove a React app can talk to a backend she already knows. Nothing more.

App: a tiny **Notes CRUD with login** — one or two tables. Deliberately small.

Topics:
- Laravel side: one migration, one model, `routes/api.php`, an API Resource returning JSON.
- **CORS** — what it is, why it only shows up now, `config/cors.php`.
- Laravel Sanctum token auth: login returns a token, React stores it (state/context, discuss localStorage tradeoffs), sends `Authorization: Bearer <token>`, logout clears it.
- A real protected route; a 401 response redirects to login.
- **The lesson of this module, stated explicitly:** swapping `json-server` for Laravel changed only the base URL and the addition of an auth header — almost none of the React code from Stage 9 had to change. This is exactly what makes plugging in the Golang backend later feel routine instead of scary.
- Short sidebar: what **Supabase** is (Postgres + instant API/auth, "no backend code at all") shown as a contrast, and a one-paragraph note on what **Inertia.js** is and why this curriculum deliberately did not use it (it hides the frontend/backend boundary this whole stage exists to teach).

Guide: `react-journey/10-laravel-connect-demo.md`.

---

## Appendix — Optional Bonus (not one of the 10 sessions)

A quick "see it live on the internet" motivational win, folded into the end of `react-journey/09-project-taskflow-lite.md` rather than its own session: `npm run build`, then push the TaskFlow Lite build to **Vercel** (frontend-only, local/`json-server` data — no real backend to host yet). No env vars, no CORS, no backend deploy here. The real, full deploy lesson (React + Go + PostgreSQL, all pieces hosted together) is taught **once, at the very end of the whole curriculum, after the closed-book fullstack exam** — see the Golang outline below.

---

## Exam — deferred, not part of this document's stages

No React-only exam is written here. Per the agreed sequencing, the **single closed-book fullstack exam** happens at the end of the Golang stage (see `6-GOLANG/go-journey/CURRICULUM.md`), after the student has connected React to both a Laravel and a Golang backend. Until then, assessment is the small per-stage checkpoints and bug hunts listed above.

---

## Teaching Method (carried over from php-journey)

- Practical, build-from-scratch over theory/multiple-choice.
- A debugging/bug-hunt exercise in every stage where a subtle mistake is common (3, 4, 6) — hardest thing to fake with AI-copied code.
- Closed-book checkpoints between major stages to confirm real understanding, not just "does it look done."
- Student-facing material (README, code comments) in Indonesian; this curriculum doc and the tutor prompt stay in English.
- Every project is built as numbered steps from an empty folder — never handed over as a finished dump.

---

## Golang Stage (outline only — see `6-GOLANG/go-journey/CURRICULUM.md`)

The Golang stage is where the **serious, portfolio-grade project** lives: a Task Management app with a real Go + PostgreSQL backend (users → projects → tasks, foreign keys, joins, ownership rules), closing with the one fullstack exam. **Deploy & hosting is taught once, at the very end, after that exam** — not per-stage. Detailed stage-by-stage material for Golang is written after this React stage is actually running with the student, not before.
