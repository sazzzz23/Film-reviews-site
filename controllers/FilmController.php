<?php

declare(strict_types=1);

class FilmController {
    public function __construct(
        private DatabaseTable $filmsTable,
        private DatabaseTable $reviewersTable,
        private Authentication $authentication
    ) {
    }

    public function list(): array {
        $limit = 5;
        $page = filter_input(INPUT_GET, 'page', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) ?: 1;
        $totalFilms = $this->filmsTable->total();
        $totalPages = max(1, (int) ceil($totalFilms / $limit));
        $page = min($page, $totalPages);
        $films = [];

        foreach ($this->filmsTable->findAll($limit, ($page - 1) * $limit) as $film) {
            $reviewer = $this->reviewersTable->findById((int) $film['reviewer_id']);
            $films[] = [
                'id' => (int) $film['id'],
                'title' => $film['title'],
                'review' => $film['review'],
                'reviewdate' => $film['reviewdate'] ?? '',
                'rating' => $film['rating'] ?? null,
                'name' => $reviewer['name'] ?? '',
                'email' => $reviewer['email'] ?? '',
                'reviewerId' => $reviewer['id'] ?? null,
            ];
        }

        $user = $this->authentication->getUser();
        return [
            'template' => 'films.html.php',
            'title' => 'Film list',
            'variables' => [
                'totalFilms' => $totalFilms,
                'films' => $films,
                'userId' => $user['id'] ?? null,
                'page' => $page,
                'totalPages' => $totalPages,
                'csrfToken' => $this->authentication->csrfToken(),
            ],
        ];
    }

    public function home(): array {
        return ['template' => 'home.html.php', 'title' => 'Internet Film Reviews'];
    }

    public function delete(): ?array {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$this->authentication->validateCsrfToken($_POST['csrf_token'] ?? null)) {
            return $this->error('Your form session has expired. Please try again.');
        }
        $user = $this->authentication->getUser();
        $id = filter_var($_POST['id'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($user === null || $id === false) {
            return $this->error('You are not allowed to delete this review.', 403);
        }

        $film = $this->filmsTable->findById($id);
        if ($film === false || (int) $film['reviewer_id'] !== (int) $user['id']) {
            return $this->error('You are not allowed to delete this review.', 403);
        }
        $this->filmsTable->delete('id', $id);
        header('Location: index.php?controller=film&action=list', true, 303);
        return null;
    }

    public function edit(): ?array {
        $user = $this->authentication->getUser();
        if ($user === null) {
            return $this->error('Please log in to manage reviews.', 403);
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!$this->authentication->validateCsrfToken($_POST['csrf_token'] ?? null)) {
                return $this->error('Your form session has expired. Please try again.');
            }
            return $this->saveFilm($user);
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        $film = $id ? $this->filmsTable->findById($id) : null;
        if ($film !== null && $film !== false && (int) $film['reviewer_id'] !== (int) $user['id']) {
            return $this->error('You may only edit reviews that you posted.', 403);
        }

        return $this->editPage($film ?: null, $user['id']);
    }

    private function saveFilm(array $user): ?array {
        $submitted = $_POST['film'] ?? [];
        $film = is_array($submitted) ? $submitted : [];
        $id = !isset($film['id']) || $film['id'] === '' ? null : filter_var($film['id'], FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
        if ($id === false) {
            return $this->editPage($film, $user['id'], ['Invalid review identifier.']);
        }
        if ($id !== null) {
            $existing = $this->filmsTable->findById($id);
            if ($existing === false || (int) $existing['reviewer_id'] !== (int) $user['id']) {
                return $this->error('You may only edit reviews that you posted.', 403);
            }
        }

        $title = trim((string) ($film['title'] ?? ''));
        $review = trim((string) ($film['review'] ?? ''));
        $rating = filter_var($film['rating'] ?? null, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 5]]);
        $errors = [];
        if ($title === '' || mb_strlen($title) > 255) {
            $errors[] = 'Enter a film title of up to 255 characters.';
        }
        if ($review === '' || mb_strlen($review) > 10000) {
            $errors[] = 'Enter a review of up to 10,000 characters.';
        }
        if ($rating === false) {
            $errors[] = 'Select a rating from 1 to 5.';
        }
        if ($errors !== []) {
            return $this->editPage(['id' => $id ?: '', 'title' => $title, 'review' => $review, 'rating' => $film['rating'] ?? ''], $user['id'], $errors);
        }

        $this->filmsTable->save([
            'id' => $id,
            'title' => $title,
            'review' => $review,
            'reviewdate' => (new DateTimeImmutable())->format('Y-m-d'),
            'rating' => $rating,
            'reviewer_id' => (int) $user['id'],
        ]);
        header('Location: index.php?controller=film&action=list', true, 303);
        return null;
    }

    private function editPage(?array $film, int $userId, array $errors = []): array {
        return [
            'template' => 'editreview.html.php',
            'title' => 'Edit review',
            'variables' => [
                'film' => $film,
                'userId' => $userId,
                'errors' => $errors,
                'csrfToken' => $this->authentication->csrfToken(),
            ],
        ];
    }

    private function error(string $message, int $status = 400): array {
        http_response_code($status);
        return ['template' => 'error.html.php', 'title' => 'Request error', 'variables' => ['message' => $message]];
    }
}
