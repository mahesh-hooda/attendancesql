<?php
// ===============================================
// Database Configuration
// ===============================================

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'nsut_attendance');

// ===============================================
// SQL DEMO: Database Connection using MySQLi
// Establishing connection to MySQL database
// ===============================================
function getDbConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

// Session configuration
session_start();

// Helper function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['user_type']);
}

// Helper function to check if user is teacher
function isTeacher() {
    return isLoggedIn() && $_SESSION['user_type'] === 'teacher';
}

// Helper function to check if user is student
function isStudent() {
    return isLoggedIn() && $_SESSION['user_type'] === 'student';
}
?>
