# Vehicle Record System

Individual final project - Web Systems Technologies (PHP and MySQL Development)
Stack: PHP 8, MySQL (MySQLi prepared statements), HTML, CSS, Bootstrap 5

## Features
- **Create / Read / Update / Delete** vehicle records
- Search (plate, brand, model, owner), filter by type and status, pagination
- Summary cards: total, active, expiring in 30 days, registration expired
- Registration expiry warnings in the table
- Two related tables: `vehicle_types` (1) -> `vehicles` (many) with a foreign key
- Validation, prepared statements, XSS escaping, CSRF token on forms and delete

## Database
`vehicles` has 14 columns, primary key `vehicle_id`, unique `plate_number`, and a foreign key `type_id` -> `vehicle_types.type_id`.

## Run locally (XAMPP)
1. Copy the `vehicle-record-system` folder to `C:\xampp\htdocs\`.
2. Start **Apache** and **MySQL** in the XAMPP Control Panel.
3. Open http://localhost/phpmyadmin, create a database named `vehicle_record_db`, select it, then **Import** `database.sql`.
4. Open http://localhost/vehicle-record-system/

`db_connect.php` already uses the XAMPP defaults (root, no password).

## Deploy to InfinityFree
1. Create an account at https://www.infinityfree.net and make a new **Hosting Account**.
2. Control Panel -> **MySQL Databases** -> create a database. Copy the **hostname, username, password and database name** (the name has a prefix like `if0_12345678_`).
3. Edit `db_connect.php` with those four values.
4. In **File Manager**, open `htdocs/`, delete the default files, and upload the whole project (or the zip, then extract).
5. Open **phpMyAdmin** from the Control Panel, select your database, and import `database.sql`.
6. Visit your live link (e.g. https://yourfullname.epizy.com) and test add, edit, view and delete.

## Submission checklist
- [ ] Live InfinityFree URL works
- [ ] GitHub repository contains all project files and `database.sql`
- [ ] Email the live link and repository link to the instructor
- [ ] Prepare a 5 to 7 minute presentation
- Deadline: November 23-27, 2026

## Files
| File | Purpose |
|---|---|
| `index.php` | List, search, filter, pagination (SELECT) |
| `create.php` | Add record (INSERT) |
| `edit.php` | Edit record (UPDATE) |
| `delete.php` | Remove record (DELETE) |
| `view.php` | Single record details |
| `db_connect.php` | Database credentials |
| `includes/` | Header, footer, shared form, helper functions |
| `assets/css/style.css` | Custom styles |
| `database.sql` | Tables and sample data |
