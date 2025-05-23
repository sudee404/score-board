<?php
/**
 * Score Board - Fetch Scores API
 * This file provides JSON data for the scoreboard's AJAX functionality
 */

require_once __DIR__ . '/../includes/init.php';

// Get all users with their total scores
$users = $db->query("
    SELECT 
        u.id,
        u.name,
        COALESCE(SUM(s.points), 0) as total_points,
        COUNT(DISTINCT s.judge_id) as judges_count
    FROM users u
    LEFT JOIN scores s ON u.id = s.user_id
    GROUP BY u.id, u.name
    ORDER BY total_points DESC
")->fetchAll();

// Set content type to JSON
header('Content-Type: application/json');

// Return the data
echo json_encode($users);
