# Film Reviews

A small PHP application where registered reviewers can create, edit, delete, and browse film reviews. It is intended as a local-learning and portfolio project, not a production deployment.

## Requirements

- PHP 8.1 or newer (the project uses typed properties and union types)
- MySQL 8+ or a recent MariaDB release
- MAMP, or another PHP/MySQL local development environment

The interface uses W3.CSS and the Poppins font from their public CDNs. No package manager or other third-party PHP library is required.

## Local setup

1. In phpMyAdmin or the MySQL command line, import the clean starter database:

   ```sh
   mysql -u YOUR_DATABASE_USER -p < "Films Data.sql"
   ```

2. Copy the local configuration template and edit the copy only:

   ```sh
   cp includes/config.example.php includes/config.php
   ```

   Set your MySQL/MAMP host, optional port, username, and password in `includes/config.php`. The database name must remain `films`. This local file is ignored by Git.

3. Place this directory under MAMP's document root (or configure a virtual host), start PHP and MySQL, then open `index.php` in the browser.

4. Register a new account before logging in. The seed reviewer is fictional and intentionally has no password.

## Features and safeguards

- Registration, login, logout, and owner-only review editing/deletion
- Passwords are hashed with PHP's `password_hash`; sessions retain only a user ID and email, never a password hash
- Password resets use cryptographically random one-time tokens, storing only a SHA-256 token hash with a one-hour expiry
- CSRF validation on all state-changing forms
- Server-side validation for account fields, review fields, IDs, and ratings
- Prepared statements plus fixed table/column allow-lists
- Escaped HTML output and baseline security headers

## Password reset limitation

This project has no email delivery service. By default, requesting a reset deliberately does not reveal a reset URL. For local development only, set `show_reset_link` to `true` in the ignored `includes/config.php`; never enable that setting on a public site. A production application needs a real mailer, rate limiting, HTTPS, and account-recovery monitoring.

## Project layout

- `index.php` — front controller and security headers
- `classes/` — authentication and database-table abstraction
- `controllers/` — film, registration, login, and reset workflows
- `templates/` — escaped HTML views
- `includes/config.example.php` — tracked configuration template
- `Films Data.sql` — sanitized starter schema and fictional demo data

## Before publishing

Do not add `includes/config.php`, `.env` files, database exports containing live accounts, or logs to Git. The included `.gitignore` excludes those files. This directory was supplied without a `.git` repository, so its prior Git history could not be reviewed; inspect any repository history separately before publishing.
