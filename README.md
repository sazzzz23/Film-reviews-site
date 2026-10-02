Film Reviews

A PHP and MySQL web application for creating, editing, deleting, and browsing film reviews.

Built as a university project and refined as a software development portfolio project.

Features

* User registration and login
* Create, edit, and delete film reviews
* Owner-only review management
* MySQL database integration
* Password hashing and session authentication
* CSRF protection and server-side validation
* Prepared SQL statements and escaped output
* Password reset functionality

Technologies

* PHP 8.1+
* MySQL 8+
* HTML & CSS
* W3.CSS
* MAMP
* Git & GitHub

Project Structure

week9-films/
├── classes/
├── controllers/
├── includes/
├── templates/
├── Films Data.sql
├── film.css
├── films.php
├── editfilm.php
├── deletefilm.php
└── index.php

Setup

1. Clone the repository:

git clone https://github.com/sazzzz23/week9-films.git

2. Import Films Data.sql into a MySQL database named films.
3. Copy includes/config.example.php to includes/config.php and add your local database credentials.
4. Place the project in your MAMP htdocs folder, start Apache and MySQL, then open:

http://localhost/week9-films/

includes/config.php is excluded from Git and should never contain credentials that are committed to the repository.

Future Improvements

Potential improvements include:

* Automated unit and integration tests
* Improved responsive UI
* Film search and filtering
* Pagination
* User profile management
* Email-based password recovery
* API integration with a film database
* Production deployment
* CI/CD using GitHub Actions

Previews of the site

[2026-10-02_20-04-44.tiff](https://github.com/user-attachments/files/32977938/2026-10-02_20-04-44.tiff)
[2026-10-02_20-08-52.tiff](https://github.com/user-attachments/files/32977991/2026-10-02_20-08-52.tiff)
[2026-10-02_20-08-19.tiff](https://github.com/user-attachments/files/32977984/2026-10-02_20-08-19.tiff)
[2026-10-02_20-07-11.tiff](https://github.com/user-attachments/files/32977975/2026-10-02_20-07-11.tiff)


Limitations

This is a university/portfolio project rather than a production application.

Some production features are intentionally outside the scope of the project, including:

* Email delivery for password resets
* Production hosting
* Automated deployment
* Comprehensive automated test coverage
* Production monitoring and logging
* Rate limiting
* Full production-grade account recovery

Author

Sabir Uddin
GitHub: @sazzzz23
