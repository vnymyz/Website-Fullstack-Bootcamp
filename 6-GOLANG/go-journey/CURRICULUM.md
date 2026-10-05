# Golang Curriculum — Outline (Detailed Later)

Stack: Go (stdlib-first, `net/http`) → PostgreSQL (`pgx`) → JWT auth → the serious fullstack project → the one closed-book exam → deploy.
Student: has completed `3-PHP/php-journey/`, `4-LARAVEL/`, and `5-React/react-journey/` (see `panduan/00-daftar-isi.md` there). Comfortable with SQL (MySQL), REST APIs, and React as a client.

This file is a **roadmap only**. Detailed session-by-session material (mirroring the Markdown-guide format used in `5-React/react-journey/panduan/`) gets written after the React stage is actually running with the student, not before — so the pacing reflects how she actually did with React first.

---

## Sequencing Decided So Far

1. **Go deep, on its own** — syntax, stdlib HTTP, no framework yet (deliberately, so a framework later feels like a convenience, not magic).
2. **PostgreSQL** replaces MySQL for this stage — idiomatic in Go (`pgx`), her SQL knowledge transfers almost entirely, free managed hosting (Neon/Supabase) at deploy time.
3. **The serious, portfolio-grade project lives here**: Task Management, full Go + PostgreSQL backend + a fresh React frontend. Real relations (users → projects → tasks), real auth, real deploy.
4. **The single closed-book fullstack exam** happens after that project, testing React + Go + Postgres together — not a Go-only exam.
5. **Deploy & hosting is taught once, here, after the exam** — the only full deploy lesson in the entire curriculum (React stage only had an optional light Vercel-for-frontend-only note).
6. **TypeScript starts here**, at the point static typing (Go's) is already being learned anyway — not introduced earlier in the React stage.

---

## Outline

1. **Go fundamentals** — syntax, static typing (contrast with JS/PHP's dynamic typing), structs, slices, maps, pointers, `error` as a return value (no exceptions/try-catch), `go mod`.
2. **`net/http` from scratch** — handlers, `ServeMux`, JSON encode/decode, no framework. The goal: she understands what a framework like Gin/Echo/Fiber would later be doing FOR her, instead of it feeling like magic from day one.
3. **PostgreSQL** — install, `psql`/pgAdmin, a MySQL→Postgres diff cheatsheet (`SERIAL`/`IDENTITY`, `RETURNING`, quoting/casing rules), `pgx` driver, migrations (raw SQL files or a lightweight migration tool).
4. **First Go REST API** — rebuild the tiny Notes endpoints from the React stage's Laravel demo (`5-React/react-journey/panduan/10-laravel-connect-demo.md`), in Go, so the same React client points at Go by changing one env var. **The payoff moment: same frontend, third backend, and it barely notices.**
5. **Middleware, JWT auth, password hashing** (`bcrypt`), request validation, structured logging.
6. **Project layout** (`cmd/`, `internal/`), config/env handling, graceful shutdown, an intro to `go test`.
7. ⭐ **The serious project — Task Management, fullstack, from zero.** Go + PostgreSQL backend (users, projects, tasks; foreign keys, joins, ownership rules, pagination, search) with a fresh React frontend built alongside it. This is the portfolio piece — multiple sessions, built step by step, numbered from an empty folder like every other project in this bootcamp. *(An anime/movie tracker stays available as an optional lighter alternative if motivation needs a boost — but it leans on an external public API and teaches noticeably less Go/SQL, so it's the fallback, not the default.)*
8. **The single closed-book fullstack exam** — a new resource, built end to end: DB schema → Go endpoints → React UI → auth, plus a fullstack bug hunt spanning both sides. Ships with a `mockup/` folder and a tutor-only `KUNCI-JAWABAN-GURU.md`, matching the pattern already used in `3-PHP/php-journey/9-Ujian-Project/` and `1-HMTL-CSS/1. portofolio-website/`.
9. **Deploy & hosting — taught once, here, after the exam.** Single Go binary, Docker, hosted PostgreSQL (Neon/Supabase), React build on Vercel, env vars, wiring the real URLs together. This is the only full deploy lesson in the whole curriculum.

---

## Format

Whether this becomes per-session Markdown guides under `go-journey/panduan/` (mirroring the React stage) or scaffolded Go modules per stage is a decision to make once this stage actually starts — the React stage's pivot to Markdown-guides-plus-one-shared-project worked well for keeping her in one continuously-growing codebase, so that's the leaning default unless something about Go's tooling (e.g., needing separate `go.mod`s to isolate stages) argues otherwise.
