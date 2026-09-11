<?php

use App\Database;
use App\Response;

require_once __DIR__ . '/../vendor/autoload.php';

try {
    $pdo = Database::connection();
    $email = 'cityofwriters24@gmail.com';
    $password = 'cityofwriters24@gmail.com';
    $passwordHash = password_hash($password, PASSWORD_DEFAULT);

    // 1. Check if user exists
    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE LOWER(email) = LOWER(:email)");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $userId = $user['id'];
        $updateStmt = $pdo->prepare("
            UPDATE `users`
            SET `password_hash` = :password_hash,
                `is_active` = 1,
                `email_verified` = 1,
                `updated_at` = NOW()
            WHERE `id` = :id
        ");
        $updateStmt->execute([
            'password_hash' => $passwordHash,
            'id' => $userId,
        ]);
        $action = 'updated';
    } else {
        $userId = Database::uuid();
        $insertStmt = $pdo->prepare("
            INSERT INTO `users` (`id`, `email`, `password_hash`, `email_verified`, `is_active`, `created_at`, `updated_at`)
            VALUES (:id, :email, :password_hash, 1, 1, NOW(), NOW())
        ");
        $insertStmt->execute([
            'id' => $userId,
            'email' => strtolower(trim($email)),
            'password_hash' => $passwordHash,
        ]);
        $action = 'created';
    }

    // 2. Ensure profile exists
    $profStmt = $pdo->prepare("SELECT * FROM `profiles` WHERE `id` = :id");
    $profStmt->execute(['id' => $userId]);
    $profile = $profStmt->fetch(PDO::FETCH_ASSOC);

    if (!$profile) {
        $insProf = $pdo->prepare("
            INSERT INTO `profiles` (`id`, `full_name`, `phone`, `created_at`, `updated_at`)
            VALUES (:id, 'Admin City of Writers', NULL, NOW(), NOW())
        ");
        $insProf->execute(['id' => $userId]);
    } else {
        if (empty($profile['full_name'])) {
            $updProf = $pdo->prepare("UPDATE `profiles` SET `full_name` = 'Admin City of Writers' WHERE `id` = :id");
            $updProf->execute(['id' => $userId]);
        }
    }

    // 3. Ensure admin role in user_roles
    $pdo->prepare("DELETE FROM `user_roles` WHERE `user_id` = :uid AND `role` != 'admin'")->execute(['uid' => $userId]);

    $roleCheck = $pdo->prepare("SELECT * FROM `user_roles` WHERE `user_id` = :uid AND `role` = 'admin'");
    $roleCheck->execute(['uid' => $userId]);
    $hasAdminRole = $roleCheck->fetch(PDO::FETCH_ASSOC);

    if (!$hasAdminRole) {
        $insRole = $pdo->prepare("
            INSERT INTO `user_roles` (`id`, `user_id`, `role`, `created_at`)
            VALUES (:id, :uid, 'admin', NOW())
        ");
        $insRole->execute([
            'id' => Database::uuid(),
            'uid' => $userId,
        ]);
    }

    // 4. Fetch final verification state
    $stmtUser = $pdo->prepare("SELECT id, email, is_active, email_verified, created_at, password_hash FROM `users` WHERE `id` = :id");
    $stmtUser->execute(['id' => $userId]);
    $finalUser = $stmtUser->fetch(PDO::FETCH_ASSOC);

    $pwdCheck = password_verify($password, $finalUser['password_hash']);
    unset($finalUser['password_hash']);

    $stmtRoles = $pdo->prepare("SELECT role FROM `user_roles` WHERE `user_id` = :uid");
    $stmtRoles->execute(['uid' => $userId]);
    $roles = $stmtRoles->fetchAll(PDO::FETCH_COLUMN);

    Response::ok([
        'status' => 'success',
        'action' => $action,
        'user' => $finalUser,
        'roles' => $roles,
        'password_verified' => $pwdCheck,
    ]);
} catch (\Throwable $e) {
    Response::serverError($e->getMessage());
}
