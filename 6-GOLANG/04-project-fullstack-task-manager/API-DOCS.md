# Task Manager API: Endpoint Documentation and Testing Guide

Base URL: `http://localhost:8080/api`

This guide lists every endpoint and shows how to test them step by step with **Thunder Client** (VS Code) or **Postman**. Both tools work the same way. Every example below uses the same fields.

---

## 0. Before You Start

### Step 0.1: Start MySQL

Open XAMPP/Laragon (or your MySQL service) and start MySQL.

### Step 0.2: Check the tables exist

The API needs a database named `task_manager` (the `DB_NAME` in your `.env`) with a `users` table and a `tasks` table.

Columns the code uses:

| Table | Columns |
|-------|---------|
| `users` | `id`, `name`, `email` (unique), `password_hash`, `created_at` |
| `tasks` | `id`, `user_id`, `title`, `description`, `status`, `due_date`, `created_at`, `updated_at` |

### Step 0.3: Start the server

From the `backend/` folder:

```bash
go run ./api
```

Or with auto-reload:

```bash
air
```

**Cek:** the terminal shows both lines below. If it does not, stop here and see the error table.

```
terhubung ke MySQL
server berjalan di http://localhost:8080
```

| Error | Cause | Fix |
|-------|-------|-----|
| `JWT_SECRET wajib diisi` | `JWT_SECRET` is empty in `.env` | Put a long random string after `JWT_SECRET=` |
| `gagal ping database` | MySQL is not running, or wrong `DB_*` values | Start MySQL, check `.env` |
| `Unknown database 'task_manager'` | Database does not exist | Create it in phpMyAdmin |
| `bind: Only one usage of each socket address` | Port 8080 is already in use | Close the other server, or change `APP_PORT` |

---

## 1. Response Format

Every endpoint returns JSON in this shape.

Success:

```json
{
  "success": true,
  "message": "berhasil",
  "data": { }
}
```

Failure (no `data` field):

```json
{
  "success": false,
  "message": "penjelasan error"
}
```

---

## 2. Endpoint Summary

| # | Method | URL | Auth | Purpose |
|---|--------|-----|------|---------|
| 1 | GET | `/api/health` | No | Check the server is alive |
| 2 | POST | `/api/auth/register` | No | Create an account, get a token |
| 3 | POST | `/api/auth/login` | No | Log in, get a token |
| 4 | GET | `/api/auth/me` | **Yes** | Get the logged-in user |
| 5 | GET | `/api/tasks` | **Yes** | List your tasks (filter and pagination) |
| 6 | POST | `/api/tasks` | **Yes** | Create a task |
| 7 | GET | `/api/tasks/:id` | **Yes** | Get one task |
| 8 | PUT | `/api/tasks/:id` | **Yes** | Update a task |
| 9 | DELETE | `/api/tasks/:id` | **Yes** | Delete a task |

**Auth = Yes** means you must send this header:

```
Authorization: Bearer <your token>
```

Each user sees only their own tasks. Asking for another user's task returns `404`, the same as a task that does not exist.

---

## 3. Endpoint Details

### 3.1 GET `/api/health`

No body, no token.

Response `200`:

```json
{
  "success": true,
  "message": "server berjalan",
  "data": { "time": "2026-10-27T09:55:10.123+07:00" }
}
```

---

### 3.2 POST `/api/auth/register`

Headers: `Content-Type: application/json`

Body:

```json
{
  "name": "Vanya",
  "email": "vanya@example.com",
  "password": "rahasia123"
}
```

| Field | Rule |
|-------|------|
| `name` | required, 2 to 100 characters |
| `email` | required, valid email, max 150 characters. Saved in lowercase. |
| `password` | required, 6 to 72 characters |

Response `201`:

```json
{
  "success": true,
  "message": "registrasi berhasil",
  "data": {
    "token": "eyJhbGciOiJIUzI1NiIs...",
    "user": {
      "id": 1,
      "name": "Vanya",
      "email": "vanya@example.com",
      "created_at": "2026-10-27T09:56:00+07:00"
    }
  }
}
```

Errors:

| Status | `message` | When |
|--------|-----------|------|
| 400 | `data tidak valid: ...` | A field is missing or breaks a rule |
| 409 | `email sudah terdaftar` | The email is already used |
| 500 | `gagal menyimpan user` | Database problem (for example, `users` table is missing) |

---

### 3.3 POST `/api/auth/login`

Headers: `Content-Type: application/json`

Body:

```json
{
  "email": "vanya@example.com",
  "password": "rahasia123"
}
```

Response `200`: same shape as register, with `"message": "login berhasil"`.

Errors:

| Status | `message` | When |
|--------|-----------|------|
| 400 | `data tidak valid: ...` | Field missing or email format wrong |
| 401 | `email atau password salah` | Wrong email **or** wrong password. The message is the same on purpose. |

---

### 3.4 GET `/api/auth/me`

Headers: `Authorization: Bearer <token>`

No body.

Response `200`:

```json
{
  "success": true,
  "message": "berhasil",
  "data": {
    "id": 1,
    "name": "Vanya",
    "email": "vanya@example.com",
    "created_at": "2026-10-27T09:56:00+07:00"
  }
}
```

Errors:

| Status | `message` | When |
|--------|-----------|------|
| 401 | `token tidak ditemukan` | No `Authorization` header, or it does not start with `Bearer ` |
| 401 | `token tidak valid atau kedaluwarsa` | Token is wrong, edited, or expired (default 24 hours) |
| 401 | `user tidak ditemukan` | Token is valid but the user was deleted |

The 401 token errors apply to **every** endpoint marked Auth = Yes.

---

### 3.5 GET `/api/tasks`

Headers: `Authorization: Bearer <token>`

Optional query parameters:

| Param | Default | Rule |
|-------|---------|------|
| `status` | all | `todo`, `in_progress`, or `done` |
| `page` | 1 | Number. Values below 1 become 1. |
| `limit` | 10 | 1 to 100. Anything else becomes 10. |

Examples:

```
GET /api/tasks
GET /api/tasks?status=todo
GET /api/tasks?status=done&page=2&limit=5
```

Tasks are sorted newest first.

Response `200`:

```json
{
  "success": true,
  "message": "berhasil",
  "data": {
    "items": [
      {
        "id": 3,
        "user_id": 1,
        "title": "Belajar Gin",
        "description": "Selesaikan bab routing",
        "status": "todo",
        "due_date": "2026-11-01T00:00:00Z",
        "created_at": "2026-10-27T10:00:00+07:00",
        "updated_at": "2026-10-27T10:00:00+07:00"
      }
    ],
    "page": 1,
    "limit": 10,
    "total": 1
  }
}
```

`total` is the number of tasks that match the filter, not only the ones on this page. `due_date` is `null` when no date is set.

Errors:

| Status | `message` | When |
|--------|-----------|------|
| 400 | `status harus todo, in_progress, atau done` | Invalid `status` value |

---

### 3.6 POST `/api/tasks`

Headers: `Authorization: Bearer <token>` and `Content-Type: application/json`

Body:

```json
{
  "title": "Belajar Gin",
  "description": "Selesaikan bab routing",
  "status": "todo",
  "due_date": "2026-11-01"
}
```

| Field | Rule |
|-------|------|
| `title` | required, 1 to 200 characters |
| `description` | optional, max 2000 characters |
| `status` | optional: `todo`, `in_progress`, `done`. Default `todo`. |
| `due_date` | optional, format `YYYY-MM-DD`. Empty or missing means no date. |

Response `201`: `"message": "task berhasil dibuat"`, and `data` is the new task (same fields as in 3.5).

Errors:

| Status | `message` | When |
|--------|-----------|------|
| 400 | `data tidak valid: ...` | Missing `title`, wrong `status`, field too long |
| 400 | `due_date harus berformat YYYY-MM-DD` | Date format is wrong (for example `01-11-2026`) |
| 500 | `gagal membuat task` | Database problem |

---

### 3.7 GET `/api/tasks/:id`

Headers: `Authorization: Bearer <token>`

Example: `GET /api/tasks/3`

Response `200`: `data` is one task.

Errors:

| Status | `message` | When |
|--------|-----------|------|
| 400 | `id tidak valid` | `:id` is not a number, or is below 1 |
| 404 | `task tidak ditemukan` | The task does not exist, or belongs to another user |

---

### 3.8 PUT `/api/tasks/:id`

Headers: `Authorization: Bearer <token>` and `Content-Type: application/json`

Body: same fields as create.

```json
{
  "title": "Belajar Gin (revisi)",
  "description": "Routing dan middleware",
  "status": "in_progress",
  "due_date": "2026-11-05"
}
```

Important behavior:
- `title` is required every time, even if you only want to change the status.
- If you leave out `status`, the old status stays.
- If you leave out `description` or `due_date`, they are **cleared** (empty description, `null` date). This is a full replace, not a partial update.

Response `200`: `"message": "task berhasil diubah"`, `data` is the updated task.

Errors: the same as create, plus `400 id tidak valid` and `404 task tidak ditemukan`.

---

### 3.9 DELETE `/api/tasks/:id`

Headers: `Authorization: Bearer <token>`

Example: `DELETE /api/tasks/3`

Response `200`:

```json
{
  "success": true,
  "message": "task berhasil dihapus"
}
```

Errors: `400 id tidak valid`, `404 task tidak ditemukan`.

---

### 3.10 Unknown URL

Any URL that does not exist returns `404`:

```json
{ "success": false, "message": "endpoint tidak ditemukan" }
```

---

## 4. Step-by-Step Testing in Thunder Client

### Step 4.1: Install and open

1. In VS Code, open Extensions (`Ctrl+Shift+X`) and install **Thunder Client**.
2. Click the Thunder Client icon in the left bar.
3. Click **New Request**.

### Step 4.2: Create an environment (so you do not repeat the URL and token)

1. Open the **Env** tab, then click **New Environment**. Name it `task-manager`.
2. Add two variables:

   | Name | Value |
   |------|-------|
   | `baseUrl` | `http://localhost:8080/api` |
   | `token` | (leave empty for now) |

3. Click the star icon to make it the active environment.
4. In requests, write `{{baseUrl}}` and `{{token}}`.

### Step 4.3: Test 1, Health

1. New Request. Method **GET**. URL: `{{baseUrl}}/health`
2. Click **Send**.

**Cek:** status `200 OK` and `"message": "server berjalan"`.

If you get `Connection refused`, the server is not running. Go back to Step 0.3.

### Step 4.4: Test 2, Register

1. New Request. Method **POST**. URL: `{{baseUrl}}/auth/register`
2. Open the **Body** tab, choose **JSON**, paste:

   ```json
   {
     "name": "Vanya",
     "email": "vanya@example.com",
     "password": "rahasia123"
   }
   ```

3. Click **Send**.

**Cek:** status `201 Created`, and the response has a `token`.

Send it a second time. **Cek:** status `409` with `email sudah terdaftar`. This proves the duplicate check works.

### Step 4.5: Test 3, Login and save the token

1. New Request. Method **POST**. URL: `{{baseUrl}}/auth/login`
2. Body (JSON):

   ```json
   {
     "email": "vanya@example.com",
     "password": "rahasia123"
   }
   ```

3. Click **Send**. **Cek:** status `200` and a `token` in `data`.
4. Copy the token value. Copy only the text inside the quotes.
5. Open Env, paste it into the `token` variable, and save.

Shortcut: open the **Tests** tab of this request, click **Set Env Variable**, set source `json.data.token`, target `token`. After that, every login updates the token by itself.

Try a wrong password. **Cek:** status `401` and `email atau password salah`.

### Step 4.6: Add the token to the protected requests

For every request from Test 4 onward:

1. Open the **Auth** tab.
2. Choose **Bearer**.
3. In the token field, type `{{token}}`.

(Alternative: in the **Headers** tab add `Authorization` = `Bearer {{token}}`.)

### Step 4.7: Test 4, Me

1. GET `{{baseUrl}}/auth/me` with the Bearer token.

**Cek:** status `200` and your name and email.

Remove the token and send again. **Cek:** status `401` and `token tidak ditemukan`.

### Step 4.8: Test 5, Create tasks

1. POST `{{baseUrl}}/tasks` with the Bearer token. Body (JSON):

   ```json
   {
     "title": "Belajar Gin",
     "description": "Selesaikan bab routing",
     "due_date": "2026-11-01"
   }
   ```

2. **Cek:** status `201`, `"status": "todo"` (the default), and an `id`. Note the `id`.
3. Create two more tasks with other titles. Set `"status": "in_progress"` on one of them.

Validation checks:

| Send | Expect |
|------|--------|
| Body `{}` | `400 data tidak valid` |
| `"status": "selesai"` | `400 data tidak valid` |
| `"due_date": "01-11-2026"` | `400 due_date harus berformat YYYY-MM-DD` |

### Step 4.9: Test 6, List tasks

1. GET `{{baseUrl}}/tasks`. **Cek:** `items` has your 3 tasks, newest first, and `total` is 3.
2. GET `{{baseUrl}}/tasks?status=in_progress`. **Cek:** only the in-progress task.
3. GET `{{baseUrl}}/tasks?page=1&limit=2`. **Cek:** 2 items, `total` still 3.
4. GET `{{baseUrl}}/tasks?status=abc`. **Cek:** `400`.

### Step 4.10: Test 7, Get one task

1. GET `{{baseUrl}}/tasks/1` (use a real `id`). **Cek:** `200` and that task.
2. GET `{{baseUrl}}/tasks/9999`. **Cek:** `404 task tidak ditemukan`.
3. GET `{{baseUrl}}/tasks/abc`. **Cek:** `400 id tidak valid`.

### Step 4.11: Test 8, Update a task

1. PUT `{{baseUrl}}/tasks/1`. Body (JSON):

   ```json
   {
     "title": "Belajar Gin (revisi)",
     "status": "done"
   }
   ```

2. **Cek:** status `200`, `status` is `done`, and `description` and `due_date` are now empty (full replace).

### Step 4.12: Test 9, Delete a task

1. DELETE `{{baseUrl}}/tasks/1`. **Cek:** `200 task berhasil dihapus`.
2. Send the same request again. **Cek:** `404`.

### Step 4.13: Test 10, Data isolation (optional, important)

1. Register a second user (`andi@example.com`) and log in. Put this token in `token`.
2. GET `{{baseUrl}}/tasks`. **Cek:** empty `items`.
3. GET `{{baseUrl}}/tasks/2` (a task of the first user). **Cek:** `404`.

Each user cannot see or change another user's tasks.

---

## 5. Postman Differences

The steps are the same. Only the clicks differ.

| Thunder Client | Postman |
|----------------|---------|
| Env tab | **Environments** (left sidebar), then add `baseUrl` and `token` |
| Body, JSON | **Body**, choose **raw**, then set the type on the right to **JSON** |
| Auth, Bearer | **Authorization** tab, Type **Bearer Token** |
| Set Env Variable (Tests tab) | **Scripts**, **Post-response** tab, then paste the script below |

Auto-save the token in Postman (on the login request):

```js
pm.environment.set("token", pm.response.json().data.token);
```

Select the `task-manager` environment in the top-right corner, or `{{baseUrl}}` will not resolve.

---

## 6. Troubleshooting

| Symptom | Cause | Fix |
|---------|-------|-----|
| `Connection refused` / `ECONNREFUSED` | Server not running | Run `go run ./api` and wait for `server berjalan` |
| `401 token tidak ditemukan` | Header missing, or you forgot `Bearer ` (with a space) | Use the Auth tab, Bearer |
| `401 token tidak valid atau kedaluwarsa` | Token pasted wrongly (extra quote or space), expired, or `JWT_SECRET` changed after login | Log in again and save the new token |
| `400 data tidak valid: ... EOF` | Body is empty | Set the body type to JSON and write the body |
| `400 data tidak valid: ... invalid character` | JSON has a syntax error (trailing comma, single quotes) | Fix the JSON |
| `404 endpoint tidak ditemukan` | Wrong URL, for example a missing `/api` | Check `{{baseUrl}}` and the path |
| `404 task tidak ditemukan` on your own task | Logged in as a different user | Check which token is active |
| `500` on register or create | Tables missing or wrong columns | Check the `users` and `tasks` tables (Step 0.2) |

---

## 7. Note for the React Frontend

CORS only allows the origin in `CORS_ORIGIN` (default `http://localhost:5173`). Thunder Client and Postman are not browsers, so CORS does not affect them. React will be affected, so keep that value matching your Vite address.
