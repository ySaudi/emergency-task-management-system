<?php
session_start();
require_once '../config/config.php';
require_once '../includes/db.php';

// TODO: يجب إضافة نظام مصادقة للمدير
// مؤقتاً نستخدم متغير الجلسة للتحقق من صلاحية المدير
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

// إحصائيات عامة
try {
    // عدد المسعفين المسجلين
    $stmt = $pdo->query("SELECT COUNT(*) as total_users FROM users");
    $totalUsers = $stmt->fetch()['total_users'];

    // عدد المهام النشطة
    $stmt = $pdo->query("SELECT COUNT(*) as active_tasks FROM tasks WHERE status = 'pending'");
    $activeTasks = $stmt->fetch()['active_tasks'];

    // عدد المهام المكتملة
    $stmt = $pdo->query("SELECT COUNT(*) as completed_tasks FROM tasks WHERE status = 'completed'");
    $completedTasks = $stmt->fetch()['completed_tasks'];

    // أفضل 5 مسعفين (حسب النقاط)
    $stmt = $pdo->query("
        SELECT employee_number, points 
        FROM users 
        ORDER BY points DESC 
        LIMIT 5
    ");
    $topUsers = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log($e->getMessage());
    $_SESSION['flash_message'] = "حدث خطأ أثناء جلب البيانات";
    $_SESSION['flash_type'] = 'error';
}

require_once '../includes/header.php';
?>

<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-primary">لوحة التحكم</h1>
        <div class="space-x-4 space-x-reverse">
            <a href="create_task.php" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-full inline-flex items-center">
                <i class="fas fa-plus ml-2"></i>
                إضافة مهمة جديدة
            </a>
            <a href="manage_tasks.php" class="bg-secondary hover:bg-secondary/90 text-white px-4 py-2 rounded-full inline-flex items-center">
                <i class="fas fa-tasks ml-2"></i>
                إدارة المهام
            </a>
        </div>
    </div>

    <!-- الإحصائيات -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- عدد المسعفين -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">إجمالي المسعفين</p>
                    <h3 class="text-3xl font-bold text-primary"><?php echo $totalUsers; ?></h3>
                </div>
                <div class="bg-blue-100 p-3 rounded-full">
                    <i class="fas fa-users text-primary text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- المهام النشطة -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">المهام النشطة</p>
                    <h3 class="text-3xl font-bold text-green-600"><?php echo $activeTasks; ?></h3>
                </div>
                <div class="bg-green-100 p-3 rounded-full">
                    <i class="fas fa-clock text-green-600 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- المهام المكتملة -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-500 text-sm">المهام المكتملة</p>
                    <h3 class="text-3xl font-bold text-purple-600"><?php echo $completedTasks; ?></h3>
                </div>
                <div class="bg-purple-100 p-3 rounded-full">
                    <i class="fas fa-check-circle text-purple-600 text-2xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- أفضل المسعفين -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 class="text-xl font-bold mb-4 text-primary">
            <i class="fas fa-trophy ml-2"></i>
            أفضل المسعفين
        </h2>
        <?php if (empty($topUsers)): ?>
            <p class="text-gray-500 text-center">لا يوجد مسعفين مسجلين بعد</p>
        <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">الترتيب</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">رقم الموظف</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">النقاط</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($topUsers as $index => $user): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $index + 1; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($user['employee_number']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php echo $user['points']; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <!-- روابط سريعة -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <a href="reports.php" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="bg-blue-100 p-3 rounded-full ml-4">
                    <i class="fas fa-chart-bar text-primary text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-primary">التقارير والإحصائيات</h3>
                    <p class="text-gray-500 text-sm">عرض تقارير مفصلة عن أداء المسعفين والمهام</p>
                </div>
            </div>
        </a>
        <a href="settings.php" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
            <div class="flex items-center">
                <div class="bg-gray-100 p-3 rounded-full ml-4">
                    <i class="fas fa-cog text-gray-600 text-2xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-lg text-gray-600">إعدادات النظام</h3>
                    <p class="text-gray-500 text-sm">تعديل إعدادات النظام والصلاحيات</p>
                </div>
            </div>
        </a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
