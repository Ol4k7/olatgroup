<?php
/**
 * admin/delete.php
 * Path: /olatgroup/admin/delete.php
 */

// 1. IMPORT THE BRAIN (Handles session & DB connection)
require_once dirname(__DIR__) . '/config/config.php';

// 2. SECURITY CHECK
// Only allow deletion if the admin session is active
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized access']);
    exit;
}

// Get ID from the request
$id = $_GET['id'] ?? null;

if (!$id) {
    echo json_encode(['error' => 'No ID provided']);
    exit;
}

try {
    // 3. FETCH IMAGE PATH BEFORE DELETING RECORD
    // We need to know the filename to remove it from the hard drive
    $stmt = $pdo->prepare("SELECT image_path FROM projects WHERE id = ?");
    $stmt->execute([$id]);
    $project = $stmt->fetch();

    if ($project) {
        // Build the absolute path to the file on XAMPP
        // Result: C:/xampp/htdocs/olatgroup/public/projects/filename.jpg
        $full_image_path = $_SERVER['DOCUMENT_ROOT'] . '/olatgroup' . $project['image_path'];

        // 4. DELETE THE PHYSICAL FILE
        if (file_exists($full_image_path)) {
            unlink($full_image_path);
        }

        // 5. DELETE FROM DATABASE (Prepared Statement prevents SQLi)
        $del = $pdo->prepare("DELETE FROM projects WHERE id = ?");
        $del->execute([$id]);

        echo json_encode(['success' => true, 'message' => 'Project and file deleted.']);
    } else {
        echo json_encode(['error' => 'Project not found in database.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error during deletion.']);
}