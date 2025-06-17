<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'u492790436_Wazfnee');
define('DB_USER', 'u492790436_wazfneeDB');
define('DB_PASS', 'GUi@Pa7&');

// API Base URL
define('API_BASE_URL', 'http://localhost/api/');

// Site configuration
define('SITE_NAME', 'Wazfnee');
define('SITE_URL', 'http://localhost');
define('ADMIN_EMAIL', 'admin@wazfnee.com');

// Language settings
define('DEFAULT_LANGUAGE', 'ar');
define('SUPPORTED_LANGUAGES', ['ar', 'en']);

// File upload settings
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Pagination settings
define('ITEMS_PER_PAGE', 20);

// Session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 for HTTPS

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Riyadh');

// Database connection
try {
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Set default language
if (!isset($_SESSION['language'])) {
    $_SESSION['language'] = DEFAULT_LANGUAGE;
}

// Handle language switching
if (isset($_GET['lang']) && in_array($_GET['lang'], SUPPORTED_LANGUAGES)) {
    $_SESSION['language'] = $_GET['lang'];
    // Redirect to remove lang parameter from URL
    $redirect_url = strtok($_SERVER['REQUEST_URI'], '?');
    if (!empty($_GET)) {
        $params = $_GET;
        unset($params['lang']);
        if (!empty($params)) {
            $redirect_url .= '?' . http_build_query($params);
        }
    }
    header("Location: $redirect_url");
    exit;
}
?>