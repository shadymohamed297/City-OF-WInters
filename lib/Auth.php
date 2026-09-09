<?php

namespace App;

use PDO;

class Auth
{
    private PDO $pdo;
    private string $sessionName;
    private int $sessionLifetime;
    private string $cookiePath;
    private string $cookieDomain;
    private bool $cookieSecure;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->sessionName = getenv('SESSION_NAME') ?: 'cw_session';
        $this->sessionLifetime = (int) (getenv('SESSION_LIFETIME') ?: 7200);
        $this->cookiePath = '/';
        $this->cookieDomain = getenv('COOKIE_DOMAIN') ?: '';
        $this->cookieSecure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

        if (session_status() === PHP_SESSION_NONE) {
            session_name($this->sessionName);
            session_set_cookie_params([
                'lifetime' => $this->sessionLifetime,
                'path' => $this->cookiePath,
                'domain' => $this->cookieDomain,
                'secure' => $this->cookieSecure,
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public function register(string $email, string $password, array $metadata = []): array
    {
        $email = strtolower(trim($email));
        $existing = Database::table('users')->where(['email' => $email]);
        if (!empty($existing)) {
            Response::error('DUPLICATE_EMAIL', 'هذا البريد مسجل بالفعل، حاول تسجيل الدخول', 409);
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        $userId = Database::uuid();
        Database::table('users')->insert([
            'id' => $userId,
            'email' => $email,
            'password_hash' => $passwordHash,
            'email_verified' => true,
            'is_active' => true,
        ]);

        $fullName = $metadata['full_name'] ?? null;
        $phone = $metadata['phone'] ?? null;
        Database::table('profiles')->insert([
            'id' => $userId,
            'full_name' => $fullName,
            'phone' => $phone,
        ]);
        Database::table('user_roles')->insert([
            'id' => Database::uuid(),
            'user_id' => $userId,
            'role' => 'customer',
        ]);

        // Link guest orders matching email or phone
        $this->linkGuestOrders($userId, $email, $phone);

        return ['id' => $userId];
    }

    public function login(string $email, string $password): array
    {
        $email = strtolower(trim($email));
        $user = Database::table('users')->find('email', $email);
        if (!$user || !password_verify($password, $user['password_hash'])) {
            Response::error('INVALID_CREDENTIALS', 'البريد أو كلمة المرور غير صحيحة', 401);
        }
        if (!$user['is_active']) {
            Response::error('ACCOUNT_DISABLED', 'الحساب معطل', 403);
        }

        $this->regenerateSession();
        $_SESSION['user_id'] = $user['id'];

        Database::table('users')->update('id', $user['id'], [
            'last_sign_in_at' => date('Y-m-d H:i:s'),
        ]);

        return $this->userResponse($user['id']);
    }

    public function logout(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie($this->sessionName, '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public function me(): ?array
    {
        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            return null;
        }
        return $this->userResponse($userId);
    }

    public function requireAuth(): array
    {
        $user = $this->me();
        if (!$user) {
            Response::unauthorized();
        }
        return $user;
    }

    public function requireAdmin(): array
    {
        $user = $this->requireAuth();
        $role = Database::table('user_roles')->find('user_id', $user['id']);
        if (!$role || $role['role'] !== 'admin') {
            Response::forbidden('Admin role required');
        }
        $user['role'] = $role['role'];
        return $user;
    }

    public function isAdmin(string $userId): bool
    {
        $role = Database::table('user_roles')->find('user_id', $userId);
        return $role && $role['role'] === 'admin';
    }

    private function regenerateSession(): void
    {
        session_regenerate_id(true);
    }

    private function userResponse(string $userId): array
    {
        $user = Database::table('users')->find('id', $userId);
        $profile = Database::table('profiles')->find('id', $userId);
        $roles = Database::table('user_roles')->where(['user_id' => $userId]);
        $roleNames = array_column($roles, 'role');

        return [
            'id' => $user['id'],
            'email' => $user['email'],
            'full_name' => $profile['full_name'] ?? null,
            'phone' => $profile['phone'] ?? null,
            'avatar_url' => $profile['avatar_url'] ?? null,
            'roles' => $roleNames,
            'is_active' => (bool) $user['is_active'],
            'email_verified' => (bool) $user['email_verified'],
            'created_at' => $user['created_at'],
            'last_sign_in_at' => $user['last_sign_in_at'] ?? null,
        ];
    }

    private function linkGuestOrders(string $userId, string $email, ?string $phone): void
    {
        $params = ['user_id' => $userId, 'email' => $email];
        $sql = "UPDATE `orders` SET user_id = :user_id, guest_email = NULL, guest_phone = NULL, guest_name = NULL WHERE user_id IS NULL AND (";
        $conditions = [];
        if ($email) {
            $conditions[] = "LOWER(guest_email) = LOWER(:email)";
        }
        if ($phone) {
            $conditions[] = "REGEXP_REPLACE(guest_phone, '[^0-9]', '') = REGEXP_REPLACE(:phone_clean, '[^0-9]', '')";
            $params['phone_clean'] = $phone;
        }
        if (empty($conditions)) {
            return;
        }
        $sql .= implode(' OR ', $conditions) . ')';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
    }
}
