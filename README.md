# Student Book Exchange

This repository contains a small web application built as a college project by Danil Hordiienko.

The idea is to simulate a simple student platform where users can:

- register and log in
- browse a list of textbooks stored in the database
- view basic campus merchandise
- add items to favourites
- place small demo orders for merchandise
- leave reviews for books and merch
- view a simple admin dashboard (users, books, merch)

## Technologies

- PHP 8 (procedural style with small functions)
- MySQL (via PDO)
- Bootstrap 5 for layout and components
- Session-based authentication and cart

## Database

The database schema and sample data are stored in:

`sql/student_book_exchange_schema.sql`

To set it up on a local WAMP stack:

1. Start WAMP and open phpMyAdmin.
2. Use the "Import" tab to import the SQL file.
3. The script creates the `student_book_exchange` database and tables and inserts sample records.

The connection settings for local development can be adjusted in `db.php` if needed.

## Running the project locally

1. Clone or copy the project into `c:/wamp64/www/book_exchange`.
2. Ensure the database is created and imported as described above.
3. Start Apache and MySQL in WAMP.
4. Open `http://localhost/book_exchange/` in a browser.

To create an admin user, you can register a normal account and then change its `role` column to `admin` in the `users` table via phpMyAdmin.