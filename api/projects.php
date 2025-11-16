<?php
require_once '../config.php';
header('Content-Type: application/json');

// Get requested service
$service = $_GET['service'] ?? '';

// Allowed services
$allowed = ['facilities', 'digital', 'gallery'];

// Load data safely
$data = [];
if (file_exists(DATA_FILE)) {
    $json = file_get_contents(DATA_FILE);
    $data = json_decode($json, true) ?? [];
}

// Respond based on requested service
if (in_array($service, $allowed)) {
    echo json_encode($data[$service] ?? []);
} else {
    // Return all projects if no service specified or invalid service
    $all = [];
    foreach ($allowed as $s) {
        if (isset($data[$s])) $all = array_merge($all, $data[$s]);
    }
    echo json_encode($all);
}
?>