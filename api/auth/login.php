<?php

use App\Auth;
use App\Csrf;
use App\Database;
use App\Response;
use App\Validator;

require_once __DIR__ . '/../../vendor/autoload.php';
Csrf::middleware();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;

$email = Validator::email($input['email'] ?? '');
$password = Validator::string($input['password'] ?? '', 72, 'Password');

$auth = new Auth(Database::connection());
$user = $auth->login($email, $password);

Response::ok($user);
