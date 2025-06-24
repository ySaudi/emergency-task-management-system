<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME . ' - ' . SITE_SUBTITLE; ?></title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1F4287',
                        secondary: '#278EA5',
                    },
                    fontFamily: {
                        arabic: ['Tajawal', 'sans-serif'],
                    },
                }
            }
        }
    </script>
    
    <!-- Google Fonts - Tajawal -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <!-- Header -->
    <header class="bg-white shadow-md">
        <nav class="container mx-auto px-4 py-3">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-4 space-x-reverse">
                    <div class="text-right">
                        <h1 class="text-2xl font-bold text-primary"><?php echo SITE_NAME; ?></h1>
                        <h2 class="text-lg text-secondary"><?php echo SITE_SUBTITLE; ?></h2>
                    </div>
                </div>
                <div class="hidden md:flex space-x-6 space-x-reverse">
                    <a href="/" class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-home ml-1"></i>الرئيسية
                    </a>
                    <a href="/tasks.php" class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-tasks ml-1"></i>المهام
                    </a>
                    <a href="/register.php" class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-user-plus ml-1"></i>تسجيل
                    </a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/profile.php" class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-user ml-1"></i>الملف الشخصي
                    </a>
                    <a href="/logout.php" class="text-red-600 hover:text-red-700 transition-colors">
                        <i class="fas fa-sign-out-alt ml-1"></i>تسجيل خروج
                    </a>
                    <?php endif; ?>
                </div>
                <!-- Mobile Menu Button -->
                <button class="md:hidden text-gray-600 hover:text-primary focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            <!-- Mobile Menu -->
            <div class="md:hidden hidden mt-4 pb-4">
                <a href="/" class="block py-2 text-gray-600 hover:text-primary">
                    <i class="fas fa-home ml-1"></i>الرئيسية
                </a>
                <a href="/tasks.php" class="block py-2 text-gray-600 hover:text-primary">
                    <i class="fas fa-tasks ml-1"></i>المهام
                </a>
                <a href="/register.php" class="block py-2 text-gray-600 hover:text-primary">
                    <i class="fas fa-user-plus ml-1"></i>تسجيل
                </a>
                <?php if (isset($_SESSION['user_id'])): ?>
                <a href="/profile.php" class="block py-2 text-gray-600 hover:text-primary">
                    <i class="fas fa-user ml-1"></i>الملف الشخصي
                </a>
                <a href="/logout.php" class="block py-2 text-red-600 hover:text-red-700">
                    <i class="fas fa-sign-out-alt ml-1"></i>تسجيل خروج
                </a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8">
        <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="mb-4 p-4 rounded 
            <?php echo ($_SESSION['flash_type'] ?? 'success') === 'error' ? 'bg-red-100 text-red-700' : 'bg-green-100 text-green-700'; ?>">
            <?php 
            echo $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
            ?>
        </div>
        <?php endif; ?>
