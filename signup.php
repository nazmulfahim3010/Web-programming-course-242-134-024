<?php
/**
 * ============================================================================
 * SIGN UP APPLICATION (signup.php)
 * ============================================================================
 * Course: Web Programming (242-134-024)
 * Assignment: Sign up form that saves to a MySQL database
 * Based on: Laracasts — PHP for Beginners (2023 Edition)
 *
 * Requirements Met:
 *  - TODO 1: Connect to MySQL database ('myapp') using PDO
 *  - TODO 2: Form validation with exact required error messages:
 *            "Name is required" and "City is required"
 *  - TODO 3: Save user using Prepared Statements (SQL Injection Protection)
 *  - TODO 4: Escape output with htmlspecialchars() & display "Welcome [Name] from [City]!"
 *  - BONUS 1: List all users from database
 *  - BONUS 2: AJAX submission using JavaScript `fetch` API
 * ============================================================================
 */

// ----------------------------------------------------------------------------
// TODO 1: DATABASE CONNECTION (PDO)
// ----------------------------------------------------------------------------
$host = 'localhost';
$dbname = 'myapp';
$username = 'root';
$password = '';

try {
    // Initial connection to server to auto-provision database & table if missing
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);

    // Ensure database `myapp` exists
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbname` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `$dbname`");

    // Ensure table `users` exists with the exact required schema
    $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `name` VARCHAR(255) NOT NULL,
        `age` INT DEFAULT NULL,
        `city` VARCHAR(255) NOT NULL,
        `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

} catch (PDOException $e) {
    if (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $e->getMessage()]);
        exit;
    }
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

// State variables
$errors = [];
$welcomeMessage = '';
$isAjax = (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') 
          || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
          || (isset($_GET['ajax']) && $_GET['ajax'] == '1');

// ----------------------------------------------------------------------------
// TODO 2 & 3: FORM HANDLING, VALIDATION & PREPARED STATEMENTS
// ----------------------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Support both standard POST and JSON POST (AJAX)
    $inputData = $_POST;
    if (empty($inputData)) {
        $rawJson = file_get_contents('php://input');
        $decoded = json_decode($rawJson, true);
        if (is_array($decoded)) {
            $inputData = $decoded;
        }
    }

    $name = trim($inputData['name'] ?? '');
    $age  = trim($inputData['age'] ?? '');
    $city = trim($inputData['city'] ?? '');

    // TODO 2: Form Validation
    if (empty($name)) {
        $errors['name'] = 'Name is required';
    }
    if (empty($city)) {
        $errors['city'] = 'City is required';
    }

    // If there are no validation errors, proceed with insertion
    if (empty($errors)) {
        // TODO 3: Save user using Prepared Statement
        $stmt = $pdo->prepare("INSERT INTO users (name, age, city) VALUES (:name, :age, :city)");
        $stmt->execute([
            ':name' => $name,
            ':age'  => ($age !== '' && is_numeric($age)) ? (int)$age : null,
            ':city' => $city
        ]);

        $newId = (int)$pdo->lastInsertId();

        // TODO 4: Construct the required Welcome message with escaping
        $welcomeMessage = "Welcome " . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . " from " . htmlspecialchars($city, ENT_QUOTES, 'UTF-8') . "!";

        // If request is via AJAX, return clean JSON response
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => $welcomeMessage,
                'user' => [
                    'id' => $newId,
                    'name' => htmlspecialchars($name, ENT_QUOTES, 'UTF-8'),
                    'age' => ($age !== '' && is_numeric($age)) ? (int)$age : null,
                    'city' => htmlspecialchars($city, ENT_QUOTES, 'UTF-8')
                ]
            ]);
            exit;
        }
    } else {
        if ($isAjax) {
            header('Content-Type: application/json');
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'errors' => $errors
            ]);
            exit;
        }
    }
}

// ----------------------------------------------------------------------------
// BONUS 1: FETCH ALL REGISTERED USERS
// ----------------------------------------------------------------------------
$stmtUsers = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$allUsers = $stmtUsers->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up | Web Programming Assignment</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-body: #0f172a;
            --bg-card: #1e293b;
            --bg-input: #0f172a;
            --border-color: #334155;
            --primary: #38bdf8;
            --primary-hover: #0ea5e9;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --error: #f87171;
            --error-bg: rgba(239, 68, 68, 0.15);
            --success: #4ade80;
            --success-bg: rgba(34, 197, 94, 0.15);
            --radius: 12px;
            --shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.3), 0 8px 10px -6px rgba(0, 0, 0, 0.3);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: var(--bg-body);
            color: var(--text-main);
            min-height: 100vh;
            padding: 40px 20px;
            display: flex;
            justify-content: center;
            align-items: flex-start;
        }

        .container {
            width: 100%;
            max-width: 720px;
            display: flex;
            flex-direction: column;
            gap: 28px;
        }

        .card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius);
            padding: 32px;
            box-shadow: var(--shadow);
        }

        .header-title {
            font-size: 1.75rem;
            font-weight: 700;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header-title span.badge {
            font-size: 0.75rem;
            background: #0284c7;
            color: #fff;
            padding: 4px 10px;
            border-radius: 9999px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .subtitle {
            color: var(--text-muted);
            font-size: 0.95rem;
            margin-bottom: 24px;
        }

        /* Banner Messages */
        .alert {
            padding: 16px 20px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: fadeIn 0.3s ease;
        }

        .alert-success {
            background: var(--success-bg);
            border: 1px solid rgba(74, 222, 128, 0.4);
            color: var(--success);
        }

        .alert-error {
            background: var(--error-bg);
            border: 1px solid rgba(248, 113, 113, 0.4);
            color: var(--error);
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 20px;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        label {
            font-size: 0.9rem;
            font-weight: 600;
            color: var(--text-main);
        }

        label .required {
            color: var(--error);
        }

        input[type="text"],
        input[type="number"] {
            width: 100%;
            padding: 12px 16px;
            background-color: var(--bg-input);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-main);
            font-size: 1rem;
            font-family: inherit;
            transition: all 0.2s ease;
        }

        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(56, 189, 248, 0.2);
        }

        .input-error {
            border-color: var(--error) !important;
        }

        .error-text {
            color: var(--error);
            font-size: 0.85rem;
            font-weight: 500;
        }

        .btn-submit {
            background: var(--primary);
            color: #0f172a;
            border: none;
            padding: 14px 24px;
            font-size: 1rem;
            font-weight: 700;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            transition: background 0.2s, transform 0.1s;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
        }

        .btn-submit:active {
            transform: scale(0.99);
        }

        .ajax-toggle {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-top: 14px;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        /* Table Styles (Bonus) */
        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
        }

        .table-header h3 {
            font-size: 1.25rem;
            font-weight: 600;
        }

        .count-badge {
            background: #334155;
            color: #cbd5e1;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .table-responsive {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 0.95rem;
        }

        th {
            background-color: rgba(15, 23, 42, 0.6);
            color: var(--text-muted);
            font-weight: 600;
            padding: 12px 16px;
            border-bottom: 2px solid var(--border-color);
        }

        td {
            padding: 14px 16px;
            border-bottom: 1px solid var(--border-color);
        }

        tr:last-child td {
            border-bottom: none;
        }

        tr:hover td {
            background-color: rgba(255, 255, 255, 0.02);
        }

        .empty-row {
            text-align: center;
            color: var(--text-muted);
            padding: 24px;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-6px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="container">

    <!-- SIGN UP FORM CARD -->
    <div class="card">
        <h1 class="header-title">
            Sign Up
            <span class="badge">PHP + MySQL</span>
        </h1>
        <p class="subtitle">Complete the form below to register your account into the MySQL database.</p>

        <!-- Dynamic Success Message Alert (TODO 4: htmlspecialchars output) -->
        <div id="alert-container">
            <?php if (!empty($welcomeMessage)): ?>
                <div class="alert alert-success" id="server-welcome">
                    <span>🎉</span>
                    <!-- Output escaped safely with htmlspecialchars -->
                    <strong><?= $welcomeMessage ?></strong>
                </div>
            <?php endif; ?>
        </div>

        <form id="signup-form" method="POST" action="signup.php" novalidate>
            <!-- NAME FIELD -->
            <div class="form-group">
                <label for="name">Name <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    placeholder="e.g. Sara"
                    value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8') : '' ?>"
                    class="<?= isset($errors['name']) ? 'input-error' : '' ?>"
                >
                <div class="error-text" id="error-name">
                    <?= isset($errors['name']) ? htmlspecialchars($errors['name'], ENT_QUOTES, 'UTF-8') : '' ?>
                </div>
            </div>

            <!-- AGE FIELD -->
            <div class="form-group">
                <label for="age">Age</label>
                <input 
                    type="number" 
                    id="age" 
                    name="age" 
                    placeholder="e.g. 22"
                    min="1" 
                    max="120"
                    value="<?= isset($_POST['age']) ? htmlspecialchars($_POST['age'], ENT_QUOTES, 'UTF-8') : '' ?>"
                >
                <div class="error-text" id="error-age"></div>
            </div>

            <!-- CITY FIELD -->
            <div class="form-group">
                <label for="city">City <span class="required">*</span></label>
                <input 
                    type="text" 
                    id="city" 
                    name="city" 
                    placeholder="e.g. Dhaka"
                    value="<?= isset($_POST['city']) ? htmlspecialchars($_POST['city'], ENT_QUOTES, 'UTF-8') : '' ?>"
                    class="<?= isset($errors['city']) ? 'input-error' : '' ?>"
                >
                <div class="error-text" id="error-city">
                    <?= isset($errors['city']) ? htmlspecialchars($errors['city'], ENT_QUOTES, 'UTF-8') : '' ?>
                </div>
            </div>

            <button type="submit" class="btn-submit" id="submit-btn">Submit Sign Up</button>

            <!-- Bonus Option: AJAX mode toggle indicator -->
            <label class="ajax-toggle">
                <input type="checkbox" id="ajax-enabled" checked> Enable Bonus AJAX (fetch) submission
            </label>
        </form>
    </div>

    <!-- BONUS 1: REGISTERED USERS TABLE -->
    <div class="card">
        <div class="table-header">
            <h3>Registered Users (Bonus)</h3>
            <span class="count-badge" id="user-count"><?= count($allUsers) ?> <?= count($allUsers) === 1 ? 'user' : 'users' ?></span>
        </div>

        <div class="table-responsive">
            <table>
                <thead>
                    <tr>
                        <th style="width: 70px;">ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>City</th>
                    </tr>
                </thead>
                <tbody id="users-tbody">
                    <?php if (count($allUsers) > 0): ?>
                        <?php foreach ($allUsers as $user): ?>
                            <tr>
                                <td>#<?= htmlspecialchars($user['id'], ENT_QUOTES, 'UTF-8') ?></td>
                                <td><strong><?= htmlspecialchars($user['name'], ENT_QUOTES, 'UTF-8') ?></strong></td>
                                <td><?= htmlspecialchars($user['age'] !== null && $user['age'] !== '' ? $user['age'] : '—', ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?= htmlspecialchars($user['city'], ENT_QUOTES, 'UTF-8') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr id="empty-row">
                            <td colspan="4" class="empty-row">No users registered yet in database.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- BONUS 2: JAVASCRIPT AJAX (FETCH) IMPLEMENTATION -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('signup-form');
    const ajaxCheckbox = document.getElementById('ajax-enabled');
    const alertContainer = document.getElementById('alert-container');
    const nameInput = document.getElementById('name');
    const ageInput = document.getElementById('age');
    const cityInput = document.getElementById('city');
    const errorName = document.getElementById('error-name');
    const errorCity = document.getElementById('error-city');
    const usersTbody = document.getElementById('users-tbody');
    const userCount = document.getElementById('user-count');
    const submitBtn = document.getElementById('submit-btn');

    form.addEventListener('submit', async function(e) {
        if (!ajaxCheckbox.checked) {
            // Traditional full page POST submission fallback
            return;
        }

        e.preventDefault();

        // Clear previous error styles
        errorName.textContent = '';
        errorCity.textContent = '';
        nameInput.classList.remove('input-error');
        cityInput.classList.remove('input-error');
        alertContainer.innerHTML = '';

        const formData = {
            name: nameInput.value.trim(),
            age: ageInput.value.trim(),
            city: cityInput.value.trim()
        };

        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';

        try {
            const response = await fetch('signup.php?ajax=1', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                // Show Welcome message banner (Escaped)
                alertContainer.innerHTML = `
                    <div class="alert alert-success">
                        <span>🎉</span>
                        <strong>${escapeHTML(result.message)}</strong>
                    </div>
                `;

                // Add newly inserted user to table
                const emptyRow = document.getElementById('empty-row');
                if (emptyRow) emptyRow.remove();

                const newRow = document.createElement('tr');
                newRow.style.animation = 'fadeIn 0.4s ease';
                newRow.innerHTML = `
                    <td>#${escapeHTML(String(result.user.id))}</td>
                    <td><strong>${escapeHTML(result.user.name)}</strong></td>
                    <td>${result.user.age ? escapeHTML(String(result.user.age)) : '—'}</td>
                    <td>${escapeHTML(result.user.city)}</td>
                `;
                usersTbody.insertBefore(newRow, usersTbody.firstChild);

                // Update count badge
                const currentRows = usersTbody.querySelectorAll('tr').length;
                userCount.textContent = `${currentRows} ${currentRows === 1 ? 'user' : 'users'}`;

                // Reset form inputs
                form.reset();
                nameInput.focus();
            } else if (result.errors) {
                if (result.errors.name) {
                    errorName.textContent = result.errors.name;
                    nameInput.classList.add('input-error');
                }
                if (result.errors.city) {
                    errorCity.textContent = result.errors.city;
                    cityInput.classList.add('input-error');
                }
            }
        } catch (err) {
            alertContainer.innerHTML = `
                <div class="alert alert-error">
                    <span>⚠️</span>
                    <strong>An unexpected error occurred. Please check database connection.</strong>
                </div>
            `;
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit Sign Up';
        }
    });

    function escapeHTML(str) {
        return str.replace(/[&<>'"]/g, 
            tag => ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                "'": '&#39;',
                '"': '&quot;'
            }[tag] || tag)
        );
    }
});
</script>

</body>
</html>
