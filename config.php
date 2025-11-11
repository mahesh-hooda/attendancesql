<?php
// ===============================================
// Database Configuration - SQLite Version
// ===============================================

// Database file path
define('DB_PATH', __DIR__ . '/nsut_attendance.db');

// ===============================================
// SQL DEMO: Database Connection using PDO (SQLite)
// Establishing connection to SQLite database
// PDO provides a consistent interface for different databases
// ===============================================
function getDbConnection() {
    try {
        // Create PDO instance for SQLite
        $conn = new PDO('sqlite:' . DB_PATH);

        // Set error mode to exceptions for better error handling
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Enable foreign key constraints (disabled by default in SQLite)
        $conn->exec('PRAGMA foreign_keys = ON;');

        return $conn;
    } catch(PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
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
