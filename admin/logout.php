<?php
session_start();

// حذف جميع متغيرات الجلسة
$_SESSION = array();

// حذف ملف الجلسة
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time()-3600, '/');
}

// إنهاء الجلسة
session_destroy();

// إعادة التوجيه إلى صفحة تسجيل الدخول مع رسالة نجاح
session_start();
$_SESSION['flash_message'] = "تم تسجيل الخروج بنجاح";
$_SESSION['flash_type'] = 'success';

header("Location: login.php");
exit;
