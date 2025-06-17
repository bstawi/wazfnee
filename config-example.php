<?php
// Copy this file to includes/config.php and update the values

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'your_database_name');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');

// API Base URL - Point this to your existing API folder
define('API_BASE_URL', 'https://yourdomain.com/Wazfnee API/');

// Site configuration
define('SITE_NAME', 'Wazfnee');
define('SITE_URL', 'https://yourdomain.com');
define('ADMIN_EMAIL', 'admin@yourdomain.com');

// Language settings
define('DEFAULT_LANGUAGE', 'ar');
define('SUPPORTED_LANGUAGES', ['ar', 'en']);

// File upload settings
define('UPLOAD_PATH', 'uploads/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB

// Pagination settings
define('ITEMS_PER_PAGE', 20);

// Google Analytics (optional)
define('GA_MEASUREMENT_ID', 'GA_MEASUREMENT_ID');

// Google AdSense (optional)
define('ADSENSE_CLIENT_ID', 'ca-pub-xxxxxxxxxx');

// Social Media Links (optional)
define('FACEBOOK_URL', 'https://facebook.com/yourpage');
define('TWITTER_URL', 'https://twitter.com/yourhandle');
define('LINKEDIN_URL', 'https://linkedin.com/company/yourcompany');
define('INSTAGRAM_URL', 'https://instagram.com/yourhandle');

// Email Configuration (for contact forms)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USERNAME', 'your-email@gmail.com');
define('SMTP_PASSWORD', 'your-app-password');
define('SMTP_FROM_EMAIL', 'noreply@yourdomain.com');
define('SMTP_FROM_NAME', 'Wazfnee');

// reCAPTCHA (optional)
define('RECAPTCHA_SITE_KEY', 'your-site-key');
define('RECAPTCHA_SECRET_KEY', 'your-secret-key');
?>
```