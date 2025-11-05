<?php
require_once '../config.php';
header('Content-Type: application/json');
$service = $_GET['service'] ?? '';
if (in_array($service, ['facilities', 'digital'])) {
    $data = json_decode(file_get_contents(DATA_FILE), true);
    echo json_encode($data[$service] ?? []);
}
?>