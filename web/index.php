<?php
// index.php

// Get the page parameter from the URL
$page = $_GET['page'] ?? 'index2';

// Sanitize the page parameter to prevent directory traversal attacks
$page = preg_replace('/[^a-zA-Z0-9_-]/', '', $page); // Allow only alphanumeric, underscores, and hyphens

// Check if the requested page file exists
$pageFile = $page . '.php';

// If the page file exists, include it; otherwise, show 404
if (file_exists($pageFile)) {
    include $pageFile;
} else {
    include '404.php'; // Show a 404 page if the page is not found
}
?>