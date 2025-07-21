<?php
// filepath: c:\xampp\htdocs\register-learning\templates\components\header.php
// ปรับปรุงเพื่อป้องกัน headers already sent
if (!defined('BASE_PATH')) {
    exit('Direct access not allowed');
}

// นำเข้า authentication helpers
require_once BASE_PATH . '/src/helpers/auth.php';

// เริ่ม session อย่างปลอดภัย
safeSessionStart();

// ตรวจสอบสถานะการเข้าสู่ระบบ
$is_logged_in = isLoggedIn();
$current_user = $is_logged_in ? getCurrentUsername() : null;
$is_admin = $is_logged_in ? isAdmin() : false;
?>
<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?? 'ລະບົບລົງທະບຽນນັກສຶກສາ' ?> - ວິທະຍາໄລການສຶກສາ</title>
    
    <!-- Tailwind CSS from CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Custom CSS Files -->
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/form.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/student-detail.css?v=<?= time() ?>">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/student-list.css?v=<?= time() ?>">
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Google Fonts for Lao -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Noto Sans Lao', sans-serif;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #f59e0b;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #d97706;
        }
        
        /* Custom animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes slideOutRight {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(100%);
                opacity: 0;
            }
        }
        
        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }
        
        .animate-slide-in {
            animation: slideInRight 0.3s ease-out;
        }
        
        .animate-slide-out {
            animation: slideOutRight 0.3s ease-out;
        }
        
        /* Button animations */
        .btn-hover {
            transition: all 0.3s ease;
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
        
        /* Mobile menu overlay */
        .mobile-menu-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 40;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .mobile-menu-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        /* Mobile menu */
        .mobile-menu {
            position: fixed;
            top: 0;
            right: -100%;
            width: 280px;
            height: 100vh;
            background: white;
            z-index: 50;
            transition: right 0.3s ease;
            box-shadow: -5px 0 15px rgba(0,0,0,0.1);
        }
        
        .mobile-menu.active {
            right: 0;
        }
        
        /* Touch-friendly buttons on mobile */
        @media (max-width: 768px) {
            .touch-target {
                min-height: 44px;
                min-width: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
            }
        }
        
        /* Safe area for notched screens */
        @supports (padding: max(0px)) {
            .safe-area-top {
                padding-top: max(1rem, env(safe-area-inset-top));
            }
            
            .safe-area-bottom {
                padding-bottom: max(1rem, env(safe-area-inset-bottom));
            }
        }
        
        /* Loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        /* Desktop Navigation */
        @media (max-width: 1023px) {
            .desktop-nav,
            .desktop-user,
            .desktop-login {
                display: none !important;
            }
        }
        
        @media (min-width: 1024px) {
            .desktop-nav {
                display: flex !important;
            }
            .desktop-user,
            .desktop-login {
                display: block !important;
            }
            .tablet-nav,
            .mobile-btn {
                display: none !important;
            }
        }
        
        /* Username visibility on extra large screens */
        @media (max-width: 1279px) {
            .desktop-username {
                display: none !important;
            }
        }
        
        @media (min-width: 1280px) {
            .desktop-username {
                display: block !important;
            }
        }
        
        /* Tablet Navigation */
        @media (max-width: 767px) {
            .tablet-nav {
                display: none !important;
            }
        }
    </style>
    
    <!-- Tailwind Configuration -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        'lao': ['Noto Sans Lao', 'sans-serif'],
                    },
                    colors: {
                        'amber': {
                            50: '#fffbeb',
                            100: '#fef3c7',
                            200: '#fde68a',
                            300: '#fcd34d',
                            400: '#fbbf24',
                            500: '#f59e0b',
                            600: '#d97706',
                            700: '#b45309',
                            800: '#92400e',
                            900: '#78350f',
                        }
                    },
                    screens: {
                        'xs': '475px',
                    }
                }
            }
        }
    </script>
</head>
<body class="font-lao bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 min-h-screen">
    
    <!-- Loading Overlay -->
    <div id="loadingOverlay" class="loading-overlay" style="display: none;">
        <div class="text-center">
            <div class="w-16 h-16 border-4 border-t-amber-500 border-gray-200 rounded-full animate-spin mx-auto mb-4"></div>
            <p class="text-white font-medium">ກຳລັງໂຫຼດ...</p>
        </div>
    </div>
    
    <!-- Mobile Menu Overlay -->
    <div id="mobileMenuOverlay" class="mobile-menu-overlay" onclick="closeMobileMenu()"></div>
    
    <!-- Mobile Menu -->
    <div id="mobileMenu" class="mobile-menu">
        <div class="flex flex-col h-full">
            <!-- Mobile Menu Header -->
            <div class="flex items-center justify-between p-4 border-b border-gray-200 safe-area-top bg-gradient-to-r from-amber-500 to-orange-500">
                <div class="flex items-center">
                    <i class="fas fa-graduation-cap text-xl text-white mr-2"></i>
                    <span class="text-lg font-bold text-white">ລະບົບລົງທະບຽນ</span>
                </div>
                <button onclick="closeMobileMenu()" class="p-2 text-white hover:text-gray-200 touch-target">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <!-- User Info (Mobile) -->
            <?php if ($is_logged_in): ?>
                <div class="p-4 bg-amber-50 border-b border-gray-200">
                    <div class="flex items-center">
                        <div class="w-12 h-12 bg-amber-500 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user text-white"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($current_user) ?></p>
                            <p class="text-xs text-gray-500">
                                <?= $is_admin ? 'ຜູ້ຄຸ້ມຄອງລະບົບ' : 'ຜູ້ໃຊ້ທົ່ວໄປ' ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Mobile Menu Items -->
            <div class="flex-1 overflow-y-auto py-4">
                <nav class="space-y-1">
                    <a href="<?= BASE_URL ?>" 
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-amber-50 hover:text-amber-600 transition-colors duration-200 touch-target"
                       onclick="closeMobileMenu()">
                        <i class="fas fa-home text-lg mr-3 w-6 text-center"></i>
                        <span class="font-medium">ໜ້າຫຼັກ</span>
                    </a>
                    
                    <a href="<?= BASE_URL ?>?page=register" 
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-amber-50 hover:text-amber-600 transition-colors duration-200 touch-target"
                       onclick="closeMobileMenu()">
                        <i class="fas fa-user-plus text-lg mr-3 w-6 text-center"></i>
                        <span class="font-medium">ລົງທະບຽນ</span>
                    </a>
                    
                    <a href="<?= BASE_URL ?>?page=students" 
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-amber-50 hover:text-amber-600 transition-colors duration-200 touch-target"
                       onclick="closeMobileMenu()">
                        <i class="fas fa-users text-lg mr-3 w-6 text-center"></i>
                        <span class="font-medium">ລາຍຊື່ນັກສຶກສາ</span>
                    </a>
                    
                    <a href="<?= BASE_URL ?>?page=qrcode" 
                       class="flex items-center px-4 py-3 text-gray-700 hover:bg-amber-50 hover:text-amber-600 transition-colors duration-200 touch-target"
                       onclick="closeMobileMenu()">
                        <i class="fas fa-qrcode text-lg mr-3 w-6 text-center"></i>
                        <span class="font-medium">QR Code</span>
                    </a>
                    
                    <?php if ($is_logged_in): ?>
                        <div class="border-t border-gray-200 mt-4 pt-4">
                            <a href="<?= BASE_URL ?>?page=dashboard" 
                               class="flex items-center px-4 py-3 text-gray-700 hover:bg-amber-50 hover:text-amber-600 transition-colors duration-200 touch-target"
                               onclick="closeMobileMenu()">
                                <i class="fas fa-tachometer-alt text-lg mr-3 w-6 text-center"></i>
                                <span class="font-medium">Dashboard</span>
                            </a>
                            
                            <a href="<?= BASE_URL ?>?action=logout" 
                               onclick="return confirm('ທ່ານຕ້ອງການອອກຈາກລະບົບບໍ?')" 
                               class="flex items-center px-4 py-3 text-red-600 hover:bg-red-50 transition-colors duration-200 touch-target">
                                <i class="fas fa-sign-out-alt text-lg mr-3 w-6 text-center"></i>
                                <span class="font-medium">ອອກຈາກລະບົບ</span>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="border-t border-gray-200 mt-4 pt-4">
                            <a href="<?= BASE_URL ?>?page=login" 
                               class="flex items-center px-4 py-3 text-amber-600 hover:bg-amber-50 transition-colors duration-200 font-medium touch-target"
                               onclick="closeMobileMenu()">
                                <i class="fas fa-sign-in-alt text-lg mr-3 w-6 text-center"></i>
                                <span>ເຂົ້າສູ່ລະບົບ</span>
                            </a>
                        </div>
                    <?php endif; ?>
                </nav>
            </div>
            
            <!-- Mobile Menu Footer -->
            <div class="border-t border-gray-200 p-4 safe-area-bottom bg-gray-50">
                <div class="text-center">
                    <p class="text-xs text-gray-500">© <?= date('Y') ?> ລະບົບລົງທະບຽນນັກສຶກສາ</p>
                    <p class="text-xs text-gray-400 mt-1">ວິທະຍາໄລການສຶກສາ</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Navigation -->
    <nav class="bg-white shadow-xl border-b-4 border-amber-500 sticky top-0 z-30">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo Section -->
                <div class="flex items-center flex-shrink-0">
                    <a href="<?= BASE_URL ?>" class="flex items-center group">
                        <div class="w-10 h-10 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center mr-2 sm:mr-3 group-hover:from-amber-500 group-hover:to-orange-600 transition-all">
                            <i class="fas fa-graduation-cap text-white text-xl"></i>
                        </div>
                        <div class="hidden sm:block">
                            <span class="text-xl font-bold text-gray-800 group-hover:text-amber-600 transition-colors">ລະບົບລົງທະບຽນນັກສຶກສາ</span>
                            <p class="text-xs text-gray-500">Student Registration System</p>
                        </div>
                        <div class="sm:hidden">
                            <span class="text-lg font-bold text-gray-800 group-hover:text-amber-600 transition-colors">ລະບົບລົງທະບຽນ</span>
                        </div>
                    </a>
                </div>
                
                <!-- Desktop Navigation - ใช้ CSS แบบ responsive -->
                <div class="desktop-nav flex items-center space-x-1">
                    <a href="<?= BASE_URL ?>" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 btn-hover">
                        <i class="fas fa-home mr-1"></i>ໜ້າຫຼັກ
                    </a>
                    <a href="<?= BASE_URL ?>?page=register" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 btn-hover">
                        <i class="fas fa-user-plus mr-1"></i>ລົງທະບຽນ
                    </a>
                    <a href="<?= BASE_URL ?>?page=students" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 btn-hover">
                        <i class="fas fa-users mr-1"></i>ລາຍຊື່ນັກສຶກສາ
                    </a>
                    <a href="<?= BASE_URL ?>?page=qrcode" 
                       class="text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 btn-hover">
                        <i class="fas fa-qrcode mr-1"></i>QR Code
                    </a>
                </div>
                
                <!-- Right Section -->
                <div class="flex items-center space-x-2">
                    <!-- User Menu (Desktop) -->
                    <?php if ($is_logged_in): ?>
                        <div class="desktop-user relative">
                            <button type="button" 
                                    class="flex items-center text-gray-700 hover:text-amber-600 px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200 btn-hover" 
                                    onclick="toggleUserMenu()">
                                <div class="w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                                <span class="desktop-username"><?= htmlspecialchars($current_user) ?></span>
                                <i class="fas fa-chevron-down ml-1 text-xs"></i>
                            </button>
                            
                            <div id="userMenu" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 z-50">
                                <div class="py-1">
                                    <div class="px-4 py-3 border-b border-gray-200">
                                        <p class="text-sm font-medium text-gray-900"><?= htmlspecialchars($current_user) ?></p>
                                        <p class="text-xs text-gray-500">
                                            <?= $is_admin ? 'ຜູ້ຄຸ້ມຄອງລະບົບ' : 'ຜູ້ໃຊ້ທົ່ວໄປ' ?>
                                        </p>
                                    </div>
                                    <a href="<?= BASE_URL ?>?page=dashboard" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors">
                                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                                    </a>
                                    <a href="<?= BASE_URL ?>?action=logout" 
                                       onclick="return confirm('ທ່ານຕ້ອງການອອກຈາກລະບົບບໍ?')" 
                                       class="block px-4 py-2 text-sm text-red-700 hover:bg-red-50 transition-colors">
                                        <i class="fas fa-sign-out-alt mr-2"></i>ອອກຈາກລະບົບ
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>?page=login" 
                           class="desktop-login flex items-center bg-amber-500 hover:bg-amber-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 btn-hover">
                            <i class="fas fa-sign-in-alt mr-2"></i>ເຂົ້າສູ່ລະບົບ
                        </a>
                    <?php endif; ?>
                    
                    <!-- Quick Action Button (Tablet) -->
                    <div class="tablet-nav md:flex lg:hidden">
                        <?php if ($is_logged_in): ?>
                            <button onclick="toggleUserMenu()" class="p-2 text-gray-700 hover:text-amber-600 transition-colors touch-target">
                                <div class="w-8 h-8 bg-amber-500 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user text-white text-sm"></i>
                                </div>
                            </button>
                        <?php else: ?>
                            <a href="<?= BASE_URL ?>?page=login" class="p-2 text-amber-600 hover:text-amber-700 transition-colors touch-target">
                                <i class="fas fa-sign-in-alt text-xl"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Mobile Menu Button -->
                    <button onclick="openMobileMenu()" 
                            class="mobile-btn lg:hidden p-2 text-gray-700 hover:text-amber-600 transition-colors touch-target"
                            aria-label="เปิดเมนู">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
        
       
    </nav>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['message'])): ?>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="alert-message bg-<?= $_SESSION['message_type'] === 'success' ? 'green' : ($_SESSION['message_type'] === 'warning' ? 'yellow' : 'red') ?>-100 border border-<?= $_SESSION['message_type'] === 'success' ? 'green' : ($_SESSION['message_type'] === 'warning' ? 'yellow' : 'red') ?>-400 text-<?= $_SESSION['message_type'] === 'success' ? 'green' : ($_SESSION['message_type'] === 'warning' ? 'yellow' : 'red') ?>-700 px-4 py-3 rounded-xl relative animate-fade-in" role="alert">
                <div class="flex items-center">
                    <i class="fas fa-<?= $_SESSION['message_type'] === 'success' ? 'check-circle' : ($_SESSION['message_type'] === 'warning' ? 'exclamation-triangle' : 'times-circle') ?> mr-2 flex-shrink-0"></i>
                    <span class="font-medium flex-1"><?= htmlspecialchars($_SESSION['message']) ?></span>
                    <button type="button" class="ml-4 p-1 hover:bg-<?= $_SESSION['message_type'] === 'success' ? 'green' : ($_SESSION['message_type'] === 'warning' ? 'yellow' : 'red') ?>-200 rounded touch-target" onclick="this.parentElement.parentElement.style.display='none'">
                        <i class="fas fa-times text-<?= $_SESSION['message_type'] === 'success' ? 'green' : ($_SESSION['message_type'] === 'warning' ? 'yellow' : 'red') ?>-500"></i>
                    </button>
                </div>
            </div>
        </div>
        <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
    <?php endif; ?>
    
    <!-- Main Content -->
    <main class="max-w-7xl mx-auto">

    <script>
        // Global loading functions
        function showLoading() {
            document.getElementById('loadingOverlay').style.display = 'flex';
        }
        
        function hideLoading() {
            document.getElementById('loadingOverlay').style.display = 'none';
        }
        
        // Mobile menu functions
        function openMobileMenu() {
            const overlay = document.getElementById('mobileMenuOverlay');
            const menu = document.getElementById('mobileMenu');
            
            overlay.classList.add('active');
            menu.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
        
        function closeMobileMenu() {
            const overlay = document.getElementById('mobileMenuOverlay');
            const menu = document.getElementById('mobileMenu');
            
            overlay.classList.remove('active');
            menu.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // User menu toggle
        function toggleUserMenu() {
            const menu = document.getElementById('userMenu');
            if (menu) {
                menu.classList.toggle('hidden');
            }
        }

        // Close menus when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const userButton = event.target.closest('button[onclick="toggleUserMenu()"]');
            
            // Close user menu if clicking outside
            if (userMenu && !userButton && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
        
        // Handle window resize
        window.addEventListener('resize', function() {
            if (window.innerWidth >= 1024) { // lg breakpoint
                closeMobileMenu();
            }
        });
        
        // Keyboard navigation for accessibility
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeMobileMenu();
                const userMenu = document.getElementById('userMenu');
                if (userMenu) {
                    userMenu.classList.add('hidden');
                }
            }
        });
        
        // Auto-hide success messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            hideLoading();
            
            const alertMessages = document.querySelectorAll('.alert-message');
            alertMessages.forEach(function(message) {
                setTimeout(function() {
                    if (message.parentElement) {
                        message.style.opacity = '0';
                        message.style.transform = 'translateY(-10px)';
                        setTimeout(function() {
                            message.style.display = 'none';
                        }, 300);
                    }
                }, 5000);
            });
            
            // Show loading on form submissions
            const forms = document.querySelectorAll('form:not(.no-loading)');
            forms.forEach(function(form) {
                form.addEventListener('submit', function() {
                    showLoading();
                });
            });
        });
    </script>
</body>
</html>