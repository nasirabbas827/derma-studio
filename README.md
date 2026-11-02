# Derma Studio — Clinic Management System

A PHP and MySQL web application for a dermatology clinic ("Derma Elixir Studio" in the site title), with separate admin and doctor areas for managing services, tests, doctors, patients, appointments, medical records, and feedback. Visitors can browse approved doctors, services, and tests, search doctors by name or specialisation, book appointments, and submit feedback.

## What it does

- **Public site (`index.php`, `home.php`):** lists approved doctors, tests, services, and about-us content; doctor search by name or specialisation using prepared statements; appointment booking (`make_appointment.php`) and feedback submission (`post_feedback.php`); patient registration and login (`register.php`, `login.php`).
- **Admin area (`admin/`):** admin login, dashboard, and pages to add/edit about-us content, services, and tests, and to view and manage appointments, doctors, patients, and feedback.
- **Doctor area (`doctor/`):** doctor registration and login, dashboard, appointment and feedback views, profile updates, and adding, editing, and deleting medical records (`view_medical_records.php`, `add_medical_record.php`).
- **Data:** the MySQL schema and seed content are in `Database/derma_db.sql` (database `derma_db`). Tables include about-us content, services, tests, doctors, patients, appointments, medical records, and feedback, as used by the PHP pages.

## Tech stack

- PHP with MySQL (mysqli), plain PHP pages with Bootstrap 4.5 and Font Awesome in the templates
- Session-based login for admin, doctor, and patient accounts
- No framework, package manager file, or automated tests are included in this repository

## Project structure

```text
index.php / home.php        Public site: doctors, services, tests, search, booking
config.php                  Database connection (localhost, derma_db)
admin/                      Admin login, dashboard, and management pages
doctor/                     Doctor dashboard, appointments, and medical records
Database/derma_db.sql       MySQL schema and seed content
css/                        Stylesheets
images/ / uploads/          Site images and uploaded files
login.php / register.php    Patient login and registration
view_appointments.php       Appointment views (public/patient side)
```

A `derma-finalafterpro(thirdupdate).zip` snapshot is also present in the repository; it is not needed to run the application.

## Getting started

1. Install a local PHP and MySQL stack, for example XAMPP with Apache and MySQL.
2. Place this folder in the web server document root, or configure a virtual host pointing to it.
3. Create the database by importing `Database/derma_db.sql` (for example with phpMyAdmin).
4. Check `config.php` — it connects to `localhost` as user `root` with an empty password and database `derma_db`. Update the host, username, password, and database name to match your own setup. The role folders also contain their own small `config.php` files.
5. Open the site in a browser (for example `http://localhost/derma-studio/`) and use the admin and doctor login pages to sign in.

## Notes and limitations

- The default database credentials in `config.php` are local development values. Change them, and do not expose the database or the `uploads/` folder publicly without access controls, before any shared deployment.
- This repository contains no machine-learning code. The seed text in `Database/derma_db.sql` mentions AI-driven skincare in its about-us wording; that is content in the database seed, not a feature of the application code.
- No accuracy, client, or deployment claims are made here; run the application locally and verify each workflow before presenting it as a finished product.

## Author

Nasir Abbas — Software Developer (Python, Django & Flask, Machine Learning & Blockchain)

GitHub: https://github.com/nasirabbas827
