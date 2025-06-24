<?php
session_start();
require_once 'config/config.php';
require_once 'includes/db.php';

// جلب إحصائيات عامة
try {
    // عدد المسعفين
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM users");
    $totalVolunteers = $stmt->fetch()['total'];

    // عدد المهام النشطة
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tasks WHERE status = 'pending'");
    $activeTasks = $stmt->fetch()['total'];

    // عدد المهام المكتملة
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM tasks WHERE status = 'completed'");
    $completedTasks = $stmt->fetch()['total'];

} catch (PDOException $e) {
    error_log($e->getMessage());
}

require_once 'includes/header.php';
?>

<!-- Hero Section -->
<div class="relative bg-gradient-to-r from-primary to-secondary text-white py-20">
    <div class="container mx-auto px-4">
        <div class="max-w-3xl mx-auto text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-6">
                هيئة الهلال الأحمر السعودي - فرع الشرقية
            </h1>
            <p class="text-xl md:text-2xl mb-8">
                نظام إدارة المهام الطارئة للمسعفين المتطوعين
            </p>
            <?php if (!isset($_SESSION['user_id'])): ?>
                <a href="register.php" class="inline-block bg-white text-primary font-bold py-3 px-8 rounded-full hover:bg-gray-100 transition-colors">
                    <i class="fas fa-user-plus ml-2"></i>
                    سجل كمسعف الآن
                </a>
            <?php else: ?>
                <a href="tasks.php" class="inline-block bg-white text-primary font-bold py-3 px-8 rounded-full hover:bg-gray-100 transition-colors">
                    <i class="fas fa-tasks ml-2"></i>
                    استعرض المهام المتاحة
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Wave Design -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 220">
            <path fill="#ffffff" fill-opacity="1" d="M0,96L48,106.7C96,117,192,139,288,138.7C384,139,480,117,576,101.3C672,85,768,75,864,80C960,85,1056,107,1152,112C1248,117,1344,107,1392,101.3L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
        </svg>
    </div>
</div>

<!-- Stats Section -->
<div class="py-12">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- إجمالي المسعفين -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-blue-100 text-primary mb-4">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-800 mb-2"><?php echo $totalVolunteers; ?></h3>
                <p class="text-gray-600">مسعف متطوع</p>
            </div>

            <!-- المهام النشطة -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-yellow-100 text-yellow-600 mb-4">
                    <i class="fas fa-clock text-xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-800 mb-2"><?php echo $activeTasks; ?></h3>
                <p class="text-gray-600">مهمة نشطة</p>
            </div>

            <!-- المهام المكتملة -->
            <div class="bg-white rounded-lg shadow-md p-6 text-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-green-100 text-green-600 mb-4">
                    <i class="fas fa-check-circle text-xl"></i>
                </div>
                <h3 class="text-3xl font-bold text-gray-800 mb-2"><?php echo $completedTasks; ?></h3>
                <p class="text-gray-600">مهمة مكتملة</p>
            </div>
        </div>
    </div>
</div>

<!-- Features Section -->
<div class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        <h2 class="text-3xl font-bold text-center text-gray-800 mb-12">مميزات النظام</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-primary mb-4">
                    <i class="fas fa-user-clock text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">تسجيل سريع</h3>
                <p class="text-gray-600">سجل كمسعف في دقائق معدودة وكن جزءاً من فريق الاستجابة للطوارئ</p>
            </div>

            <!-- Feature 2 -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-primary mb-4">
                    <i class="fas fa-tasks text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">إدارة المهام</h3>
                <p class="text-gray-600">نظام متكامل لإدارة المهام وتوزيعها على المسعفين المتاحين</p>
            </div>

            <!-- Feature 3 -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-primary mb-4">
                    <i class="fas fa-medal text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-2">نظام النقاط</h3>
                <p class="text-gray-600">احصل على نقاط مقابل كل مهمة تكملها بنجاح</p>
            </div>
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="bg-primary text-white py-16">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">كن جزءاً من فريقنا</h2>
        <p class="text-xl mb-8">انضم إلينا اليوم وساهم في خدمة مجتمعك</p>
        <?php if (!isset($_SESSION['user_id'])): ?>
            <a href="register.php" class="inline-block bg-white text-primary font-bold py-3 px-8 rounded-full hover:bg-gray-100 transition-colors">
                <i class="fas fa-user-plus ml-2"></i>
                سجل الآن
            </a>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
