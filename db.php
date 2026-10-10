<?php
/**
 * ============================================================================
 * DATABASE CONNECTION & INITIALIZATION FILE (db.php)
 * ============================================================================
 * 
 * Purpose:
 * 1. Establish a connection to the MySQL database server using PHP's `mysqli` extension.
 * 2. Set default connection parameters commonly used in XAMPP.
 * 3. Automatically create the database and table if they don't exist yet,
 *    making the project beginner-friendly and plug-and-play for students.
 */

// ----------------------------------------------------------------------------
// Step 1: Define Database Configuration Credentials
// ----------------------------------------------------------------------------
// - $host: The server hosting MySQL. 'localhost' means MySQL is on this same computer.
// - $user: Default MySQL user created by XAMPP is 'root'.
// - $pass: In a fresh XAMPP installation, the 'root' password is empty ("").
// - $dbname: The name of the database we want to create and use for our application.
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "small_steps_db";

// ----------------------------------------------------------------------------
// Step 2: Connect to the MySQL Server
// ----------------------------------------------------------------------------
// We create an instance of the `mysqli` class.
// We initially connect WITHOUT specifying a database name so we can create it
// if it doesn't already exist on the server.
$conn = new mysqli($host, $user, $pass);

// ----------------------------------------------------------------------------
// Step 3: Check Connection Status & Handle Errors
// ----------------------------------------------------------------------------
// If the connection failed (for example, if MySQL is not started in XAMPP),
// $conn->connect_error will contain an error message.
if ($conn->connect_error) {
    // Set HTTP status code 500 (Internal Server Error) to inform the browser/client.
    http_response_code(500);
    
    // Return an error message in JSON format and stop executing the script immediately.
    echo json_encode([
        "success" => false, 
        "error" => "Database connection failed: " . $conn->connect_error . ". Please make sure MySQL is running in XAMPP."
    ]);
    exit;
}

// ----------------------------------------------------------------------------
// Step 4: Automatically Create the Database (If Not Exists)
// ----------------------------------------------------------------------------
// In classroom settings, students often forget to create the database in phpMyAdmin.
// This SQL command automatically creates the database if it is missing.
$conn->query("CREATE DATABASE IF NOT EXISTS `$dbname`");

// Select the database to work with for all subsequent queries.
$conn->select_db($dbname);

// ----------------------------------------------------------------------------
// Step 5: Automatically Create the Table (If Not Exists)
// ----------------------------------------------------------------------------
// We define the table schema for storing our to-do list items:
// - `id`: INT AUTO_INCREMENT PRIMARY KEY -> Unique number for each task, increases automatically.
// - `text`: VARCHAR(255) NOT NULL -> Stores the description of the task (up to 255 characters).
// - `completed`: TINYINT(1) DEFAULT 0 -> Boolean flag (0 = false/not done, 1 = true/completed).
// - `created_at`: TIMESTAMP DEFAULT CURRENT_TIMESTAMP -> Automatically saves when the item was added.
$table_sql = "CREATE TABLE IF NOT EXISTS `todos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `text` VARCHAR(255) NOT NULL,
    `completed` TINYINT(1) NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($table_sql);

// Note: We leave $conn open here so any file that includes `db.php` (like `api.php`)
// can use the active `$conn` variable to execute queries.
?>
