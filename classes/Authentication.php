<?php

declare(strict_types=1);

class Authentication {
    public function __construct(
        private DatabaseTable $users,
        private string $usernameColumn,
        private string $passwordColumn
    ) {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
                || (($_SERVER['SERVER_PORT'] ?? null) === '443');
            session_set_cookie_params([
                'httponly' => true,
                'secure' => $isHttps,
                'samesite' => 'Lax',
                'path' => '/',
            ]);
            session_start();
        }
    }

    public function login(string $username, string $password): string {
        $username = strtolower(trim($username));
        $user = $this->users->find($this->usernameColumn, $username)[0] ?? null;

        if ($user !== null && !empty($user['isBanned'])) {
            return 'banned';
        }

        $hash = $user[$this->passwordColumn] ?? null;
        if ($user !== null && is_string($hash) && $hash !== '' && password_verify($password, $hash)) {
            session_regenerate_id(true);
            $_SESSION['user_id'] = (int) $user['id'];
            $_SESSION['username'] = $username;
            return 'success';
        }

        return 'invalid';
    }

    public function isLoggedIn(): bool {
        $userId = $_SESSION['user_id'] ?? null;
        $username = $_SESSION['username'] ?? null;
        if (!is_int($userId) || !is_string($username)) {
            return false;
        }

        $user = $this->users->findById($userId);
        return $user !== false
            && empty($user['isBanned'])
            && hash_equals(strtolower((string) $user[$this->usernameColumn]), strtolower($username));
    }

    public function logout(): void {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
    }

    public function csrfToken(): string {
        if (empty($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    public function validateCsrfToken(?string $token): bool {
        return is_string($token)
            && isset($_SESSION['csrf_token'])
            && is_string($_SESSION['csrf_token'])
            && hash_equals($_SESSION['csrf_token'], $token);
    }

    public function findUser(string $username): ?array {
        return $this->cleanUser($this->users->find($this->usernameColumn, strtolower(trim($username)))[0] ?? null);
    }

    public function findUserByResetToken(string $token): ?array {
        if (!preg_match('/^[a-f0-9]{64}$/', $token)) {
            return null;
        }
        return $this->cleanUser($this->users->find('resetToken', hash('sha256', $token))[0] ?? null);
    }

    public function saveUser(array $user): void {
        $this->users->save($user);
    }

    public function getUser(): ?array {
        if (!$this->isLoggedIn()) {
            return null;
        }
        $user = $this->cleanUser($this->users->findById($_SESSION['user_id']));
        if ($user !== null) {
            unset($user[$this->passwordColumn], $user['resetToken'], $user['resetTokenExpiry']);
        }
        return $user;
    }

    private function cleanUser(array|false|null $user): ?array {
        return $user ?: null;
    }
}
