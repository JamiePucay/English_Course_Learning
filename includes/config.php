<?php
// Application configuration

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'english_lms');

// Application settings
define('APP_NAME', 'API BECI Cafe Campus International Language Academy');
define('APP_URL', 'http://localhost/english-course-learning');
define('APP_VERSION', '1.0.0');
define('APP_EMAIL', 'beci@beciedu.com');

// File upload settings
define('UPLOAD_DIR', '../content/attachments');
define('MAX_FILE_SIZE', 50000000); // 50MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'mp3', 'mp4']);

// Session settings
define('SESSION_LIFETIME', 7200); // 1 hour

// Debug mode (set to false in production)
define('DEBUG_MODE', true);

// Default pagination limit
define('DEFAULT_PAGINATION_LIMIT', 10);

// User roles
define('ROLE_ADMIN', 'admin');
define('ROLE_TEACHER', 'teacher');
define('ROLE_STUDENT', 'student');
define('ROLE_LIBRARIAN', 'librarian');

// Default password for new users (they will be prompted to change it)
define('DEFAULT_PASSWORD', 'password123');

// Time zone
date_default_timezone_set('UTC');

// Error reporting
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set session cookie parameters
session_set_cookie_params(SESSION_LIFETIME, '/', '', false, true);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}