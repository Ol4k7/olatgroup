<?php
session_start();
require_once '../config.php';
if (!$_SESSION['admin']) { http_response_code(403); exit; }

$id = $_GET['id'] ?? '';
if (!$id) { echo json_encode(['error' => 'No ID']); exit; }

$data = json_decode(file_get_contents(DATA_FILE), true);
$deleted = false;
$image = '';

foreach (['facilities', 'digital', 'gallery'] as $service) {
    foreach ($data[$service] as $i => $p) {
        if ($p['id'] === $id) {
            $image = $p['image'];
            unset($data[$service][$i]);
            $deleted = true;
            break 2;
        }
    }
}

if ($deleted) {
    $data['facilities'] = array_values($data['facilities']);
    $data['digital'] = array_values($data['digital']);
    $data['gallery'] = array_values($data['gallery'] ?? []);
    file_put_contents(DATA_FILE, json_encode($data, JSON_PRETTY_PRINT));
    if ($image && file_exists(__DIR__ . '/../' . $image)) {
        unlink(__DIR__ . '/../' . $image);
    }
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['error' => 'Not found']);
}
?>