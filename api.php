<?php
/**
 * ============================================================================
 * BACKEND API SCRIPT (api.php)
 * ============================================================================
 * 
 * Purpose:
 * This file acts as the backend Application Programming Interface (API).
 * It listens for HTTP requests sent by the frontend JavaScript (`fetch`),
 * interacts with the MySQL database, and returns data in JSON format.
 * 
 * Supported Operations (CRUD):
 * - Create: Insert a new task into the database.
 * - Read:   Fetch all tasks from the database.
 * - Update: Toggle a task between complete (1) and incomplete (0).
 * - Delete: Delete a single task or clear all completed tasks.
 */

// ----------------------------------------------------------------------------
// Step 1: Set Response Header to JSON
// ----------------------------------------------------------------------------
// By default, PHP sends responses as `text/html`.
// Because our frontend JavaScript communicates via JSON, we set the header
// to `application/json` so the browser understands the data structure.
header('Content-Type: application/json');

// ----------------------------------------------------------------------------
// Step 2: Include Database Connection
// ----------------------------------------------------------------------------
// `require_once` loads the database connection object ($conn) from `db.php`.
// If `db.php` is missing or fails, PHP will stop execution with a fatal error.
require_once 'db.php';

// ----------------------------------------------------------------------------
// Step 3: Parse Incoming Request Data
// ----------------------------------------------------------------------------
// Modern frontend applications send data as raw JSON in the request body.
// 1. `file_get_contents('php://input')` reads raw HTTP request body data.
// 2. `json_decode(..., true)` converts the JSON string into an associative PHP array.
// 3. We use a fallback to `$_POST` to support traditional HTML form submissions as well.
$input_raw = file_get_contents('php://input');
$input_json = json_decode($input_raw, true);
$data = is_array($input_json) ? $input_json : $_POST;

// Determine request method (GET, POST, etc.) and requested action name.
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($data['action']) ? $data['action'] : (isset($_GET['action']) ? $_GET['action'] : '');


// ============================================================================
// 1. READ / FETCH ALL TASKS (GET)
// ============================================================================
// When the page first loads, the frontend asks for all existing tasks.
// Query: Fetch all rows from `todos` ordered by newest first (ORDER BY id DESC).
if ($method === 'GET' || $action === 'get') {
    $sql = "SELECT id, text, completed FROM todos ORDER BY id DESC";
    $result = $conn->query($sql);
    $todos = [];
    
    // Loop through each row returned by the database
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Convert database values to proper PHP data types
            $todos[] = [
                'id' => (int)$row['id'],            // Integer ID
                'text' => $row['text'],              // Task description string
                'completed' => (bool)$row['completed'] // Convert 1/0 to true/false for JavaScript
            ];
        }
    }
    
    // Send back JSON response: {"success": true, "items": [...]}
    echo json_encode(['success' => true, 'items' => $todos]);
    exit;
}


// ============================================================================
// 2. CREATE / ADD A NEW TASK (POST action = 'add')
// ============================================================================
// When the user submits the form to add a task.
if ($action === 'add') {
    // Sanitize user input by removing extra surrounding spaces
    $text = trim($data['text'] ?? '');
    
    // Validation: Prevent adding empty tasks
    if (empty($text)) {
        echo json_encode(['success' => false, 'error' => 'Task text cannot be empty']);
        exit;
    }
    
    /**
     * WHY USE PREPARED STATEMENTS ($conn->prepare)?
     * Security Lesson: Never concatenate user input directly into SQL queries!
     * Direct string concatenation allows "SQL Injection" attacks.
     * Prepared statements ensure user input is treated strictly as data, not executable SQL.
     */
    $stmt = $conn->prepare("INSERT INTO todos (text, completed) VALUES (?, 0)");
    
    // "s" indicates the type is 'string' for the parameter $text
    $stmt->bind_param("s", $text);
    
    if ($stmt->execute()) {
        // $stmt->insert_id gives the unique auto-generated ID of the newly inserted row
        $new_id = $stmt->insert_id;
        
        echo json_encode([
            'success' => true,
            'item' => [
                'id' => $new_id,
                'text' => $text,
                'completed' => false
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to add task: ' . $stmt->error]);
    }
    
    // Close the statement to free memory
    $stmt->close();
    exit;
}


// ============================================================================
// 3. UPDATE / TOGGLE COMPLETED STATUS (POST action = 'toggle')
// ============================================================================
// When the user clicks the checkmark button on a task.
if ($action === 'toggle') {
    $id = isset($data['id']) ? (int)$data['id'] : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid task ID']);
        exit;
    }
    
    // `completed = NOT completed` flips 0 to 1 and 1 to 0 in SQL.
    $stmt = $conn->prepare("UPDATE todos SET completed = NOT completed WHERE id = ?");
    
    // "i" indicates the type is 'integer' for the parameter $id
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to update task: ' . $stmt->error]);
    }
    
    $stmt->close();
    exit;
}


// ============================================================================
// 4. DELETE / REMOVE A SINGLE TASK (POST action = 'delete')
// ============================================================================
// When the user clicks the delete (×) button on a task.
if ($action === 'delete') {
    $id = isset($data['id']) ? (int)$data['id'] : 0;
    
    if ($id <= 0) {
        echo json_encode(['success' => false, 'error' => 'Invalid task ID']);
        exit;
    }
    
    $stmt = $conn->prepare("DELETE FROM todos WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to delete task: ' . $stmt->error]);
    }
    
    $stmt->close();
    exit;
}


// ============================================================================
// 5. BULK DELETE / CLEAR ALL COMPLETED TASKS (POST action = 'clear_completed')
// ============================================================================
// When the user clicks the "Clear completed" button.
if ($action === 'clear_completed') {
    // Delete all rows where completed flag is 1 (true)
    $sql = "DELETE FROM todos WHERE completed = 1";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Failed to clear completed tasks: ' . $conn->error]);
    }
    exit;
}


// ----------------------------------------------------------------------------
// Fallback: If no recognized action was provided
// ----------------------------------------------------------------------------
echo json_encode(['success' => false, 'error' => 'Invalid or missing API action']);
?>
