<?php

declare(strict_types=1);

class Login {
    public function __construct(private Authentication $authentication, private array $appConfig = []) {
    }

    public function login(): array {
        return $this->loginPage();
    }

    public function loginSubmit(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->authentication->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            return $this->loginPage('Your form session has expired. Please try again.');
        }

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $status = filter_var($email, FILTER_VALIDATE_EMAIL) ? $this->authentication->login($email, $password) : 'invalid';

        if ($status !== 'success') {
            return $this->loginPage($status === 'banned' ? 'banned' : 'invalid', $email);
        }

        $reviewer = $this->authentication->getUser();
        return [
            'template' => 'LoginSuccess.html.php',
            'title' => 'Login successful',
            'variables' => ['reviewer' => $reviewer['name'] ?? ''],
        ];
    }

    public function forgotForm(): array {
        return [
            'template' => 'forgotpassword.html.php',
            'title' => 'Forgot password',
            'variables' => ['csrfToken' => $this->authentication->csrfToken()],
        ];
    }

    public function forgotSubmit(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->authentication->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            return $this->forgotPage('Your form session has expired. Please try again.');
        }

        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $resetLink = null;
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $user = $this->authentication->findUser($email);
            if ($user !== null) {
                $token = bin2hex(random_bytes(32));
                $user['resetToken'] = hash('sha256', $token);
                $user['resetTokenExpiry'] = (new DateTimeImmutable('+1 hour'))->format('Y-m-d H:i:s');
                $this->authentication->saveUser($user);
                if (!empty($this->appConfig['show_reset_link'])) {
                    $resetLink = 'index.php?controller=login&action=resetForm&token=' . $token;
                }
            }
        }

        return $this->forgotPage(null, $resetLink, true);
    }

    public function resetForm(): array {
        $token = (string) ($_GET['token'] ?? '');
        $isValid = $this->validResetToken($token);
        return $this->resetPage($token, $isValid);
    }

    public function resetSubmit(): array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->authentication->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            return $this->resetPage('', false, false, 'Your form session has expired. Please request a new reset link.');
        }

        $token = (string) ($_POST['token'] ?? '');
        $password = (string) ($_POST['password'] ?? '');
        $user = $this->authentication->findUserByResetToken($token);
        $isValid = $user !== null && $this->validResetToken($token);

        if (!$isValid || strlen($password) < 12) {
            return $this->resetPage($token, $isValid, false, $isValid ? 'Use a password of at least 12 characters.' : null);
        }

        $user['password'] = password_hash($password, PASSWORD_DEFAULT);
        $user['resetToken'] = null;
        $user['resetTokenExpiry'] = null;
        $this->authentication->saveUser($user);
        return $this->resetPage('', true, true);
    }

    public function logout(): ?array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->authentication->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            http_response_code(400);
            return ['template' => 'error.html.php', 'title' => 'Request error', 'variables' => ['message' => 'Your form session has expired. Please try again.']];
        }
        $this->authentication->logout();
        header('Location: index.php', true, 303);
        return null;
    }

    private function validResetToken(string $token): bool {
        $user = $this->authentication->findUserByResetToken($token);
        return $user !== null
            && !empty($user['resetTokenExpiry'])
            && strtotime((string) $user['resetTokenExpiry']) >= time();
    }

    private function loginPage(?string $errorMessage = null, string $email = ''): array {
        return [
            'template' => 'LoginForm.html.php',
            'title' => 'Log in',
            'variables' => ['errorMessage' => $errorMessage, 'email' => $email, 'csrfToken' => $this->authentication->csrfToken()],
        ];
    }

    private function forgotPage(?string $errorMessage = null, ?string $resetLink = null, bool $submitted = false): array {
        return [
            'template' => 'forgotpassword.html.php',
            'title' => 'Forgot password',
            'variables' => compact('errorMessage', 'resetLink', 'submitted') + ['csrfToken' => $this->authentication->csrfToken()],
        ];
    }

    private function resetPage(string $token, bool $isValid, bool $success = false, ?string $errorMessage = null): array {
        return [
            'template' => 'resetpassword.html.php',
            'title' => 'Reset password',
            'variables' => compact('token', 'isValid', 'success', 'errorMessage') + ['csrfToken' => $this->authentication->csrfToken()],
        ];
    }
}
