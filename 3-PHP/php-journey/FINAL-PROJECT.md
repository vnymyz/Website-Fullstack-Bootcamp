# Final Project — Pre-Laravel Checkpoint

**Goal:** prove she can combine CRUD + Auth + Roles + MVC herself, small scope, no framework crutch. This is the gate before Laravel.

## Suggested project: "Mini Task/Inventory Manager"
Pick one domain — task list, small inventory, simple blog posts, or event RSVP list. Keep single main entity (e.g. "Task", "Product", "Post") plus Users. Do not let scope grow to multiple related entities with JOINs — that's a Laravel-era problem.

## Required features
- **Auth:** register, login, logout, hashed passwords, session-protected pages.
- **Roles:** `admin` and `user`. Admin can manage all records + manage users' roles. Regular user can only manage their own records (`user_id` column ties records to owner).
- **CRUD:** full create/read/update/delete on main entity, server-side validated, prepared statements only.
- **MVC structure:** organized per Stage 9 pattern — models/, views/, controllers/, single front controller.
- **UI:** Bootstrap (or Tailwind if she's chosen to upgrade), responsive, flash messages on actions (created/updated/deleted).
- **Security baseline:** htmlspecialchars on all output, prepared statements on all queries, CSRF token on forms, no plaintext passwords.

## Explicitly out of scope (don't let it creep in)
- No AJAX/API/JSON endpoints — plain form POST/GET only.
- No file uploads unless she's already comfortable with Stage 9/10 material.
- No multi-table JOINs beyond the simple `user_id` ownership foreign key.
- No third-party PHP packages/Composer — that's Laravel's job next.

## Evaluation format (same style as the HTML/CSS/JS exam)
- Closed-book build window first (no AI) to prove she can start from a blank MVC skeleton and get login + one CRUD action working unaided.
- AI allowed only in a final, smaller window for polish (styling, edge-case handling) — not for the core logic.
- Bug-hunt add-on: give her a working copy with 2-3 planted issues (one SQLi-shaped, one missing role check, one XSS-shaped) to find and fix — same rationale as the fundamentals exam: debugging is what AI-assisted work can't fake.

## Pass criteria
- Register/login/logout works, passwords hashed.
- A logged-out user cannot reach protected pages by typing the URL directly.
- A regular user cannot edit/delete another user's record even by guessing the URL/id.
- Admin-only actions are blocked server-side for non-admins, not just hidden in the UI.
- All SQL uses prepared statements; no raw `$_POST`/`$_GET` concatenated into a query.
- Code is organized into models/views/controllers, not one giant file per page.

Once this passes, she's ready for Laravel — the pass criteria above map directly onto what Laravel's auth, gates/policies, Eloquent, and MVC folders do for you automatically.
