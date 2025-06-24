<?php
session_start();
require_once '../config/config.php';
require_once '../includes/db.php';

// التحقق من صلاحيات المدير
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // تنظيف وفحص المدخلات
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $location = sanitizeInput($_POST['location']);
    
    // التحقق من البيانات
    if (empty($title) || !isArabic($title)) {
        $errors[] = "يرجى إدخال عنوان المهمة باللغة العربية";
    }
    
    if (empty($description) || !isArabic($description)) {
        $errors[] = "يرجى إدخال وصف المهمة باللغة العربية";
    }
    
    if (empty($location) || !isArabic($location)) {
        $errors[] = "يرجى إدخال موقع المهمة باللغة العربية";
    }

    // إذا لم تكن هناك أخطاء، قم بإضافة المهمة
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO tasks (title, description, location, status) 
                VALUES (?, ?, ?, 'pending')
            ");
            
            $stmt->execute([$title, $description, $location]);
            
            $_SESSION['flash_message'] = "تم إضافة المهمة بنجاح";
            $_SESSION['flash_type'] = 'success';
            
            // إعادة التوجيه إلى صفحة إدارة المهام
            header("Location: manage_tasks.php");
            exit;
            
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $errors[] = "حدث خطأ أثناء إضافة المهمة";
        }
    }
}

require_once '../includes/header.php';
?>

<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-primary">إضافة مهمة جديدة</h1>
        <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-full inline-flex items-center">
            <i class="fas fa-arrow-right ml-2"></i>
            عودة
        </a>
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

    <div class="bg-white rounded-lg shadow-md p-6">
        <form method="POST" class="space-y-6">
            <!-- عنوان المهمة -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="title">
                    عنوان المهمة <span class="text-red-500">*</span>
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                       id="title"
                       name="title"
                       type="text"
                       required
                       value="<?php echo $_POST['title'] ?? ''; ?>"
                       placeholder="أدخل عنوان المهمة">
            </div>

            <!-- وصف المهمة -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="description">
                    وصف المهمة <span class="text-red-500">*</span>
                </label>
                <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                          id="description"
                          name="description"
                          rows="5"
                          required
                          placeholder="أدخل وصفاً تفصيلياً للمهمة"><?php echo $_POST['description'] ?? ''; ?></textarea>
            </div>

            <!-- موقع المهمة -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="location">
                    موقع المهمة <span class="text-red-500">*</span>
                </label>
                <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                       id="location"
                       name="location"
                       type="text"
                       required
                       value="<?php echo $_POST['location'] ?? ''; ?>"
                       placeholder="أدخل موقع المهمة">
            </div>

            <!-- أزرار التحكم -->
            <div class="flex items-center justify-between pt-4">
                <button type="reset" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-full">
                    <i class="fas fa-redo ml-2"></i>
                    إعادة تعيين
                </button>
                <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-6 py-2 rounded-full">
                    <i class="fas fa-plus ml-2"></i>
                    إضافة المهمة
                </button>
            </div>
        </form>
    </div>

    <!-- إرشادات إضافة المهمة -->
    <div class="bg-blue-50 rounded-lg p-6 mt-8">
        <h3 class="text-lg font-bold text-primary mb-4">
            <i class="fas fa-info-circle ml-2"></i>
            إرشادات إضافة المهمة
        </h3>
        <ul class="list-disc list-inside space-y-2 text-gray-600">
            <li>يجب إدخال جميع البيانات باللغة العربية</li>
            <li>تأكد من كتابة وصف واضح وتفصيلي للمهمة</li>
            <li>حدد الموقع بشكل دقيق لتسهيل الوصول</li>
            <li>يمكنك تعديل المهمة لاحقاً من صفحة إدارة المهام</li>
        </ul>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
