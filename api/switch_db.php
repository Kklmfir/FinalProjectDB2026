<?php
/**
 * api/switch_db.php
 * Endpoint untuk mengganti mode database via AJAX atau form POST.
 * Respons: JSON
 */

require_once __DIR__ . '/../config/database.php';

header('Content-Type: application/json');

// Terima mode dari JSON body (fetch) atau POST form
$input = file_get_contents('php://input');
$data  = json_decode($input, true);

$mode = $data['mode'] ?? $_POST['mode'] ?? '';
$mode = trim($mode);

if (switchDBMode($mode)) {
    echo json_encode(['success' => true, 'mode' => $mode, 'label' => getDBLabel()]);
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Mode tidak valid. Gunakan: local atau supabase.']);
}
