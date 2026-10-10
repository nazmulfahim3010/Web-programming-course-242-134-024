# Web Programming Course (242-134-024) - Assignment Submission

## Overview

This repository contains the full implementation for the **PHP and MySQL Sign-Up Form** assignment based on the Laracasts *PHP for Beginners (2023 Edition)* course series by Jeffrey Way, as well as the **Small Steps** full-stack web application.

---

## User Interface Screenshots

### Task List Interface
![Small Steps Task List](SS/image.png)

### Empty State Interface
![Small Steps Empty State](SS/image%20copy.png)

---

## Repository Structure

| File | Description |
| :--- | :--- |
| [signup.php](signup.php) | **Main Assignment File:** Sign-Up form with PDO database connection, server-side validation ("Name is required", "City is required"), prepared statements, `htmlspecialchars()` output escaping, "Welcome [Name] from [City]!" dynamic greeting, registered user listing, and bonus AJAX fetch submission. |
| [database.sql](database.sql) | **Database Schema:** SQL scripts to create the `myapp` database and `users` table (`id`, `name`, `age`, `city`), along with starter seed records. |
| [ASSIGNMENT_NOTES.md](ASSIGNMENT_NOTES.md) | **Part 2 Written Submission:** Complete video study notes for all 11 Laracasts episodes with video timestamps, exact code snippets, and study questions. |
| [index.php](index.php) / [index.html](index.html) | Small Steps frontend interface markup. |
| [db.php](db.php) | Database connection script for MySQL server. |
| [api.php](api.php) | REST API backend endpoints for task CRUD operations. |
| [script.js](script.js) | Client-side JavaScript event listeners and asynchronous fetch handlers. |
| [styles.css](styles.css) | Application stylesheet and responsive design system. |

---

## Setup and Execution Guide (XAMPP)

### 1. Start XAMPP Services
1. Open the **XAMPP Control Panel**.
2. Start **Apache** (Web Server) and **MySQL** (Database Server).

### 2. Move Project to `htdocs`
Ensure this repository folder is located inside your XAMPP web root directory:
- **Windows:** `C:\xampp\htdocs\Web-programming-course-242-134-024`
- **macOS:** `/Applications/XAMPP/xamppfiles/htdocs/Web-programming-course-242-134-024`
- **Linux:** `/opt/lampp/htdocs/Web-programming-course-242-134-024`

### 3. Database Initialization (`myapp`)
Open phpMyAdmin at [http://localhost/phpmyadmin](http://localhost/phpmyadmin) and run the following SQL script from [database.sql](database.sql):

```sql
CREATE DATABASE IF NOT EXISTS `myapp` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `myapp`;

CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(255) NOT NULL,
    `age` INT DEFAULT NULL,
    `city` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

*(Note: `signup.php` also includes auto-provisioning logic that creates the database and table automatically upon first load.)*

### 4. Running the Applications

- **Sign-Up Form Assignment:**
  ```text
  http://localhost/Web-programming-course-242-134-024/signup.php
  ```

- **Small Steps Web App:**
  ```text
  http://localhost/Web-programming-course-242-134-024/index.php
  ```

---

## Assignment Requirements Checklist

- [x] **Database Table:** Created database `myapp` with table `users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), age INT, city VARCHAR(255))`
- [x] **Validation Messages:** Server-side validation displaying "Name is required" and "City is required"
- [x] **Prepared Statements:** Secure database insertion using PDO prepared statements to protect against SQL Injection
- [x] **Output Escaping:** All user-supplied output safely escaped using `htmlspecialchars()` to protect against Cross-Site Scripting (XSS)
- [x] **Welcome Greeting:** Displays dynamic greeting "Welcome [Name] from [City]!" with submitted user values
- [x] **Bonus 1 - User Listing:** Displays table of all registered users queried from the database
- [x] **Bonus 2 - AJAX Submission:** Form can submit via asynchronous JavaScript `fetch()` without reloading the page
- [x] **Part 2 Written Notes:** Documented in [ASSIGNMENT_NOTES.md](ASSIGNMENT_NOTES.md) for all 11 Laracasts episodes with timestamps, exact code, and questions
