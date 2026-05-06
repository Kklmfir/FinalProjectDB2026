# MDG — My Dompet Gue (FinalProjectDB2026)

A PHP Native financial management dashboard built for a Database System final project. The app provides a modern fintech-style UI and CRUD modules for managing personal finance data (pockets, categories, transactions, budgets, goals, contacts, debt/loan, transfers).

> Recommended run mode for now: **MySQL Local (XAMPP/phpMyAdmin)**. Supabase fields exist in `.env`, but you can leave them empty until local is stable.

---

## ✅ Key Features

### Functional (CRUD)
- **Pocket**: manage wallets/pockets and balances
- **Category & Sub Category**: classify income/expense
- **Transactions**: record income/expense activity
- **Transfer**: pocket-to-pocket transfers
- **Budget**: monthly limits per category
- **Goal**: savings goals and progress
- **Contact**: people/institutions for debt/loan
- **Debt / Loan**: track debts & receivables

### UI/UX
- Fintech-inspired dashboard layout (Bootstrap 5 + custom CSS)
- Responsive pages (mobile-friendly)
- DataTables for searchable/sortable/paginated tables
- Chart.js for analytics visualization

### Configuration & Security Basics
- `.env` loader (`config/env_loader.php`)
- App bootstrap (`config/bootstrap.php`) to start session early
- CSRF token helpers + output escaping (`helpers/security.php`)

---

## 🧱 Tech Stack
- **PHP**: 7.4+ (Native)
- **Database**: MySQL (local / XAMPP)
- **UI**: Bootstrap 5, Font Awesome
- **Tables**: DataTables
- **Charts**: Chart.js

---

## 📁 Project Structure (Current)

```
FinalProjectDB2026/
│
├── menu.php
├── .env                 # local configuration (not committed)
├── .env.example         # env template
├── README.md
├── LICENSE
├── final-project-db2026.sql
├── insert-data.sql
├── reset_auto_increment.php
│
├── config/
│   ├── bootstrap.php     # session + env + timezone
│   ├── env_loader.php    # loads .env
│   ├── database.php      # (for future / dual-db architecture)
│   ├── db_local.php
│   └── db_supabase.php
│
├── components/
│   ├── header.php
│   ├── sidebar.php
│   ├── navbar.php
│   ├── footer.php
│   └── alerts.php
│
├── assets/
│   ├── css/style.css
│   ├── js/app.js
│   └── icons/
│
├── helpers/
│   ├── functions.php
│   └── security.php
│
├── api/
│   └── switch_db.php
│
└── src/
    ├── pocket/
    ├── category/
    ├── sub_category/
    ├── transaction/
    ├── transfer/
    ├── budget/
    ├── goal/
    ├── contact/
    └── debt_loan/
```

---

## 🗄️ Database (MySQL Local)

### Tables
- `Pocket`
- `Category`
- `Sub_Category`
- `Transactions`
- `Transfer`
- `Budget`
- `Goal`
- `Contact`
- `Debt_Loan`
- `counters` — application-managed sequential ID table (see below)

> Notes:
> - Table names in SQL use **Capitalization** (e.g., `Pocket`, `Category`).
> - Some environments are case-sensitive; keep naming consistent.

---

## 🔢 Sequential ID System (Counter Table)

All CREATE forms use an application-managed gapless sequential ID system instead of `AUTO_INCREMENT`.
This is implemented via the **Counter Table + `SELECT … FOR UPDATE`** pattern, which is portable
between MySQL InnoDB (local) and PostgreSQL/Supabase.

### How it works

1. A `counters` table stores one row per entity (`name`, `current_value`).
2. On each INSERT, `helpers/counter_helper.php::acquireSequentialIdAndInsert()`:
   - Starts a transaction.
   - Locks the counter row with `SELECT … FOR UPDATE` (row-level lock).
   - Increments `current_value` and obtains the new ID.
   - Calls the caller-supplied callback to perform the actual `INSERT` with the new ID.
   - COMMITs. If anything fails, the transaction is rolled back — **no gaps**.
   - Retries up to 3 times (with 100 ms back-off) on deadlock/serialisation errors.

### Syncing counters after backup/restore

After restoring an entity table from a backup, the counter **must** be re-synced so it is
`>= MAX(id)` in the restored table. Otherwise the next application insert will collide.

**MySQL / InnoDB:**
```sql
INSERT INTO counters (name, current_value)
  VALUES ('transactions', (SELECT COALESCE(MAX(Transaction_ID), 0) FROM Transactions))
  ON DUPLICATE KEY UPDATE current_value = VALUES(current_value);
```

**PostgreSQL / Supabase:**
```sql
INSERT INTO counters (name, current_value)
  VALUES ('transactions', (SELECT COALESCE(MAX("Transaction_ID"), 0) FROM "Transactions"))
  ON CONFLICT (name) DO UPDATE SET current_value = EXCLUDED.current_value;
```

Repeat for every entity: `pocket`, `category`, `sub_category`, `contact`, `goal`, `budget`, `debt_loan`, `transfer`.

### Initial migration (fresh deployment)

Run `sql-with-dummy.sql` in full — it creates the `counters` table and seeds every counter
with the `MAX(id)` value matching the bundled dummy data.

---

## 🚀 Local Setup (XAMPP)

### 1) Put project in XAMPP
Copy project folder into:
- `C:\xampp\htdocs\FinalProjectDB2026`

### 2) Create & import database
In phpMyAdmin:
1. Create database: `final-project-db2026`
2. Import schema: `final-project-db2026.sql`
3. (Optional) Import sample inserts: `insert-data.sql`

### 3) Configure environment
Copy template:
```bash
cp .env.example .env
```
Edit `.env`:
```env
APP_ENV=local
APP_DEBUG=true
APP_NAME="MDG - My Dompet Gue"

DB_MODE=local

MYSQL_HOST=localhost
MYSQL_PORT=3306
MYSQL_DATABASE=final-project-db2026
MYSQL_USERNAME=root
MYSQL_PASSWORD=
```

### 4) Run
Open in browser:
- `http://localhost/FinalProjectDB2026/menu.php`

---

## 🧩 CRUD Flow (High Level)

Each entity folder under `src/<entity>/` follows a consistent pattern:
- `<entity>.php` → loads `.env`, creates `$conn` (mysqli), defines `$table` and `$primary_key`
- `index.php` → **Read/List** (SELECT + table UI)
- `add.php` → **Create** (POST → INSERT → redirect)
- `edit.php` → **Update** (GET id → SELECT → POST → UPDATE → redirect)
- `delete.php` → **Delete** (GET id → DELETE → redirect)

---

## ⚠️ Common Troubleshooting

### 1) "Database belum terhubung"
- Ensure MySQL is running (XAMPP)
- Ensure `.env` exists and values match your phpMyAdmin database name

### 2) HTTP 500 on `add.php`
- Usually caused by PHP fatal error or session/headers issues.
- Ensure every page includes `config/bootstrap.php` **only once** and **before any HTML output**.

---

## 📄 License
MIT License — see `LICENSE`.
