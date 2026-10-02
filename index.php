<?php

declare(strict_types=1);

function loadTemplate(string $templateFileName, array $variables = []): string {
    extract($variables, EXTR_SKIP);
    ob_start();
    require __DIR__ . '/templates/' . $templateFileName;
    return (string) ob_get_clean();
}

function sendSecurityHeaders(): void {
    header('Content-Type: text/html; charset=UTF-8');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header('Permissions-Policy: geolocation=(), microphone=(), camera=()');
    header("Content-Security-Policy: default-src 'self'; style-src 'self' 'unsafe-inline' https://www.w3schools.com https://fonts.googleapis.com; font-src https://fonts.gstatic.com; img-src 'self' data:; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
}

sendSecurityHeaders();

try {
    require_once __DIR__ . '/includes/DatabaseConnection.php';
    require_once __DIR__ . '/classes/DatabaseTable.php';
    require_once __DIR__ . '/classes/Authentication.php';
    require_once __DIR__ . '/controllers/FilmController.php';
    require_once __DIR__ . '/controllers/ReviewerController.php';
    require_once __DIR__ . '/controllers/Login.php';

    $filmsTable = new DatabaseTable($pdo, 'film', 'id', ['id', 'title', 'review', 'reviewer_id', 'reviewdate', 'rating']);
    $reviewersTable = new DatabaseTable($pdo, 'reviewer', 'id', ['id', 'name', 'email', 'password', 'resetToken', 'resetTokenExpiry', 'isBanned']);
    $authentication = new Authentication($reviewersTable, 'email', 'password');

    $controllerName = $_GET['controller'] ?? 'film';
    $action = $_GET['action'] ?? 'home';
    $controllers = [
        'film' => [new FilmController($filmsTable, $reviewersTable, $authentication), ['home', 'list', 'edit', 'delete']],
        'reviewer' => [new ReviewerController($reviewersTable, $authentication), ['registrationForm', 'registrationFormSubmit', 'success']],
        'login' => [new Login($authentication, $appConfig), ['login', 'loginSubmit', 'forgotForm', 'forgotSubmit', 'resetForm', 'resetSubmit', 'logout']],
    ];

    if (!is_string($controllerName) || !is_string($action) || !isset($controllers[$controllerName]) || !in_array($action, $controllers[$controllerName][1], true)) {
        throw new RuntimeException('Invalid route.');
    }

    $page = $controllers[$controllerName][0]->{$action}();
    if ($page === null) {
        exit;
    }

    $title = $page['title'];
    $output = loadTemplate($page['template'], $page['variables'] ?? []);
    $isLoggedIn = $authentication->isLoggedIn();
    $csrfToken = $authentication->csrfToken();
} catch (Throwable $exception) {
    error_log($exception->getMessage());
    http_response_code(500);
    $title = 'Application error';
    $output = loadTemplate('error.html.php', ['message' => 'Something went wrong. Please try again later.']);
    $isLoggedIn = false;
    $csrfToken = '';
}

require __DIR__ . '/templates/layout.html.php';
