# mvc-stokbarang

Stock/inventory management system. Admin login + CRUD for stock items/production.

## Tech Stack
- PHP 8.x (vanilla, no framework)
- MySQL 8.x
- Tailwind CSS (CDN)
- MVC pattern

## Project Structure

```
mvc-stokbarang/
├── app/
│   ├── controllers/
│   │   ├── AuthController.php       # login, logout
│   │   └── StockController.php      # CRUD stok barang
│   ├── models/
│   │   ├── User.php                 # admin user model
│   │   └── Stock.php                # stok barang model
│   └── views/
│       ├── auth/
│       │   └── login.php
│       ├── stock/
│       │   ├── index.php            # list all stock
│       │   ├── create.php           # form tambah
│       │   ├── edit.php             # form edit
│       │   └── show.php             # detail item
│       └── layouts/
│           ├── main.php             # sidebar + nav wrapper
│           └── header.php           # head html
├── config/
│   └── database.php                 # DB connection (PDO)
├── core/
│   ├── Router.php                   # URL routing
│   ├── Controller.php               # base controller
│   └── Model.php                    # base model (PDO wrapper)
├── public/
│   └── index.php                    # front controller / entry point
├── database/
│   └── schema.sql                   # DB schema + seed admin
└── .htaccess                        # rewrite all to public/index.php
```

## Routing Convention
`GET /` → StockController@index (redirect to login if not auth)
`GET /login` → AuthController@login
`POST /login` → AuthController@authenticate
`GET /logout` → AuthController@logout
`GET /stock` → StockController@index
`GET /stock/create` → StockController@create
`POST /stock/store` → StockController@store
`GET /stock/edit?id=` → StockController@edit
`POST /stock/update` → StockController@update
`POST /stock/delete` → StockController@delete

## Database Tables
- `users` — id, name, email, password (bcrypt), role, created_at
- `stocks` — id, kode_barang, nama_barang, kategori, satuan, stok, harga, keterangan, created_at, updated_at

## Auth
- Session-based. `$_SESSION['user_id']` set on login.
- All non-login routes check session via base Controller.
- Password: `password_hash()` / `password_verify()`.

## Conventions
- Controllers: class names PascalCase, files match class name.
- Models: PDO, prepared statements only — no raw interpolated SQL.
- Views: plain PHP templates, no logic beyond loops/conditionals.
- No jQuery. Vanilla JS only when needed.
- Tailwind via CDN in layouts/header.php.
