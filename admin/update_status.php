<?php
// admin/update_status.php
header('Content-Type: application/json');
require_once __DIR__ . '/../config.php';

$input = json_decode(file_get_contents('php://input'), true) ?? $_POST;
$id = (int)($input['id'] ?? 0);
$status = trim($input['status'] ?? 'PENDING');

if ($id) {
    $stmt = $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
