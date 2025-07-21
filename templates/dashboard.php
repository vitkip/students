<?php
// filepath: c:\xampp\htdocs\register-learning\templates\dashboard.php
// Dashboard template - requires login

// Check authentication
if (!defined('BASE_PATH')) {
    header('Location: ../public/index.php?page=dashboard');
    exit('Access denied. Please use proper navigation.');
}

// Require login
require_once BASE_PATH . '/src/helpers/auth.php';
requireLogin('login.php');

// Get current user info
$current_user_id = getCurrentUserId();
$current_username = getCurrentUsername();
$current_role = getCurrentUserRole();
$is_admin = isAdmin();

// Page title
$page_title = 'ໜ້າຫຼັກ - Dashboard';
?>

<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            
            <!-- Welcome Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-tachometer-alt text-amber-500 mr-3"></i>Dashboard
                </h1>
                <p class="text-gray-600 text-lg">
                    ຍິນດີຕ້ອນຮັບ, <strong><?= htmlspecialchars($current_username) ?></strong>
                    <?php if ($is_admin): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 ml-2">
                            <i class="fas fa-crown mr-1"></i>Admin
                        </span>
                    <?php endif; ?>
                </p>
            </div>
            
            <!-- Quick Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Students Count -->
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-blue-100">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">ນັກສຶກສາທັງໝົດ</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php
                                try {
                                    $stmt = $db->query("SELECT COUNT(*) as count FROM students");
                                    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                                    echo number_format($count);
                                } catch (Exception $e) {
                                    echo '0';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Majors Count -->
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-green-100">
                            <i class="fas fa-graduation-cap text-2xl text-green-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">ສາຂາວິຊາ</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php
                                try {
                                    $stmt = $db->query("SELECT COUNT(*) as count FROM majors");
                                    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                                    echo number_format($count);
                                } catch (Exception $e) {
                                    echo '0';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Academic Years -->
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-purple-100">
                            <i class="fas fa-calendar text-2xl text-purple-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">ປີການສຶກສາ</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php
                                try {
                                    $stmt = $db->query("SELECT COUNT(*) as count FROM academic_years");
                                    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                                    echo number_format($count);
                                } catch (Exception $e) {
                                    echo '0';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- Online Users -->
                <div class="bg-white rounded-xl shadow-lg p-6 hover:shadow-xl transition-shadow duration-300">
                    <div class="flex items-center">
                        <div class="p-3 rounded-full bg-orange-100">
                            <i class="fas fa-user-clock text-2xl text-orange-600"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-600">ຜູ້ໃຊ້ອອນລາຍ</p>
                            <p class="text-2xl font-bold text-gray-900">
                                <?php
                                try {
                                    $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE last_login > DATE_SUB(NOW(), INTERVAL 30 MINUTE)");
                                    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
                                    echo number_format($count);
                                } catch (Exception $e) {
                                    echo '1'; // At least current user
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Register Student -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="p-3 rounded-full bg-blue-100">
                                <i class="fas fa-user-plus text-2xl text-blue-600"></i>
                            </div>
                            <h3 class="ml-4 text-xl font-semibold text-gray-800">ລົງທະບຽນນັກສຶກສາ</h3>
                        </div>
                        <p class="text-gray-600 mb-4">ເພີ່ມນັກສຶກສາໃໝ່ເຂົ້າລະບົບ</p>
                        <a href="<?= BASE_URL ?>?page=register" 
                           class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors duration-200">
                            <i class="fas fa-plus mr-2"></i>ລົງທະບຽນ
                        </a>
                    </div>
                </div>
                
                <!-- View Students -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="p-3 rounded-full bg-green-100">
                                <i class="fas fa-list text-2xl text-green-600"></i>
                            </div>
                            <h3 class="ml-4 text-xl font-semibold text-gray-800">ລາຍຊື່ນັກສຶກສາ</h3>
                        </div>
                        <p class="text-gray-600 mb-4">ເບິ່ງແລະຈັດການຂໍ້ມູນນັກສຶກສາ</p>
                        <a href="<?= BASE_URL ?>?page=students" 
                           class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors duration-200">
                            <i class="fas fa-eye mr-2"></i>ເບິ່ງລາຍຊື່
                        </a>
                    </div>
                </div>
                
                <!-- QR Code -->
                <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                    <div class="p-6">
                        <div class="flex items-center mb-4">
                            <div class="p-3 rounded-full bg-purple-100">
                                <i class="fas fa-qrcode text-2xl text-purple-600"></i>
                            </div>
                            <h3 class="ml-4 text-xl font-semibold text-gray-800">QR Code</h3>
                        </div>
                        <p class="text-gray-600 mb-4">ສ້າງແລະຈັດການ QR Code</p>
                        <a href="<?= BASE_URL ?>?page=qrcode" 
                           class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors duration-200">
                            <i class="fas fa-qrcode mr-2"></i>QR Code
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- User Info Section -->
            <div class="mt-8 bg-white rounded-xl shadow-lg p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-user-circle text-amber-500 mr-2"></i>ຂໍ້ມູນຜູ້ໃຊ້
                </h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <div class="space-y-3">
                            <div class="flex items-center">
                                <span class="font-medium text-gray-600 w-24">ຊື່ຜູ້ໃຊ້:</span>
                                <span class="text-gray-800"><?= htmlspecialchars($current_username) ?></span>
                            </div>
                            <div class="flex items-center">
                                <span class="font-medium text-gray-600 w-24">ບົດບາດ:</span>
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?= $is_admin ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' ?>">
                                    <i class="fas fa-<?= $is_admin ? 'crown' : 'user' ?> mr-1"></i>
                                    <?= $is_admin ? 'ຜູ້ຄຸ້ມຄອງ' : 'ຜູ້ໃຊ້' ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end items-center">
                        <a href="<?= BASE_URL ?>?action=logout" 
                           onclick="return confirm('ທ່ານຕ້ອງການອອກຈາກລະບົບບໍ?')"
                           class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors duration-200">
                            <i class="fas fa-sign-out-alt mr-2"></i>ອອກຈາກລະບົບ
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>
