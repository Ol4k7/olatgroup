<?php
/**
 * api/projects.php
 * Path: /olatgroup/api/projects.php
 */

// 1. IMPORT THE BRAIN
require_once dirname(__DIR__) . '/config/config.php';

header('Content-Type: application/json');

// 2. GET REQUESTED SERVICE (facilities, digital, or gallery)
$service = $_GET['service'] ?? '';
$allowed = ['facilities', 'digital', 'gallery'];

try {
    if (in_array($service, $allowed)) {
        // Fetch specific category
        $stmt = $pdo->prepare("SELECT * FROM projects WHERE service_type = :service ORDER BY created_at DESC");
        $stmt->execute([':service' => $service]);
        $results = $stmt->fetchAll();
    } else {
        // Fetch all projects if no valid service is specified
        $stmt = $pdo->query("SELECT * FROM projects ORDER BY created_at DESC");
        $results = $stmt->fetchAll();
    }

    // 3. REFORMAT FOR FRONTEND COMPATIBILITY
    // We map the SQL column names to the keys your existing JS expects
    $formatted = array_map(function($row) {
        return [
            'id'          => $row['id'],
            'title'       => $row['title'],
            'description' => $row['description'],
            'image'       => '/olatgroup' . $row['image_path'],
            'service'     => $row['service_type'],
            'type'        => $row['digital_type'],      // For Digital: 'web' or 'graphics'
            'url'         => $row['web_url'],           // For Web Design
            'category'    => $row['graphics_category'], // For Graphics Design
            'timestamp'   => $row['created_at']
        ];
    }, $results);

    echo json_encode($formatted);

} catch (PDOException $e) {
    // Return empty array on error to prevent JS crashes
    http_response_code(500);
    echo json_encode([]);
}