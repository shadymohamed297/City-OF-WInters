<?php

use App\Auth;
use App\Database;
use App\Response;

require_once __DIR__ . '/../../vendor/autoload.php';

$auth = new Auth(Database::connection());
$user = $auth->me();

if (!$user) {
    Response::unauthorized();
}

Response::ok($user);
