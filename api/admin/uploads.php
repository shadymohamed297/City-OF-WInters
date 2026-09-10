<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

Csrf::middleware();
$auth = new App\Auth(Database::connection());
$auth->requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_FILES['file'])) {
    Response::validationError('No file uploaded');
}

$file = $_FILES['file'];
$folder = preg_replace('/[^a-zA-Z0-9_-]/', '', $_POST['folder'] ?? 'misc');

if ($file['error'] !== UPLOAD_ERR_OK) {
    Response::error('UPLOAD_ERROR', 'File upload failed', 400);
}

$maxSize = 10 * 1024 * 1024;
if ($file['size'] > $maxSize) {
    Response::error('FILE_TOO_LARGE', 'File too large (max 10MB)', 400);
}

$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowedMimes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
if (!in_array($mime, $allowedMimes, true)) {
    Response::error('INVALID_TYPE', 'Invalid file type', 400);
}

$ext = match ($mime) {
    'image/jpeg' => 'jpg',
    'image/png' => 'png',
    'image/webp' => 'webp',
    'image/gif' => 'gif',
    default => 'bin',
};

$filename = bin2hex(random_bytes(16)) . '.' . $ext;
$uploadDir = __DIR__ . '/../../../uploads/' . $folder . '/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
    Response::error('UPLOAD_ERROR', 'Failed to save file', 500);
}

$publicPath = '/uploads/' . $folder . '/' . $filename;

Response::ok(['url' => $publicPath]);
