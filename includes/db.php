<?php
require_once __DIR__ . '/../config/config.php';

try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
    ]);
} catch (PDOException $e) {
    error_log("Database Connection Error: " . $e->getMessage());
    die("عذراً، حدث خطأ في الاتصال بقاعدة البيانات. الرجاء المحاولة لاحقاً.");
}

// Helper function to validate Arabic text
function isArabic($text) {
    return preg_match('/[\x{0600}-\x{06FF}]/u', $text);
}

// Helper function to sanitize input
function sanitizeInput($input) {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

// Helper function to validate Saudi phone number
function isValidSaudiPhone($phone) {
    return preg_match('/^(05)[0-9]{8}$/', $phone);
}

// Helper function to validate employee number
function isValidEmployeeNumber($number) {
    return preg_match('/^\d{3,7}$/', $number);
}
