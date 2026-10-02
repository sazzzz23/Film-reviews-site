<?php

declare(strict_types=1);

class ReviewerController {
    public function __construct(private DatabaseTable $reviewersTable, private Authentication $authentication) {
    }

    public function registrationForm(): array {
        return [
            'template' => 'Register.html.php',
            'title' => 'Register an account',
            'variables' => ['csrfToken' => $this->authentication->csrfToken()],
        ];
    }

    public function success(): array {
        return ['template' => 'RegisterSuccess.html.php', 'title' => 'Registration successful'];
    }

    public function registrationFormSubmit(): ?array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->authentication->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            return $this->error('Your form session has expired. Please try again.');
        }

        $submitted = $_POST['reviewer'] ?? [];
        $reviewer = is_array($submitted) ? $submitted : [];
        $name = trim((string) ($reviewer['name'] ?? ''));
        $email = strtolower(trim((string) ($reviewer['email'] ?? '')));
        $password = (string) ($reviewer['password'] ?? '');
        $errors = [];

        if ($name === '' || mb_strlen($name) > 255) {
            $errors[] = 'Enter a name of up to 255 characters.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 255) {
            $errors[] = 'Enter a valid email address.';
        } elseif ($this->reviewersTable->find('email', $email) !== []) {
            $errors[] = 'That email address is already registered.';
        }
        if (strlen($password) < 12) {
            $errors[] = 'Use a password of at least 12 characters.';
        }

        if ($errors !== []) {
            return [
                'template' => 'Register.html.php',
                'title' => 'Register an account',
                'variables' => [
                    'errors' => $errors,
                    'reviewer' => ['name' => $name, 'email' => $email],
                    'csrfToken' => $this->authentication->csrfToken(),
                ],
            ];
        }

        $this->reviewersTable->save([
            'name' => $name,
            'email' => $email,
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'isBanned' => 0,
        ]);
        header('Location: index.php?controller=reviewer&action=success', true, 303);
        return null;
    }

    private function error(string $message): array {
        http_response_code(400);
        return ['template' => 'error.html.php', 'title' => 'Request error', 'variables' => ['message' => $message]];
    }
}
