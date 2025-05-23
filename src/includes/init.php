<?php
/**
 * Score Board - Initialization File
 * This file handles common initialization tasks for all pages
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include the database connection
require_once __DIR__ . '/../core/Database.php';

// Get database instance
$db = Database::getInstance();

// Helper function to set toast notifications
function setToast($type, $title, $message) {
    $_SESSION['toast'] = [
        'type' => $type,
        'title' => $title,
        'message' => $message
    ];
}
