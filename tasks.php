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

// جلب المهام المتاحة
try {
    $stmt = $pdo->prepare("
        SELECT t.*, 
               COUNT(ta.id) as acceptance_count
        FROM tasks t
        LEFT JOIN task_assignments ta ON t.id = ta.task_id
        WHERE t.status = 'pending'
        GROUP BY t.id
        ORDER BY t.created_at DESC
    ");
    $stmt->execute();
    $tasks = $stmt->fetchAll();

    // جلب المهام المقبولة من قبل المستخدم
    $stmt = $pdo->prepare("
        SELECT t.*, ta.created_at as accepted_at, ta.status as assignment_status
        FROM tasks t
        INNER JOIN task_assignments ta ON t.id = ta.task_id
        WHERE ta.user_id = ?
        ORDER BY ta.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $myTasks = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log($e->getMessage());
    $_SESSION['flash_message'] = "حدث خطأ أثناء جلب المهام";
    $_SESSION['flash_type'] = 'error';
}

require_once 'includes/header.php';
?>

<div class="container mx-auto px-4">
    <!-- عرض المهام المقبولة من قبل المستخدم -->
    <section class="mb-12">
        <h2 class="text-2xl font-bold mb-6 text-primary">
            <i class="fas fa-tasks ml-2"></i>
            مهامي
        </h2>
        
        <?php if (empty($myTasks)): ?>
            <div class="bg-gray-100 rounded-lg p-6 text-center">
                <p class="text-gray-600">لم تقم بقبول أي مهام حتى الآن</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($myTasks as $task): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 border-r-4 <?php echo $task['assignment_status'] === 'completed' ? 'border-green-500' : 'border-blue-500'; ?>">
                        <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($task['title']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($task['description']); ?></p>
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <i class="fas fa-map-marker-alt ml-2"></i>
                            <?php echo htmlspecialchars($task['location']); ?>
                        </div>
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-clock ml-2"></i>
                            تم القبول: <?php echo date('Y/m/d H:i', strtotime($task['accepted_at'])); ?>
                        </div>
                        <?php if ($task['assignment_status'] === 'completed'): ?>
                            <div class="mt-4 text-green-600">
                                <i class="fas fa-check-circle ml-2"></i>
                                تم إكمال المهمة
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>

    <!-- عرض المهام المتاحة -->
    <section>
        <h2 class="text-2xl font-bold mb-6 text-primary">
            <i class="fas fa-list-alt ml-2"></i>
            المهام المتاحة
        </h2>
        
        <?php if (empty($tasks)): ?>
            <div class="bg-gray-100 rounded-lg p-6 text-center">
                <p class="text-gray-600">لا توجد مهام متاحة حالياً</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($tasks as $task): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow">
                        <h3 class="text-xl font-bold mb-2"><?php echo htmlspecialchars($task['title']); ?></h3>
                        <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($task['description']); ?></p>
                        <div class="flex items-center text-sm text-gray-500 mb-2">
                            <i class="fas fa-map-marker-alt ml-2"></i>
                            <?php echo htmlspecialchars($task['location']); ?>
                        </div>
                        <div class="flex items-center text-sm text-gray-500 mb-4">
                            <i class="fas fa-clock ml-2"></i>
                            <?php echo date('Y/m/d H:i', strtotime($task['created_at'])); ?>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">
                                <i class="fas fa-users ml-1"></i>
                                عدد المتقدمين: <?php echo $task['acceptance_count']; ?>
                            </span>
                            <form action="accept_task.php" method="POST" class="inline">
                                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                <button type="submit" 
                                        class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-full text-sm transition-colors">
                                    <i class="fas fa-check ml-1"></i>
                                    قبول المهمة
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php require_once 'includes/footer.php'; ?>
