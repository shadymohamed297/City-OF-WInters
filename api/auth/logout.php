<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';
Csrf::middleware();

$auth = new Auth(Database::connection());
$auth->logout();

Response::ok(['message' => 'Logged out']);
