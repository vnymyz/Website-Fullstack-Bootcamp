# PHP Fullstack Curriculum — Zero to Hero (Pre-Laravel)

Stack: PHP + MySQL + Bootstrap → Tailwind + Laragon.
Student: finished HTML/CSS/JS, understands frontend/backend via waiter/chef analogy, new to server-side + SQL.

Folder convention: each stage = own numbered folder under `php-journey/`, files inside are runnable `.php` (open via `http://localhost/php-journey/N-Stage-Name/` in Laragon, not `file://`).

---

## Stage 0 — Environment Setup
**Goal:** Laragon running, Apache + MySQL up, first `.php` file rendering in browser.

- Install Laragon, start Apache + MySQL.
- Explain: `www` root = server's "kitchen", browser request = "order ticket", PHP = "chef" who cooks HTML and sends it back.
- First file: `<?php echo "Hello Chef"; ?>` in `1-Fundamental-PHP/index.php`.
- Show difference: opening file directly (`file:///...`) shows raw PHP code (broken) vs via `localhost` (server executes it, sends HTML).

**Checkpoint:** she can explain in her own words why PHP needs a server to run and HTML/JS don't.

---

## `1-Fundamental-PHP/`
**Goal:** core language mechanics.

Topics:
- `<?php ?>` tags, `echo`/`print`, comments.
- Variables (`$name`), data types, `var_dump()`, type juggling (PHP is loose-typed — contrast with what she knows from JS `typeof`).
- String interpolation `"Hello $name"` vs concatenation `.`.
- Operators, `if/elseif/else`, `switch`.
- Loops: `for`, `while`, `foreach` (tie to JS `for...of` she already knows).
- Arrays: indexed vs associative (compare to JS array vs object).
- Functions: params, default values, return, `array` type hints optional.
- `include`/`require` vs `include_once`/`require_once` — reuse pieces of "kitchen" across pages (header/footer).

Files to build: `index.php`, `variables.php`, `loops.php`, `arrays.php`, `functions.php`, `includes-demo/` (header.php, footer.php, page.php).

**Mini exercise:** FizzBuzz, then a "receipt calculator" (array of items + prices → total with loop).

---

## `2-Forms-and-Superglobals/`
**Goal:** PHP receiving data from the browser — the actual waiter/chef handoff.

Topics:
- HTML `<form method="GET">` vs `method="POST"` — when to use which.
- `$_GET`, `$_POST`, `$_REQUEST`, `$_SERVER`.
- Reading form input, echoing it back.
- Basic validation: `empty()`, `isset()`, `trim()`.
- **XSS intro:** why raw `echo $_POST['name']` is dangerous, `htmlspecialchars()` as the fix. Introduce this early, not as an afterthought.
- Redirect after POST pattern (`header("Location: ...")`) to prevent resubmission.

Files: `form.php`, `process.php`, `validation-demo.php`.

**Mini exercise:** contact form that validates required fields and re-displays errors without losing entered values (sticky form).

---

## `3-MySQL-Basics/`
**Goal:** SQL fundamentals, independent of PHP first.

Topics:
- What a database/table/row/column is (pantry analogy — organized storage chef pulls from).
- phpMyAdmin (bundled with Laragon) to create DB/tables visually first.
- SQL: `CREATE TABLE`, `INSERT`, `SELECT` (with `WHERE`, `ORDER BY`, `LIMIT`), `UPDATE`, `DELETE`.
- Data types (`INT`, `VARCHAR`, `TEXT`, `DATE`, `BOOLEAN`), `PRIMARY KEY`, `AUTO_INCREMENT`.
- Basic relationships concept: foreign key (preview only, no JOIN yet).

No PHP files yet — this stage is pure SQL practiced in phpMyAdmin's SQL tab, saved as `.sql` files in `3-MySQL-Basics/queries.sql`.

**Checkpoint:** closed-book — she writes CREATE TABLE + INSERT + SELECT with WHERE from scratch, no AI, no notes.

---

## `4-CRUD-App/`
**Goal:** PHP talks to MySQL. The big one — Create, Read, Update, Delete.

Topics:
- `mysqli` (procedural, matches PHP style already taught) — connect via `mysqli_connect()`.
- **Prepared statements from day one** (`mysqli_prepare`, bind params) — do not teach raw string-concatenated queries even as a "bad example first" unless immediately followed by the fix. SQL injection is a habit to build correctly from the start.
- Read: fetch rows, loop + display in HTML table (Bootstrap table, see Stage 7 in parallel or after).
- Create: form → insert → redirect.
- Update: fetch one row into edit form → update.
- Delete: confirm → delete, with GET-based id param + POST for actual delete action (avoid delete-via-link security habit).
- Connection file pattern: `config/db.php` included everywhere (first taste of separating "config" from "pages").

Suggested file layout:
```
4-CRUD-App/
  config/db.php
  index.php        (list/read)
  create.php
  edit.php
  delete.php
```

**Project inside stage:** simple "Notes" or "Products" CRUD — student's choice of domain, same structure.

**Checkpoint:** closed-book practical — given empty table, build full CRUD for a new entity (e.g. "Books") in class, no AI.

---

## `5-Sessions-and-Auth/`
**Goal:** login/register/logout — "who's ordering."

Topics:
- `session_start()`, `$_SESSION`, how sessions persist across pages (cookie = "table number" analogy).
- Password hashing: `password_hash()` / `password_verify()` — never store plaintext, ever.
- Register form → insert hashed password.
- Login form → verify → set `$_SESSION['user_id']`.
- Logout → `session_destroy()`.
- Protecting pages: check `isset($_SESSION['user_id'])` at top of protected pages, redirect if not logged in — build this as a reusable `auth-check.php` include.

Files:
```
5-Sessions-and-Auth/
  config/db.php
  register.php
  login.php
  logout.php
  dashboard.php     (protected page demo)
  includes/auth-check.php
```

**Checkpoint:** she explains why password_hash instead of storing plaintext, and what happens if `auth-check.php` include is missing from a page.

---

## `6-Role-Management/`
**Goal:** authorization — not just "logged in" but "allowed to do this."

Topics:
- Add `role` column to users table (`admin`, `user`).
- Store role in session on login.
- Conditional UI: show/hide admin links based on role.
- Server-side gate (critical lesson: hiding a button is NOT security — every protected action must re-check role server-side, not just hide the link in HTML).
- Simple admin panel: list users, change a user's role, delete a user (admin-only).

Files:
```
6-Role-Management/
  config/db.php
  includes/auth-check.php
  includes/admin-check.php
  admin/dashboard.php
  admin/users.php
```

**Checkpoint / bug hunt:** give her a page where the admin link is hidden via CSS/JS but the backend page has no role check — she must find and fix the vulnerability herself.

---

## `7-Buku-dan-Favorite/`
**Goal:** first real "content" feature built on top of Stage 4-6 (CRUD + Auth + Role) — books shown to users, admin manages the catalog, logged-in users can favorite books. Bridges the gap between isolated CRUD/auth exercises and an actual small feature that combines them.

Topics:
- New table `buku` (admin-managed catalog) and `favorit` (many-to-many between `users` and `buku`, first real use of a join table).
- Admin CRUD for `buku` (reuses the Stage 4 CRUD pattern, admin-only via `admin-check.php`).
- Public/user-facing catalog page (`katalog.php`) — logged-in users can favorite/unfavorite a book.
- Dashboard shows relevant data pulled from the database instead of static text: user dashboard shows "my favorites," admin dashboard adds book/favorite counts to the existing stat cards.
- Homepage shows a small "latest books" preview — first time `index.php` reads from the database instead of being static copy.

Files:
```
7-Buku-dan-Favorite/
  config/db.php
  includes/auth-check.php
  includes/admin-check.php
  includes/admin-sidebar.php
  setup.sql
  index.php
  register.php / login.php / logout.php
  dashboard.php
  katalog.php
  favorit-toggle.php
  admin/dashboard.php
  admin/buku.php
```

**Checkpoint:** she can explain why `favorit` needs its own table instead of a column on `users` or `buku` (many-to-many relationship), and why the favorite toggle has to re-check `$_SESSION['user_id']` server-side instead of trusting a hidden form field for which user is favoriting.

---

## `8-Bootstrap-Integration/`
**Goal:** make everything from Stage 1-7 look decent, fast, using a framework instead of custom CSS.

Topics:
- CDN vs local Bootstrap.
- Grid system (container/row/col), navbar, forms, tables, buttons, alerts, modals.
- Retrofit CRUD app (Stage 4), Auth (Stage 5), and the book catalog (Stage 7) pages with Bootstrap components.
- Flash messages using session (e.g. "Item deleted" alert after redirect) — combines PHP sessions + Bootstrap alert component.

**Mini exercise:** restyle the Stage 4 CRUD table + forms with Bootstrap, add a Bootstrap navbar showing "Login" or "Logout (username)" depending on session state.

---

## `9-Tailwind-Upgrade/`
**Goal:** introduce utility-first CSS as the "upgrade," once Bootstrap fundamentals are solid.

Topics:
- Why utility-first differs from component-based (Bootstrap gives you `.btn`, Tailwind gives you `flex`, `p-4`, `rounded` etc.).
- Tailwind CLI or CDN Play setup with Laragon project.
- Rebuild one page (e.g. login form) in Tailwind instead of Bootstrap, side-by-side comparison.
- Not a full re-do of every stage — just enough to make the switch comfortable for future framework work (Laravel commonly pairs with Tailwind).

---

## `10-MVC-Pattern/`
**Goal:** reorganize what she's already built into Model-View-Controller, by hand, before Laravel does it for her.

Topics:
- The problem MVC solves: Stage 4-6 files mix SQL queries, PHP logic, and HTML in one file — fine for learning, painful to maintain. MVC separates "what data" (Model), "what it looks like" (View), "what happens on request" (Controller).
- Waiter/chef analogy extension: Controller = waiter taking the order and deciding what to do, Model = chef/kitchen (talks to the pantry/database), View = the plated dish sent back to the table.
- Front controller concept: single `index.php` entry point routing to different "pages" via `?page=` or basic URL parsing, instead of one `.php` file per URL — the idea behind Laravel's router, built manually so it's not a black box later.
- Model: a plain PHP class wrapping the mysqli queries for one table (e.g. `Book.php` with `getAll()`, `find($id)`, `create()`, `update()`, `delete()`) — first real use of PHP classes/OOP if not introduced earlier (brief OOP primer: `class`, `->`, `$this`, `__construct` — enough to read Laravel code later, not a deep OOP course).
- View: plain `.php` files that only receive variables and output HTML — no SQL, no business logic allowed in these files.
- Controller: receives request, calls Model, picks a View, passes data to it.

Files:
```
10-MVC-Pattern/
  config/db.php
  models/Book.php
  views/books/index.php
  views/books/create.php
  views/books/edit.php
  controllers/BookController.php
  index.php            (front controller / router)
```

**Refactor exercise:** take the Stage 4 CRUD app (or Stage 6 role management) and rebuild it in this MVC shape — same functionality, different organization. This is the bridge exercise into Laravel's own MVC structure.

**Checkpoint:** she can point at any file and say which of M/V/C it is and why, and explain why mixing SQL into a View file would be wrong.

---

## `11-Security-Hardening/`
**Goal:** consolidate all the security lessons scattered through Stages 2-6 into deliberate practice.

Topics recap + drills:
- SQL injection (prepared statements) — try to break an intentionally-vulnerable demo page, then fix it.
- XSS (`htmlspecialchars` on all output).
- CSRF basics (hidden token field on forms, verify on submit) — new concept, introduce here.
- File upload validation if covered (extension/mime check, don't trust filename).
- Environment separation concept: never commit real passwords, intro to `.env`-style thinking (prepares her for Laravel's `.env`).

**Checkpoint:** closed-book — hand her a small vulnerable app (SQLi + XSS planted), she finds and patches both without AI.

---

## `12-Final-Project/`
See `FINAL-PROJECT.md` for full spec.

---

## Suggested Pacing
Roughly one stage per session/week depending on session length; Stage 4 (CRUD) and Stage 5 (Auth) are the heaviest and can each span 2 sessions. Insert a closed-book checkpoint before moving from Stage 4→5, 5→6, 6→7, and 7→11 (the ones already marked above) — same rule as her HTML/CSS/JS exam: practical build + bug hunt, not multiple choice, AI allowed only in a small final window if at all.
