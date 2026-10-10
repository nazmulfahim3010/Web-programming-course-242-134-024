# Web Programming Course (242-134-024) — Assignment Submission

## 🎯 Assignment: Sign-Up Form with PHP & MySQL

This repository contains the complete implementation for the **PHP & MySQL Sign-Up Form** assignment based on the Laracasts *PHP for Beginners (2023 Edition)* series by Jeffrey Way.

---

## 📁 Repository Structure

| File | Description |
| :--- | :--- |
| [`signup.php`](signup.php) | **Main Assignment File:** Full-stack Sign-Up form with PDO database connection, validation ("Name is required", "City is required"), prepared statements, `htmlspecialchars()` escaping, "Welcome [Name] from [City]!" greeting, and Bonus user listing + AJAX (fetch) support. |
| [`database.sql`](database.sql) | **Database Schema:** SQL scripts to create the `myapp` database and `users` table (`id`, `name`, `age`, `city`), along with sample seed data. |
| [`ASSIGNMENT_NOTES.md`](ASSIGNMENT_NOTES.md) | **Part 2 Written Submission:** Complete notes for all 11 Laracasts episodes (3 key points with exact video timestamps `[MM:SS]`, 1 exact code snippet, and 1 question per video). |
| [`index.php`](index.php) / [`api.php`](api.php) | Additional To-Do List ("Small Steps") web app. |

---

## 🚀 How to Run the Sign-Up Form in XAMPP

### 1. Start XAMPP
- Open **XAMPP Control Panel**.
- Start **Apache** and **MySQL**.

### 2. Move Folder into `htdocs`
Ensure this project directory is inside `C:\xampp\htdocs\Web-programming-course-242-134-024`.

### 3. Setup Database (`myapp`)
- Open phpMyAdmin at [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
- Go to the **SQL** tab and run the script from [`database.sql`](database.sql):
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
*(Note: `signup.php` also features automatic database and table provisioning on initial load if MySQL is running!)*

### 4. Open in Browser
Navigate to:
```text
http://localhost/Web-programming-course-242-134-024/signup.php
```

---

## ✅ Assignment Requirements Checklist

- [x] **Database & Table:** Created database `myapp` with table `users (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(255), age INT, city VARCHAR(255))`.
- [x] **Validation:** Validates required fields with exact messages: `"Name is required"` and `"City is required"`.
- [x] **Prepared Statements:** Saves user input using PDO prepared statements (`:name`, `:age`, `:city`) preventing SQL injection.
- [x] **XSS Output Escaping:** Uses `htmlspecialchars()` on all user-supplied output in HTML and JSON.
- [x] **Welcome Greeting:** Displays dynamic greeting `"Welcome Sara from Dhaka!"` with submitted values.
- [x] **Bonus 1:** Interactive table listing all registered users from the database.
- [x] **Bonus 2:** Asynchronous form submission via AJAX using JavaScript `fetch` API without page reloads.
- [x] **Written Notes (Part 2):** Documented in [`ASSIGNMENT_NOTES.md`](ASSIGNMENT_NOTES.md) for all 11 episodes with timestamps, exact code, and questions.
