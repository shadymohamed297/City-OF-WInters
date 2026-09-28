<?php

use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';

$method = $_SERVER['REQUEST_METHOD'];
$pdo = Database::connection();

// Ensure manuscripts table exists
$pdo->exec("
    CREATE TABLE IF NOT EXISTS manuscripts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        phone VARCHAR(100) NOT NULL,
        email VARCHAR(255) NOT NULL,
        file_name VARCHAR(255) DEFAULT NULL,
        file_path VARCHAR(500) DEFAULT NULL,
        file_size INT DEFAULT 0,
        message TEXT,
        status VARCHAR(50) DEFAULT 'pending',
        notes TEXT DEFAULT NULL,
        ip_address VARCHAR(100) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
");

if ($method === 'GET') {
    $status = $_GET['status'] ?? null;
    $search = trim($_GET['search'] ?? '');

    $sql = 'SELECT * FROM manuscripts WHERE 1=1';
    $params = [];

    if ($status && $status !== 'all') {
        $sql .= ' AND status = :status';
        $params['status'] = $status;
    }

    if ($search !== '') {
        $sql .= ' AND (name LIKE :s1 OR phone LIKE :s2 OR email LIKE :s3 OR message LIKE :s4)';
        $searchTerm = '%' . $search . '%';
        $params['s1'] = $searchTerm;
        $params['s2'] = $searchTerm;
        $params['s3'] = $searchTerm;
        $params['s4'] = $searchTerm;
    }

    $sql .= ' ORDER BY created_at DESC';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $manuscripts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Counts by status
    $countsStmt = $pdo->query('
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = "pending" OR status IS NULL THEN 1 ELSE 0 END) as pending_count,
            SUM(CASE WHEN status = "reviewing" THEN 1 ELSE 0 END) as reviewing_count,
            SUM(CASE WHEN status = "contacted" THEN 1 ELSE 0 END) as contacted_count,
            SUM(CASE WHEN status = "accepted" THEN 1 ELSE 0 END) as accepted_count,
            SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected_count
        FROM manuscripts
    ');
    $counts = $countsStmt->fetch(PDO::FETCH_ASSOC);

    Response::ok([
        'manuscripts' => $manuscripts,
        'counts' => $counts
    ]);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

if ($method === 'POST' || $method === 'PUT') {
    $id = isset($input['id']) ? (int)$input['id'] : 0;
    if (!$id) {
        Response::validationError('معرف الطلب غير صحيح');
    }

    $status = Validator::stringOrNull($input['status'] ?? null, 50);
    $notes = Validator::stringOrNull($input['notes'] ?? null, 5000);

    $fields = [];
    $params = ['id' => $id];

    if ($status !== null) {
        $fields[] = 'status = :status';
        $params['status'] = $status;
    }
    if ($notes !== null) {
        $fields[] = 'notes = :notes';
        $params['notes'] = $notes;
    }

    if (empty($fields)) {
        Response::validationError('لا توجد بيانات للتعديل');
    }

    $sql = 'UPDATE manuscripts SET ' . implode(', ', $fields) . ' WHERE id = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    Response::ok(['success' => true, 'message' => 'تم تحديث حالة الطلب بنجاح']);
    exit;
}

if ($method === 'DELETE') {
    $id = isset($_GET['id']) ? (int)$_GET['id'] : (isset($input['id']) ? (int)$input['id'] : 0);
    if (!$id) {
        Response::validationError('معرف الطلب غير صحيح');
    }

    // Optional: unlink file
    $stmt = $pdo->prepare('SELECT file_path FROM manuscripts WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $id]);
    $row = $stmt->fetch();
    if ($row && !empty($row['file_path'])) {
        $fullPath = __DIR__ . '/../../' . $row['file_path'];
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }

    $delStmt = $pdo->prepare('DELETE FROM manuscripts WHERE id = :id');
    $delStmt->execute(['id' => $id]);

    Response::ok(['success' => true, 'message' => 'تم حذف الطلب بنجاح']);
    exit;
}

Response::error('METHOD_NOT_ALLOWED', 'الطريقة غير مدعومة', 405);
