<?php
session_start();
require_once '../config/config.php';
require_once '../includes/db.php';

// إذا كان المدير مسجل دخوله بالفعل، قم بتحويله إلى لوحة التحكم
if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
    header("Location: index.php");
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username']);
    $password = $_POST['password'];

    // في بيئة الإنتاج، يجب استخدام نظام مصادقة أكثر أماناً
    // هذا مجرد مثال بسيط للتوضيح
    if ($username === 'admin' && $password === 'admin123') {
        $_SESSION['is_admin'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['flash_message'] = "مرحباً بك في لوحة التحكم";
        $_SESSION['flash_type'] = 'success';
        header("Location: index.php");
        exit;
    } else {
        $errors[] = "اسم المستخدم أو كلمة المرور غير صحيحة";
    }
}

require_once '../includes/header.php';
?>

<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full">
        <div class="text-center mb-8">
            <h2 class="text-3xl font-bold text-primary">
                تسجيل دخول المدير
            </h2>
            <p class="mt-2 text-sm text-gray-600">
                لوحة تحكم هيئة الهلال الأحمر السعودي - فرع الشرقية
            </p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <?php foreach ($errors as $error): ?>
                    <p class="flex items-center">
                        <i class="fas fa-exclamation-circle ml-2"></i>
                        <?php echo $error; ?>
                    </p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <div class="bg-white py-8 px-4 shadow-lg rounded-lg sm:px-10">
            <form class="space-y-6" method="POST">
                <!-- اسم المستخدم -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="username">
                        اسم المستخدم
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-gray-400"></i>
                        </div>
                        <input type="text"
                               id="username"
                               name="username"
                               required
                               class="block w-full pr-10 sm:text-sm border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                               placeholder="أدخل اسم المستخدم">
                    </div>
                </div>

                <!-- كلمة المرور -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2" for="password">
                        كلمة المرور
                    </label>
                    <div class="relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-gray-400"></i>
                        </div>
                        <input type="password"
                               id="password"
                               name="password"
                               required
                               class="block w-full pr-10 sm:text-sm border-gray-300 rounded-md focus:ring-primary focus:border-primary"
                               placeholder="أدخل كلمة المرور">
                    </div>
                </div>

                <!-- زر تسجيل الدخول -->
                <div>
                    <button type="submit"
                            class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                        <i class="fas fa-sign-in-alt ml-2"></i>
                        تسجيل الدخول
                    </button>
                </div>
            </form>

            <!-- روابط إضافية -->
            <div class="mt-6">
                <div class="relative">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">
                            روابط مفيدة
                        </span>
                    </div>
                </div>

                <div class="mt-6 grid grid-cols-2 gap-3">
                    <a href="../" 
                       class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <i class="fas fa-home ml-2"></i>
                        الرئيسية
                    </a>
                    <a href="mailto:support@example.com"
                       class="w-full inline-flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                        <i class="fas fa-envelope ml-2"></i>
                        الدعم الفني
                    </a>
                </div>
            </div>
        </div>

        <!-- ملاحظة الأمان -->
        <div class="mt-4 text-center">
            <p class="text-xs text-gray-500">
                <i class="fas fa-shield-alt ml-1"></i>
                جميع البيانات مشفرة ومحمية
            </p>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
