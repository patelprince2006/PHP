# Nutra_leaf — Static conversion

This folder contains a converted static frontend and simple PHP/MySQL backend adapted from the original React `App.tsx`.

Files added:
- `index.html` — main static page
- `styles.css` — basic styling (approximate Tailwind look)
- `app.js` — client logic: product listing, cart, checkout, admin
- `api/` — PHP endpoints: `save_order.php`, `list_orders.php`, `db_config.php`

MySQL schema (run in your MySQL):

```
CREATE DATABASE nutra_leaf DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nutra_leaf;
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id VARCHAR(64) NOT NULL,
  date VARCHAR(64),
  name VARCHAR(255),
  phone VARCHAR(32),
  city VARCHAR(128),
  address TEXT,
  items_json LONGTEXT,
  total DECIMAL(10,2),
  status VARCHAR(64)
);

Users table
-----------

Create a `users` table for customer accounts:

```sql
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL UNIQUE,
  phone VARCHAR(32),
  password_hash VARCHAR(255) NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
```

Auth endpoints (PHP)
- `api/register.php` — POST JSON `{name,email,phone,password}` to create an account.
- `api/login.php` — POST JSON `{email,password}` to authenticate; uses PHP session on success.
- `api/admin_login.php` — POST JSON `{password}` to authenticate as admin. Admin password is configured in `api/db_config.php` under `admin_password` (change this value before use).

Security notes
- Passwords are stored using `password_hash()` and verified with `password_verify()`.
- Admin authentication uses a configured password; for production, replace with a hashed secret and serve over HTTPS.

```

To run locally with PHP built-in server:

```bash
php -S localhost:8000 -t .
```

Then open `http://localhost:8000`.

AI endpoint
-----------

A lightweight AI fallback endpoint is available at `api/ai_advice.php`. POST JSON `{ "query": "your question" }` and it returns a JSON object `{ "advice": "..." }` with a rule-based recommendation when the Gemini service is not configured.

Example:

```bash
curl -s -X POST http://localhost:8000/api/ai_advice.php -H "Content-Type: application/json" -d '{"query":"Which is better for sleep?"}'
```

