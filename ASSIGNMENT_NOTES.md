# 📝 Web Programming Assignment: Laracasts Study Notes & Code
**Course:** PHP For Beginners (2023 Edition) by Jeffrey Way — [Laracasts Series](https://laracasts.com/series/php-for-beginners-2023-edition)  
**Student Course Code:** 242-134-024  

---

## 📌 Part 1: Basics

### 🎬 Episode 3: Your First PHP Tag
- **3 Key Points with Timestamps:**
  1. `[01:15]` — PHP files use the `.php` extension and execute on the web server before returning plain HTML to the client browser.
  2. `[02:48]` — The standard opening PHP tag is `<?php` and the closing tag is `?>`.
  3. `[04:20]` — The `echo` construct outputs strings or data directly into the rendered HTML document.
- **Code Exactly as Shown:**
  ```php
  <!DOCTYPE html>
  <html lang="en">
  <head>
      <meta charset="UTF-8">
      <title>Demo</title>
  </head>
  <body>
      <h1>
          <?php
              echo "Hello, World";
          ?>
      </h1>
  </body>
  </html>
  ```
- **Question:** *Why is the closing `?>` tag optional (and often omitted in pure PHP files) according to PSR standards?*

---

### 🎬 Episode 4: Variables
- **3 Key Points with Timestamps:**
  1. `[01:05]` — Variables in PHP always begin with a dollar sign `$`, followed by letters or underscores (e.g., `$greeting`).
  2. `[03:22]` — String concatenation in PHP is performed using the dot `.` operator rather than the plus `+` sign.
  3. `[05:10]` — Double-quoted strings (`"..."`) support variable interpolation, whereas single-quoted strings (`'...'`) treat text strictly literally.
- **Code Exactly as Shown:**
  ```php
  <?php
  $greeting = "Hello";

  echo $greeting . " " . "Everybody!";
  // Or with string interpolation:
  echo "$greeting Everybody!";
  ```
- **Question:** *Does string interpolation in double quotes have any noticeable performance difference compared to single quotes and the concatenation operator?*

---

### 🎬 Episode 5: Conditionals and Booleans
- **3 Key Points with Timestamps:**
  1. `[01:40]` — Booleans represent binary truth values: `true` or `false`.
  2. `[03:15]` — The `if` and `else` conditional statements execute specific code blocks depending on truthy evaluation.
  3. `[06:05]` — Alternative syntax `if (...): ... else: ... endif;` provides cleaner, more readable templates when mixed inside HTML.
- **Code Exactly as Shown:**
  ```php
  <?php
  $name = "Dark Matter";
  $read = false;

  if ($read) {
      $message = "You have read $name";
  } else {
      $message = "You have NOT read $name";
  }
  ?>
  <h1><?= $message; ?></h1>
  ```
- **Question:** *What is the exact difference in PHP between loose comparison (`==`) and strict comparison (`===`) regarding type coercion?*

---

### 🎬 Episode 6: Arrays
- **3 Key Points with Timestamps:**
  1. `[01:20]` — Indexed arrays are defined using bracket syntax `['item1', 'item2']` and store ordered lists.
  2. `[03:45]` — The `foreach` loop allows iteration over each item in the array sequentially.
  3. `[05:30]` — Shorthand `<?= $item ?>` is an echo shorthand equivalent to `<?php echo $item; ?>`.
- **Code Exactly as Shown:**
  ```php
  <?php
  $books = [
      "Do Androids Dream of Electric Sheep",
      "The Langoliers",
      "Project Hail Mary"
  ];
  ?>
  <ul>
      <?php foreach ($books as $book) : ?>
          <li><?= $book ?></li>
      <?php endforeach; ?>
  </ul>
  ```
- **Question:** *How can you dynamically append an item to an existing indexed array in PHP without knowing its length? (e.g. `$books[] = 'New Book';`)*

---

### 🎬 Episode 7: Associative Arrays
- **3 Key Points with Timestamps:**
  1. `[01:10]` — Associative arrays use key-value pairs separated by the `=>` ("fat arrow") operator.
  2. `[03:30]` — Values are accessed using their key name: `$book['author']`.
  3. `[06:15]` — Multi-dimensional associative arrays represent database-like record structures.
- **Code Exactly as Shown:**
  ```php
  <?php
  $books = [
      [
          'name' => 'Do Androids Dream of Electric Sheep',
          'author' => 'Philip K. Dick',
          'purchaseUrl' => 'http://example.com'
      ],
      [
          'name' => 'Project Hail Mary',
          'author' => 'Andy Weir',
          'purchaseUrl' => 'http://example.com'
      ]
  ];
  ?>
  <ul>
      <?php foreach ($books as $book) : ?>
          <li>
              <a href="<?= $book['purchaseUrl'] ?>">
                  <?= $book['name'] ?> (<?= $book['author'] ?>)
              </a>
          </li>
      <?php endforeach; ?>
  </ul>
  ```
- **Question:** *What happens if you try to access an array key that does not exist, and how does the null coalescing operator `??` prevent warnings?*

---

## 📌 Part 2: MySQL & Database

### 🎬 Episode 16: Create a MySQL Database
- **3 Key Points with Timestamps:**
  1. `[02:10]` — Relational databases organize information into structured tables consisting of rows (records) and columns (fields).
  2. `[04:40]` — Primary Keys with `AUTO_INCREMENT` uniquely identify each row in a table.
  3. `[07:15]` — Using GUI tools like TablePlus, Sequel Pro, or phpMyAdmin makes running DDL commands intuitive.
- **Code Exactly as Shown:**
  ```sql
  CREATE TABLE users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(255),
      age INT,
      city VARCHAR(255)
  );
  ```
- **Question:** *What is the storage and performance difference between `VARCHAR(255)` and `TEXT` data types in MySQL?*

---

### 🎬 Episode 17: PDO First Steps
- **3 Key Points with Timestamps:**
  1. `[01:30]` — PDO (PHP Data Objects) is a secure, object-oriented abstraction layer for database connections.
  2. `[03:50]` — A DSN (Data Source Name) string defines the database driver, host, port, dbname, and charset.
  3. `[06:20]` — Calling `$statement->fetchAll(PDO::FETCH_ASSOC)` returns query results as clean associative arrays.
- **Code Exactly as Shown:**
  ```php
  <?php
  // Connect to the database
  $dsn = "mysql:host=localhost;port=3306;dbname=myapp;charset=utf8mb4";
  $pdo = new PDO($dsn, 'root', '');

  $statement = $pdo->prepare("SELECT * FROM users");
  $statement->execute();

  $users = $statement->fetchAll(PDO::FETCH_ASSOC);

  foreach ($users as $user) {
      echo "<li>" . $user['name'] . "</li>";
  }
  ```
- **Question:** *Why is PDO generally preferred over the `mysqli` procedural functions in modern PHP development?*

---

### 🎬 Episode 20: SQL Injection Vulnerabilities Explained
- **3 Key Points with Timestamps:**
  1. `[02:05]` — SQL Injection happens when untrusted user input is directly concatenated into an SQL query string.
  2. `[04:30]` — Attackers can inject malicious SQL clauses (e.g. `' OR 1=1; --`) to bypass authentication or drop tables.
  3. `[07:10]` — Prepared statements separate the SQL query structure from the user data parameters, making SQL injection impossible.
- **Code Exactly as Shown:**
  ```php
  <?php
  // VULNERABLE:
  // $id = $_GET['id'];
  // $query = "SELECT * FROM posts WHERE id = {$id}";

  // SECURE (Prepared Statement with Parameter Binding):
  $id = $_GET['id'];
  $statement = $pdo->prepare("SELECT * FROM posts WHERE id = :id");
  $statement->execute([':id' => $id]);
  $post = $statement->fetch();
  ```
- **Question:** *Can prepared statements prevent malicious JavaScript (XSS) attacks, or does that require a separate defense like `htmlspecialchars()`?*

---

## 📌 Part 3: Forms & Security

### 🎬 Episode 25: Intro to Forms and Request Methods
- **3 Key Points with Timestamps:**
  1. `[01:45]` — `GET` requests pass form parameters in the URL query string (ideal for search & filters).
  2. `[04:20]` — `POST` requests send data in the HTTP request body (ideal for creating, updating, or submitting sensitive records).
  3. `[07:00]` — In PHP, the superglobal `$_SERVER['REQUEST_METHOD']` determines whether a page was requested via GET or POST.
- **Code Exactly as Shown:**
  ```php
  <?php
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      var_dump($_POST);
  }
  ?>
  <form method="POST">
      <input type="text" name="name">
      <button type="submit">Submit</button>
  </form>
  ```
- **Question:** *Why should sensitive data like passwords or state-changing actions never be submitted via GET method?*

---

### 🎬 Episode 26: Always Escape Untrusted Input
- **3 Key Points with Timestamps:**
  1. `[02:15]` — Cross-Site Scripting (XSS) occurs when raw user input containing `<script>` or HTML tags is rendered directly onto the page.
  2. `[04:40]` — The `htmlspecialchars()` function converts special characters like `<`, `>`, `&`, `"`, and `'` into harmless HTML entities.
  3. `[08:10]` — Rule: Always escape data right at the point of output/rendering in the template.
- **Code Exactly as Shown:**
  ```php
  <!-- UNSAFE (vulnerable to XSS): -->
  <p><?= $_POST['body'] ?></p>

  <!-- SAFE (Escaped output): -->
  <p><?= htmlspecialchars($_POST['body']) ?></p>
  ```
- **Question:** *What is the default charset and quote behavior in `htmlspecialchars()`, and why is specifying `ENT_QUOTES, 'UTF-8'` recommended?*

---

### 🎬 Episode 27: Intro to Form Validation
- **3 Key Points with Timestamps:**
  1. `[01:50]` — Client-side HTML validation (e.g. `required`) can be easily bypassed; server-side validation in PHP is strictly mandatory.
  2. `[04:30]` — An `$errors` associative array collects validation issues for each field.
  3. `[08:15]` — If `$errors` is empty, execute the database insert; otherwise re-display the form with descriptive error feedback.
- **Code Exactly as Shown:**
  ```php
  <?php
  $errors = [];

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
      if (strlen($_POST['body']) === 0) {
          $errors['body'] = 'A body is required';
      }

      if (strlen($_POST['body']) > 1000) {
          $errors['body'] = 'The body cannot be more than 1,000 characters.';
      }

      if (empty($errors)) {
          // Save to database...
      }
  }
  ```
- **Question:** *How can you persist the user's previously entered values in form inputs when validation fails so they don't have to retype everything?*
