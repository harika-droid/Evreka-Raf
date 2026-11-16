<?php
require __DIR__ . '/../db.php';
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
try {
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'GET') {
        echo is_empty() ? '{"empty":true}' : json_encode(read_all(), JSON_UNESCAPED_UNICODE);
    } elseif ($method === 'PUT' || $method === 'POST') {
        $s = json_decode(file_get_contents('php://input'), true);
        if (!is_array($s) || !isset($s['users'], $s['books']) || !is_array($s['users']) || !is_array($s['books'])) {
            http_response_code(400); echo '{"error":"gecersiz veri"}'; exit;
        }
        write_all($s);
        echo '{"ok":true}';
    } else {
        http_response_code(405); echo '{"error":"izin yok"}';
    }
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Veritabani hatasi: ' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}
