<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;

require_once __DIR__ . '/../../../vendor/autoload.php';

Csrf::middleware();
$auth = new App\Auth(Database::connection());
$auth->requireAdmin();

$pdo = Database::connection();
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($input['target_user_id'])) {
    $targetId = $input['target_user_id'];
    $makeAdmin = (bool)($input['make_admin'] ?? false);

    if ($makeAdmin) {
        $stmt = $pdo->prepare('INSERT IGNORE INTO user_roles (user_id, role) VALUES (:uid, :role)');
        $stmt->execute(['uid' => $targetId, 'role' => 'admin']);
    } else {
        $stmt = $pdo->prepare('DELETE FROM user_roles WHERE user_id = :uid AND role = :role');
        $stmt->execute(['uid' => $targetId, 'role' => 'admin']);
    }

    Response::ok(['message' => 'Role updated']);
    exit;
}

Response::error('METHOD_NOT_ALLOWED', 'Method not allowed', 405);
