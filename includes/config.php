<?php

define('SITE_TITLE', 'Static Blog');
define('SITE_URL', 'http://localhost:8000');
define('POSTS_DIR', __DIR__ . '/../posts/');
define('POSTS_EXT', '.html');
// It is highly recommended to set this password via an environment variable for security.
// For example, in your web server configuration (Apache, Nginx) or .env file.
// If not set, a default (for development only) will be used, but this is INSECURE for production.
$adminPassword = getenv('ADMIN_PASSWORD') ?: 'admin123'; // Fallback for development, CHANGE THIS!
define('ADMIN_PASSWORD', password_hash($adminPassword, PASSWORD_DEFAULT));
define('TIMEZONE', 'Asia/Jakarta');

date_default_timezone_set(TIMEZONE);

?>