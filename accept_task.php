<?php
session_start();
require_once 'config/config.php';
require_once 'includes/db.php';

// التحقق من تسجيل الدخول
if (!isset($_SESSION['user_id'])) {
    $_SESSION['flash_message'] = "يجب تسجيل الدخول أولاً";
    $_SESSION['flash_type'] = 'error';
    header("Location: register.php");
    exit;
}

// التحقق من وجود معرف المهمة
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['task_id'])) {
    $_SESSION['flash_message'] = "طلب غير صالح";
    $_SESSION['flash_type'] = 'error';
    header("Location: tasks.php");
    exit;
}

$task_id = (int)$_POST['task_id'];
$user_id = $_SESSION['user_id'];

try {
    // بدء المعاملة
    $pdo->beginTransaction();

    // التحقق من أن المهمة متاحة وليست مقبولة من قبل
    $stmt = $pdo->prepare("
        SELECT status 
        FROM tasks 
        WHERE id = ? 
        AND status = 'pending'
        FOR UPDATE
    ");
    $stmt->execute([$task_id]);
    $task = $stmt->fetch();

    if (!$task) {
        throw new Exception("المهمة غير متاحة أو تم قبولها من قبل");
    }

    // التحقق من أن المستخدم لم يقبل هذه المهمة من قبل
    $stmt = $pdo->prepare("
        SELECT id 
        FROM task_assignments 
        WHERE task_id = ? 
        AND user_id = ?
    ");
    $stmt->execute([$task_id, $user_id]);
    
    if ($stmt->fetch()) {
        throw new Exception("لقد قمت بقبول هذه المهمة مسبقاً");
    }

    // إضافة قبول المهمة
    $stmt = $pdo->prepare("
        INSERT INTO task_assignments (task_id, user_id, status) 
        VALUES (?, ?, 'active')
    ");
    $stmt->execute([$task_id, $user_id]);

    // تحديث حالة المهمة إلى مقبولة
    $stmt = $pdo->prepare("
        UPDATE tasks 
        SET status = 'accepted' 
        WHERE id = ?
    ");
    $stmt->execute([$task_id]);

    // إتمام المعاملة
    $pdo->commit();

    $_SESSION['flash_message'] = "تم قبول المهمة بنجاح";
    $_SESSION['flash_type'] = 'success';

} catch (Exception $e) {
    // التراجع عن المعاملة في حالة حدوث خطأ
    $pdo->rollBack();
    
    error_log($e->getMessage());
    $_SESSION['flash_message'] = $e->getMessage();
    $_SESSION['flash_type'] = 'error';
}

// العودة إلى صفحة المهام
header("Location: tasks.php");
exit;
