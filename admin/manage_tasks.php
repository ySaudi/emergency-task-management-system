<?php
session_start();
require_once '../config/config.php';
require_once '../includes/db.php';

// التحقق من صلاحيات المدير
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header("Location: login.php");
    exit;
}

// معالجة تحديث حالة المهمة
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $task_id = (int)$_POST['task_id'];
    
    try {
        switch ($_POST['action']) {
            case 'complete':
                $stmt = $pdo->prepare("UPDATE tasks SET status = 'completed' WHERE id = ?");
                $stmt->execute([$task_id]);
                $_SESSION['flash_message'] = "تم تحديث حالة المهمة بنجاح";
                break;
                
            case 'delete':
                $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = ?");
                $stmt->execute([$task_id]);
                $_SESSION['flash_message'] = "تم حذف المهمة بنجاح";
                break;
        }
        $_SESSION['flash_type'] = 'success';
    } catch (PDOException $e) {
        error_log($e->getMessage());
        $_SESSION['flash_message'] = "حدث خطأ أثناء تحديث المهمة";
        $_SESSION['flash_type'] = 'error';
    }
    
    header("Location: manage_tasks.php");
    exit;
}

// جلب جميع المهام مع معلومات المسعفين المقبولين
try {
    $stmt = $pdo->query("
        SELECT 
            t.*,
            COUNT(DISTINCT ta.user_id) as volunteers_count,
            GROUP_CONCAT(DISTINCT u.employee_number) as volunteer_numbers
        FROM tasks t
        LEFT JOIN task_assignments ta ON t.id = ta.task_id
        LEFT JOIN users u ON ta.user_id = u.id
        GROUP BY t.id
        ORDER BY t.created_at DESC
    ");
    $tasks = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log($e->getMessage());
    $_SESSION['flash_message'] = "حدث خطأ أثناء جلب المهام";
    $_SESSION['flash_type'] = 'error';
}

require_once '../includes/header.php';
?>

<div class="container mx-auto px-4">
    <div class="flex justify-between items-center mb-8">
        <h1 class="text-3xl font-bold text-primary">إدارة المهام</h1>
        <div class="space-x-4 space-x-reverse">
            <a href="create_task.php" class="bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-full inline-flex items-center">
                <i class="fas fa-plus ml-2"></i>
                إضافة مهمة جديدة
            </a>
            <a href="index.php" class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-full inline-flex items-center">
                <i class="fas fa-arrow-right ml-2"></i>
                عودة للوحة التحكم
            </a>
        </div>
    </div>

    <!-- تصفية المهام -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-8">
        <div class="flex flex-wrap gap-4">
            <button class="task-filter active bg-primary text-white px-4 py-2 rounded-full" data-status="all">
                جميع المهام
            </button>
            <button class="task-filter bg-yellow-500 text-white px-4 py-2 rounded-full" data-status="pending">
                المهام المعلقة
            </button>
            <button class="task-filter bg-blue-500 text-white px-4 py-2 rounded-full" data-status="accepted">
                المهام المقبولة
            </button>
            <button class="task-filter bg-green-500 text-white px-4 py-2 rounded-full" data-status="completed">
                المهام المكتملة
            </button>
        </div>
    </div>

    <!-- قائمة المهام -->
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            العنوان
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الموقع
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الحالة
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            المسعفين
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            تاريخ الإنشاء
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            الإجراءات
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php foreach ($tasks as $task): ?>
                        <tr class="task-row" data-status="<?php echo $task['status']; ?>">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    <?php echo htmlspecialchars($task['title']); ?>
                                </div>
                                <div class="text-sm text-gray-500">
                                    <?php echo htmlspecialchars(substr($task['description'], 0, 100)) . '...'; ?>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?php echo htmlspecialchars($task['location']); ?>
                            </td>
                            <td class="px-6 py-4">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php
                                    echo match($task['status']) {
                                        'pending' => 'bg-yellow-100 text-yellow-800',
                                        'accepted' => 'bg-blue-100 text-blue-800',
                                        'completed' => 'bg-green-100 text-green-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                    ?>">
                                    <?php
                                    echo match($task['status']) {
                                        'pending' => 'معلقة',
                                        'accepted' => 'مقبولة',
                                        'completed' => 'مكتملة',
                                        default => 'غير معروفة'
                                    };
                                    ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?php if ($task['volunteers_count'] > 0): ?>
                                    <div class="tooltip" title="<?php echo str_replace(',', ' - ', $task['volunteer_numbers']); ?>">
                                        <?php echo $task['volunteers_count']; ?> مسعف
                                    </div>
                                <?php else: ?>
                                    لا يوجد
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                <?php echo date('Y/m/d H:i', strtotime($task['created_at'])); ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <div class="flex items-center space-x-3 space-x-reverse">
                                    <?php if ($task['status'] !== 'completed'): ?>
                                        <form method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من إكمال هذه المهمة؟')">
                                            <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                            <input type="hidden" name="action" value="complete">
                                            <button type="submit" class="text-green-600 hover:text-green-900">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    
                                    <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    
                                    <form method="POST" class="inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟')">
                                        <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                                        <input type="hidden" name="action" value="delete">
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// تصفية المهام
document.addEventListener('DOMContentLoaded', function() {
    const filterButtons = document.querySelectorAll('.task-filter');
    const taskRows = document.querySelectorAll('.task-row');

    filterButtons.forEach(button => {
        button.addEventListener('click', () => {
            // إزالة الفلتر النشط
            filterButtons.forEach(btn => btn.classList.remove('active', 'bg-primary'));
            
            // إضافة الفلتر النشط
            button.classList.add('active', 'bg-primary');
            
            const status = button.dataset.status;
            
            // إظهار/إخفاء الصفوف
            taskRows.forEach(row => {
                if (status === 'all' || row.dataset.status === status) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
    });
});

// تفعيل التلميحات
const tooltips = document.querySelectorAll('.tooltip');
tooltips.forEach(tooltip => {
    const title = tooltip.getAttribute('title');
    tooltip.addEventListener('mouseenter', () => {
        const div = document.createElement('div');
        div.className = 'tooltip-popup bg-black text-white text-xs rounded py-1 px-2 absolute z-10';
        div.textContent = title;
        tooltip.appendChild(div);
    });
    tooltip.addEventListener('mouseleave', () => {
        const popup = tooltip.querySelector('.tooltip-popup');
        if (popup) popup.remove();
    });
});
</script>

<?php require_once '../includes/footer.php'; ?>
