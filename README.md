# derma-studio  

A lightweight web‑based management system for dermatology clinics.  
Built with PHP, the application provides separate interfaces for **admins** and **doctors** to manage appointments, patient records, services, tests, and feedback.

---  

## Overview  

derma-studio streamlines daily clinic operations:

* Admins can add/edit/about services, doctors, patients, and view all appointments.  
* Doctors can create and delete medical records, and view their dashboard.  
* Patients and visitors can view available services and submit feedback (via the front‑end, not included in this repo).  

All data is stored in a MySQL database (`Database/derma_db.sql`).  

---  

## Features  

| Area | Capability |
|------|------------|
| **Admin** | Login / logout, add & edit **About**, **Services**, **Tests**, view & manage **Appointments**, **Doctors**, **Patients**, **Feedback** |
| **Doctor** | Dashboard, add & delete **Medical Records** |
| **General** | Centralized configuration (`config.php`), responsive CSS (`css/style.css`), image handling for services |
| **Security** | Simple session‑based authentication, prepared statements (where applicable) |
| **Extensible** | Easy to add new modules (e.g., patient portal) by following existing file structure |

---  

## Tech Stack  

| Component | Technology |
|-----------|------------|
| Backend | PHP 7.4+ |
| Database | MySQL / MariaDB |
| Front‑end | HTML5, CSS3 (custom stylesheet) |
| Server | Apache / Nginx (any LAMP/LEMP stack) |
| Version control | Git (GitHub) |

---  

## Installation  

1. **Clone the repository**  

   ```bash
   git clone https://github.com/yourusername/derma-studio.git
   cd derma-studio
   ```

2. **Create a MySQL database**  

   ```sql
   CREATE DATABASE derma_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import the schema & sample data**  

   ```bash
   mysql -u YOUR_DB_USER -p derma_db < Database/derma_db.sql
   ```

4. **Configure the application**  

   - Open `config.php` (root) and `admin/config.php` / `doctor/config.php`.  
   - Replace placeholder values with your own credentials:  

     ```php
     // Example
     define('DB_HOST', 'localhost');
     define('DB_NAME', 'derma_db');
     define('DB_USER', 'YOUR_DB_USER');
     define('DB_PASS', 'YOUR_DB_PASSWORD');
     ```

5. **Set up the web server**  

   - Point the document root to the repository folder (or a sub‑folder if you prefer).  
   - Ensure PHP is enabled and the `uploads/` directory (used for service images) is writable.  

6. **Optional: Composer dependencies**  

   This project does not require external Composer packages, but you can run `composer install` if you add any later.

---  

## Usage  

### Admin  

1. Navigate to `admin/admin_login.php`.  
2. Log in with the credentials you created in the database (default admin user can be added manually).  
3. From the admin navbar you can:  

   * **Add / Edit About** – `admin/add_about.php`, `admin/edit_about.php`  
   * **Manage Services** – `admin/add_services.php`, `admin/edit_service.php`, `admin/view_services.php` (images stored in `admin/services/`)  
   * **Add Tests** – `admin/add_test.php`, `admin/view_tests.php`  
   * **View Appointments** – `admin/view_appointments.php`  
   * **View Doctors / Patients** – `admin/view