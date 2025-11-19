<?php
// Central PDO connection for Student Book Exchange project.
// This file creates a shared $pdo instance that can be reused
// by all pages and API endpoints in the application.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Basic connection settings for local WAMP environment.
// These values are kept simple on purpose for a student project.
$dbHost = '127.0.0.1';
$dbName = 'student_book_exchange';
$dbUser = 'root';
$dbPass = ''; // default WAMP password (empty)

// Data Source Name for MySQL with UTF-8.
$dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";

try {
    // Create a new PDO instance for the project.
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
} catch (PDOException $e) {
    // In a real production system this message would be logged,
    // but for a college project it is helpful to see the reason.
    http_response_code(500);
    echo 'Database connection failed: ' . htmlspecialchars($e->getMessage());
    exit;
}