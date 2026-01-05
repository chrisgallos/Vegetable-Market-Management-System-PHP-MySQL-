# LAXANAGORA — Vegetable Market Management System (PHP + MySQL)

LAXANAGORA is a small **web + database** project that models and manages the core operations of a vegetable market (“λαχαναγορά”).  
It includes a **relational MySQL schema (ER-designed)** with realistic entities (producers, products, receipts/deliveries, customers, stores, etc.) and a simple **PHP dashboard** to perform basic management tasks.

---

## What this project does

### ✅ Database (MySQL)
- Full relational schema with **primary keys, foreign keys, and composite keys**
- Entities included (based on the ER diagram + SQL schema):
  - `ADEIA` (Licenses)
  - `PARAGOGOS` (Producers)
  - `AGROTEMAXIO` (Farm plots)
  - `PROIONTA` (Products)
  - `KATIGORIA_PROIONTOS` (Product categories / VAT)
  - `PARAGOGOS_PROIONTA` (Producer ↔ Product relationship)
  - `PARALABI` (Receipts / deliveries)
  - `KATASTIMATOS` (Stores)
  - `PELATIS` (Customers)
  - `PROION_PELATIS` (Customer orders per product + order status)
- Includes:
  - **Sample data (INSERTs)** to populate the database
  - A **VIEW** (`VW_PARALAVES_APLO`) for joined receipt reporting
  - A **Stored Procedure** (`add_pelatis`) to insert a customer
  - A **Trigger** (`trg_paralabi_before_insert`) that validates receipts (e.g., positive quantity, default date, normalized method)

### ✅ Web App (PHP)
A single-page PHP dashboard (`index.php`) with tabs for:
- **Producers**: add/delete producers  
  - Automatically creates a **license record** (`ADEIA`) if it doesn’t exist  
  - Deletes the related license after deleting the producer (app-level logic)
- **Products**: add/delete products
- **Receipts / Deliveries**: add/delete receipts  
  - Displays joined info (producer name + product name) for readability

---

## Tech Stack
- **PHP** (mysqli)
- **MySQL / MariaDB**
- **HTML/CSS/JS** (simple tab UI)

---

## Repository Files
- `LAXANAGORA_FINAL.sql` → Full schema + inserts + queries + view + procedure + trigger  
- `index.php` → PHP dashboard (CRUD for Producers / Products / Receipts)
- `Teliko_Diagramma_O-S.pdf` → ER Diagram (Entity–Relationship model)

---

## How to run locally (XAMPP/WAMP)

### 1) Create the database
- Open phpMyAdmin (or MySQL Workbench)
- Import `LAXANAGORA_FINAL.sql`

This creates:
- Database: `LAXANAGORA`
- Tables, sample data, view, procedure, trigger

### 2) Add the PHP file
- Put `index.php` inside:
  - XAMPP: `htdocs/`
  - WAMP: `www/`

### 3) Configure DB credentials (if needed)
In `index.php`:
```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "LAXANAGORA";
