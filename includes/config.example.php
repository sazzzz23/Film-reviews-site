<?php

// Copy this file to config.php and replace the placeholder values. config.php is ignored by Git.
return [
    'database' => [
        'host' => 'localhost',
        // Set this to your MAMP MySQL port when needed (for example, 8889).
        'port' => '',
        'database' => 'films',
        'username' => 'your_database_user',
        'password' => 'your_database_password',
    ],
    'app' => [
        // Keep false for normal use. True exposes a one-time reset URL for local development only.
        'show_reset_link' => false,
    ],
];
