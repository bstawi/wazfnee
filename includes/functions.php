<?php
require_once 'config.php';
require_once 'translations.php';

// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) return null;
    
    $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM users WHERE userId = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

function getCurrentAdmin() {
    if (!isAdmin()) return null;
    
    $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM admins WHERE adminId = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    return $stmt->fetch();
}

function login($email, $password) {
    $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM users WHERE emailAddress = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['userId'];
        $_SESSION['user_name'] = $user['fullName'];
        return true;
    }
    return false;
}

function adminLogin($email, $password) {
    $stmt = $GLOBALS['pdo']->prepare("SELECT * FROM admins WHERE emailAddress = ?");
    $stmt->execute([$email]);
    $admin = $stmt->fetch();
    
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_id'] = $admin['adminId'];
        $_SESSION['admin_name'] = $admin['fullName'];
        return true;
    }
    return false;
}

function logout() {
    session_destroy();
    header("Location: index.php");
    exit;
}

// Language functions
function getCurrentLanguage() {
    return $_SESSION['language'] ?? DEFAULT_LANGUAGE;
}

function t($key) {
    global $translations;
    $lang = getCurrentLanguage();
    return $translations[$lang][$key] ?? $key;
}

// API functions
function makeApiRequest($endpoint, $method = 'GET', $data = null) {
    $url = API_BASE_URL . $endpoint;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    
    if ($method === 'POST') {
        curl_setopt($ch, CURLOPT_POST, true);
        if ($data) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
    }
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode === 200) {
        return json_decode($response, true);
    }
    
    return null;
}

// Data fetching functions
function getSliders() {
    $response = makeApiRequest('get_sliders.php');
    return $response['sliders'] ?? [];
}

function getCategories($limit = null) {
    $endpoint = 'get_categories.php';
    if ($limit) {
        $endpoint .= '?limit=' . $limit . '&isPaginated=false';
    }
    $response = makeApiRequest($endpoint);
    return $response['categories'] ?? [];
}

function getJobs($params = []) {
    $endpoint = 'get_jobs.php';
    if (!empty($params)) {
        $endpoint .= '?' . http_build_query($params);
    }
    $response = makeApiRequest($endpoint);
    return $response['jobs'] ?? [];
}

function getJob($id) {
    $response = makeApiRequest("get_job_with_id.php?jobId=$id");
    return $response['job'] ?? null;
}

function getSeekers($params = []) {
    $endpoint = 'get_seekers.php';
    if (!empty($params)) {
        $endpoint .= '?' . http_build_query($params);
    }
    $response = makeApiRequest($endpoint);
    return $response['seekers'] ?? [];
}

function getSeeker($id) {
    $response = makeApiRequest("get_seeker_with_id.php?seekerId=$id");
    return $response['seeker'] ?? null;
}

function getArticles($params = []) {
    $endpoint = 'get_articles.php';
    if (!empty($params)) {
        $endpoint .= '?' . http_build_query($params);
    }
    $response = makeApiRequest($endpoint);
    return $response['articles'] ?? [];
}

function getArticle($id) {
    $response = makeApiRequest("get_article_with_id.php?articleId=$id");
    return $response['article'] ?? null;
}

function getConfigs() {
    $response = makeApiRequest('get_configs.php');
    $configs = $response['configs'] ?? [];
    
    $configArray = [];
    foreach ($configs as $config) {
        $configArray[$config['label']] = $config['value'];
    }
    return $configArray;
}

function getConfig($key) {
    static $configs = null;
    if ($configs === null) {
        $configs = getConfigs();
    }
    return $configs[$key] ?? '';
}

// Utility functions
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

function truncateText($text, $length = 100) {
    if (strlen($text) <= $length) {
        return $text;
    }
    return substr($text, 0, $length) . '...';
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    
    if ($time < 60) return t('just_now');
    if ($time < 3600) return floor($time/60) . ' ' . t('minutes_ago');
    if ($time < 86400) return floor($time/3600) . ' ' . t('hours_ago');
    if ($time < 2592000) return floor($time/86400) . ' ' . t('days_ago');
    if ($time < 31536000) return floor($time/2592000) . ' ' . t('months_ago');
    return floor($time/31536000) . ' ' . t('years_ago');
}

function formatSalary($amount, $currency) {
    return number_format($amount) . ' ' . $currency;
}

function generateSlug($text) {
    $text = preg_replace('/[^a-zA-Z0-9\s]/', '', $text);
    $text = preg_replace('/\s+/', '-', trim($text));
    return strtolower($text);
}

function uploadFile($file, $directory = 'uploads/') {
    if (!isset($file['tmp_name']) || empty($file['tmp_name'])) {
        return false;
    }
    
    $uploadDir = $directory;
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileName = time() . '_' . basename($file['name']);
    $targetPath = $uploadDir . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $fileName;
    }
    
    return false;
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function showAlert($message, $type = 'info') {
    $_SESSION['alert'] = [
        'message' => $message,
        'type' => $type
    ];
}

function getAlert() {
    if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        unset($_SESSION['alert']);
        return $alert;
    }
    return null;
}

// Pagination function
function paginate($total, $page, $perPage = ITEMS_PER_PAGE) {
    $totalPages = ceil($total / $perPage);
    $page = max(1, min($page, $totalPages));
    $offset = ($page - 1) * $perPage;
    
    return [
        'total' => $total,
        'page' => $page,
        'perPage' => $perPage,
        'totalPages' => $totalPages,
        'offset' => $offset,
        'hasNext' => $page < $totalPages,
        'hasPrev' => $page > 1
    ];
}

// SEO functions
function generateMetaTags($title, $description = '', $keywords = '', $image = '') {
    $siteName = SITE_NAME;
    $siteUrl = SITE_URL;
    
    echo "<title>$title - $siteName</title>\n";
    echo "<meta name='description' content='$description'>\n";
    echo "<meta name='keywords' content='$keywords'>\n";
    
    // Open Graph tags
    echo "<meta property='og:title' content='$title'>\n";
    echo "<meta property='og:description' content='$description'>\n";
    echo "<meta property='og:type' content='website'>\n";
    echo "<meta property='og:url' content='$siteUrl'>\n";
    if ($image) {
        echo "<meta property='og:image' content='$image'>\n";
    }
    
    // Twitter Card tags
    echo "<meta name='twitter:card' content='summary_large_image'>\n";
    echo "<meta name='twitter:title' content='$title'>\n";
    echo "<meta name='twitter:description' content='$description'>\n";
    if ($image) {
        echo "<meta name='twitter:image' content='$image'>\n";
    }
}

// Security functions
function csrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect('login.php');
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        redirect('admin/login.php');
    }
}
?>