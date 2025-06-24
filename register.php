<?php
session_start();
require_once 'config/config.php';
require_once 'includes/db.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // تنظيف وفحص المدخلات
    $employee_number = sanitizeInput($_POST['employee_number']);
    $phone = sanitizeInput($_POST['phone']);
    $emergency_contact = sanitizeInput($_POST['emergency_contact']);
    $current_address = sanitizeInput($_POST['current_address']);
    $secondary_address = sanitizeInput($_POST['secondary_address']);
    $response_time = sanitizeInput($_POST['response_time']);
    $special_conditions = sanitizeInput($_POST['special_conditions']);

    // التحقق من صحة البيانات
    if (!isValidEmployeeNumber($employee_number)) {
        $errors[] = "رقم الموظف يجب أن يكون من 3 إلى 7 أرقام";
    }

    if (!isValidSaudiPhone($phone)) {
        $errors[] = "رقم الهاتف غير صحيح. يجب أن يبدأ ب 05 ويتكون من 10 أرقام";
    }

    if (!isValidSaudiPhone($emergency_contact)) {
        $errors[] = "رقم الطوارئ غير صحيح. يجب أن يبدأ ب 05 ويتكون من 10 أرقام";
    }

    if (empty($current_address) || !isArabic($current_address)) {
        $errors[] = "يرجى إدخال العنوان الحالي باللغة العربية";
    }

    if (empty($secondary_address) || !isArabic($secondary_address)) {
        $errors[] = "يرجى إدخال العنوان الثانوي باللغة العربية";
    }

    if (!in_array($response_time, ['أقل من ساعة', 'من ساعة إلى 5 ساعات', 'من 5 ساعات إلى 12 ساعة'])) {
        $errors[] = "يرجى اختيار وقت استجابة صحيح";
    }

    // إذا لم تكن هناك أخطاء، قم بالإدخال في قاعدة البيانات
    if (empty($errors)) {
        try {
            $stmt = $pdo->prepare("
                INSERT INTO users (
                    employee_number, 
                    phone, 
                    emergency_contact, 
                    current_address, 
                    secondary_address, 
                    response_time, 
                    special_conditions
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->execute([
                $employee_number,
                $phone,
                $emergency_contact,
                $current_address,
                $secondary_address,
                $response_time,
                $special_conditions
            ]);

            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['flash_message'] = "تم التسجيل بنجاح!";
            $_SESSION['flash_type'] = 'success';
            
            header("Location: index.php");
            exit;
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) { // Duplicate entry
                $errors[] = "رقم الموظف مسجل مسبقاً";
            } else {
                $errors[] = "حدث خطأ أثناء التسجيل. الرجاء المحاولة مرة أخرى";
                error_log($e->getMessage());
            }
        }
    }
}

require_once 'includes/header.php';
?>

<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-2xl font-bold mb-6 text-center text-primary">تسجيل مسعف جديد</h2>

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

        <form method="POST" class="space-y-6" novalidate>
            <!-- رقم الموظف -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="employee_number">
                    رقم الموظف <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                           id="employee_number"
                           name="employee_number"
                           type="text"
                           pattern="\d{3,7}"
                           required
                           placeholder="مثال: 1234"
                           value="<?php echo $_POST['employee_number'] ?? ''; ?>">
                    <i class="fas fa-id-card absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- رقم الهاتف -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="phone">
                    رقم الهاتف <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                           id="phone"
                           name="phone"
                           type="tel"
                           required
                           placeholder="05xxxxxxxx"
                           value="<?php echo $_POST['phone'] ?? ''; ?>">
                    <i class="fas fa-phone absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- رقم الطوارئ -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="emergency_contact">
                    رقم شخص قريب للطوارئ <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                           id="emergency_contact"
                           name="emergency_contact"
                           type="tel"
                           required
                           placeholder="05xxxxxxxx"
                           value="<?php echo $_POST['emergency_contact'] ?? ''; ?>">
                    <i class="fas fa-phone-square absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- العنوان الحالي -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="current_address">
                    مكان السكن الحالي <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                           id="current_address"
                           name="current_address"
                           type="text"
                           required
                           placeholder="المدينة، الحي، الشارع"
                           value="<?php echo $_POST['current_address'] ?? ''; ?>">
                    <i class="fas fa-home absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- العنوان الثانوي -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="secondary_address">
                    مكان سكن آخر (منطقة الأهل/الوالدين) <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <input class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                           id="secondary_address"
                           name="secondary_address"
                           type="text"
                           required
                           placeholder="المدينة، الحي، الشارع"
                           value="<?php echo $_POST['secondary_address'] ?? ''; ?>">
                    <i class="fas fa-building absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- وقت الاستجابة -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="response_time">
                    وقت الاستجابة المتوقع <span class="text-red-500">*</span>
                </label>
                <div class="relative">
                    <select class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                            id="response_time"
                            name="response_time"
                            required>
                        <option value="">اختر وقت الاستجابة</option>
                        <option value="أقل من ساعة" <?php echo (isset($_POST['response_time']) && $_POST['response_time'] === 'أقل من ساعة') ? 'selected' : ''; ?>>
                            أقل من ساعة
                        </option>
                        <option value="من ساعة إلى 5 ساعات" <?php echo (isset($_POST['response_time']) && $_POST['response_time'] === 'من ساعة إلى 5 ساعات') ? 'selected' : ''; ?>>
                            من ساعة إلى 5 ساعات
                        </option>
                        <option value="من 5 ساعات إلى 12 ساعة" <?php echo (isset($_POST['response_time']) && $_POST['response_time'] === 'من 5 ساعات إلى 12 ساعة') ? 'selected' : ''; ?>>
                            من 5 ساعات إلى 12 ساعة
                        </option>
                    </select>
                    <i class="fas fa-clock absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- ظروف خاصة -->
            <div>
                <label class="block text-gray-700 text-sm font-bold mb-2" for="special_conditions">
                    ظروف خاصة يجب وضعها في الاعتبار
                </label>
                <div class="relative">
                    <textarea class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent"
                              id="special_conditions"
                              name="special_conditions"
                              rows="4"
                              placeholder="اكتب أي ظروف خاصة يجب مراعاتها..."><?php echo $_POST['special_conditions'] ?? ''; ?></textarea>
                    <i class="fas fa-info-circle absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- زر التسجيل -->
            <div class="flex items-center justify-center">
                <button class="bg-primary hover:bg-primary/90 text-white font-bold py-2 px-8 rounded-full focus:outline-none focus:shadow-outline transition-colors"
                        type="submit">
                    <i class="fas fa-user-plus ml-2"></i>
                    تسجيل
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
