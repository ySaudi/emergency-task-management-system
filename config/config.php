<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'emergency_plan_db');

// Application configuration
define('SITE_NAME', 'هيئة الهلال الأحمر السعودي فرع الشرقية');
define('SITE_SUBTITLE', 'خطة الطوارئ');
define('COORDINATOR', 'سلطان الشمراني');
define('DESIGNER', 'adel aldhfeeri');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');
