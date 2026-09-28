<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../vendor/autoload.php';

// Allow POST requests only
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    Response::error('METHOD_NOT_ALLOWED', 'طريقة الطلب غير مسموح بها', 405);
}

// Get form fields
$name = trim($_POST['name'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

// Basic validation
if (empty($name)) {
    Response::validationError('يرجى إدخال اسم المؤلف بالكامل');
}
if (empty($phone)) {
    Response::validationError('يرجى إدخال رقم الهاتف / الواتساب للتواصل');
}
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    Response::validationError('يرجى إدخال بريد إلكتروني صحيح');
}

$uploadedFileName = null;
$uploadedFilePath = null;
$uploadedFileSize = 0;

// Handle file upload if provided
if (isset($_FILES['manuscript']) && $_FILES['manuscript']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['manuscript'];
    $maxSize = 50 * 1024 * 1024; // 50 MB
    
    if ($file['size'] > $maxSize) {
        Response::validationError('حجم الملف كبير جداً. الحد الأقصى المسموح به هو 50 ميجابايت');
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowedExts = ['pdf', 'doc', 'docx', 'rtf', 'txt', 'epub'];
    if (!in_array($ext, $allowedExts, true)) {
        Response::validationError('صيغة الملف غير مدعومة. الصيغ المقبولة: PDF, Word (DOC, DOCX), RTF, TXT, EPUB');
    }

    // Target upload directory
    $uploadDir = __DIR__ . '/../uploads/manuscripts';
    if (!is_dir($uploadDir)) {
        @mkdir($uploadDir, 0755, true);
        // Put an .htaccess in uploadDir to prevent executing any script files
        @file_put_contents($uploadDir . '/.htaccess', "<FilesMatch \".*\">\n  Require all granted\n</FilesMatch>\n<FilesMatch \"\.(php|phtml|php3|php4|php5|php7|phps)$\">\n  Deny from all\n</FilesMatch>\n");
    }

    $safeName = time() . '_' . bin2hex(random_bytes(8)) . '.' . $ext;
    $targetPath = $uploadDir . '/' . $safeName;

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        $uploadedFileName = basename($file['name']);
        $uploadedFilePath = 'uploads/manuscripts/' . $safeName;
        $uploadedFileSize = $file['size'];
    }
}

try {
    $pdo = Database::connection();

    // Create manuscripts table if not exists
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
            ip_address VARCHAR(100) DEFAULT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

    $stmt = $pdo->prepare('
        INSERT INTO manuscripts (name, phone, email, file_name, file_path, file_size, message, status, ip_address)
        VALUES (:name, :phone, :email, :file_name, :file_path, :file_size, :message, :status, :ip)
    ');

    $stmt->execute([
        'name' => $name,
        'phone' => $phone,
        'email' => $email,
        'file_name' => $uploadedFileName,
        'file_path' => $uploadedFilePath,
        'file_size' => $uploadedFileSize,
        'message' => $message,
        'status' => 'pending',
        'ip' => $ip,
    ]);

    $insertId = $pdo->lastInsertId();

    // Optionally send email notification to Dar
    $subject = "طلب نشر عمل جديد: " . $name . " (رقم #" . $insertId . ")";
    $body = "تم استلام طلب نشر عمل جديد عبر الموقع الإلكتروني:\n\n";
    $body .= "الاسم: " . $name . "\n";
    $body .= "الهاتف: " . $phone . "\n";
    $body .= "البريد الإلكتروني: " . $email . "\n";
    if ($uploadedFileName) {
        $body .= "الملف المرفق: " . $uploadedFileName . " (" . round($uploadedFileSize / 1024, 1) . " KB)\n";
        $body .= "رابط الملف: https://yellow-mandrill-766893.hostingersite.com/" . $uploadedFilePath . "\n";
    }
    $body .= "الرسالة:\n" . ($message ?: 'لا توجد رسالة إضافية') . "\n\n";
    $body .= "تاريخ التقديم: " . date('Y-m-d H:i:s') . "\n";

    $headers = "From: noreply@madinatalodabaa.com\r\n";
    $headers .= "Reply-To: " . $email . "\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

    @mail("info@madinetalodabaa.com", $subject, $body, $headers);

    Response::ok([
        'success' => true,
        'id' => (int)$insertId,
        'message' => 'تم استلام عملك الأدبي بنجاح. سيقوم فريق القراءة بمراجعته والتواصل معك قريباً.',
    ]);
} catch (\Throwable $e) {
    // If DB fails, fallback to recording submission in a backup json file
    $backupDir = __DIR__ . '/../storage';
    if (!is_dir($backupDir)) @mkdir($backupDir, 0755, true);
    $backupData = [
        'timestamp' => date('c'),
        'name' => $name,
        'phone' => $phone,
        'email' => $email,
        'file' => $uploadedFilePath,
        'message' => $message,
        'error' => $e->getMessage()
    ];
    @file_put_contents($backupDir . '/manuscripts_backup.jsonl', json_encode($backupData, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);

    Response::ok([
        'success' => true,
        'id' => time(),
        'message' => 'تم استلام عملك الأدبي بنجاح. سيقوم فريق القراءة بمراجعته والتواصل معك قريباً.',
    ]);
}
