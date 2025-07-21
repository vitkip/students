<?php
// filepath: c:\xampp\htdocs\register-learning\templates\home.php
// ຫນ້າຫຼັກຂອງລະບົບລົງທະບຽນນັກສຶກສາ

// ตรวจสอบว่ามีการเรียกใช้ผ่าน index.php หรือไม่
if (!defined('BASE_PATH')) {
    header('Location: ../public/index.php');
    exit('Access denied. Please use proper navigation.');
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

<!-- Hero Section -->
<div class="relative bg-gradient-to-br from-amber-500 via-orange-500 to-red-500 text-white">
    <div class="absolute inset-0 bg-black opacity-20"></div>
    <div class="relative container mx-auto px-4 py-20 text-center">
        <div class="max-w-4xl mx-auto">
            <h1 class="text-5xl md:text-7xl font-bold mb-6 animate-fade-in">
                ລະບົບລົງທະບຽນນັກສຶກສາ
            </h1>
            <p class="text-xl md:text-2xl mb-8 opacity-90">
                ລະບົບຄຸ້ມຄອງຂໍ້ມູນນັກສຶກສາແບບຄົບວົງຈອນ ສຳລັບສະຖາບັນການສຶກສາ
            </p>
            
            <?php if (!$is_logged_in): ?>
                <div class="flex flex-col sm:flex-row justify-center gap-4 mb-8">
                    <a href="<?= BASE_URL ?>index.php?page=login" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-white text-amber-600 font-bold rounded-lg shadow-lg hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-1">
                        <i class="fas fa-sign-in-alt mr-2"></i>ເຂົ້າສູ່ລະບົບ
                    </a>
                    <a href="<?= BASE_URL ?>index.php?page=register" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-lg hover:bg-white hover:text-amber-600 transition-all duration-300 transform hover:-translate-y-1">
                        <i class="fas fa-user-plus mr-2"></i>ລົງທະບຽນນັກສຶກສາ
                    </a>
                </div>
            <?php else: ?>
                <div class="bg-white bg-opacity-20 backdrop-blur-sm rounded-xl p-6 mb-8 max-w-md mx-auto">
                    <div class="flex items-center justify-center mb-4">
                        <div class="w-16 h-16 bg-white bg-opacity-30 rounded-full flex items-center justify-center">
                            <i class="fas fa-user text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">ຍິນດີຕ້ອນຮັບ!</h3>
                    <p class="opacity-90"><?= htmlspecialchars($current_user) ?></p>
                    <?php if ($is_admin): ?>
                        <span class="inline-block mt-2 px-3 py-1 bg-yellow-400 text-yellow-900 text-sm font-semibold rounded-full">
                            <i class="fas fa-crown mr-1"></i>Administrator
                        </span>
                    <?php endif; ?>
                </div>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="<?= BASE_URL ?>index.php?page=dashboard" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-white text-amber-600 font-bold rounded-lg shadow-lg hover:bg-gray-100 transition-all duration-300 transform hover:-translate-y-1">
                        <i class="fas fa-tachometer-alt mr-2"></i>Dashboard
                    </a>
                    <a href="<?= BASE_URL ?>index.php?page=students" 
                       class="inline-flex items-center justify-center px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-lg hover:bg-white hover:text-amber-600 transition-all duration-300 transform hover:-translate-y-1">
                        <i class="fas fa-users mr-2"></i>ລາຍຊື່ນັກສຶກສາ
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Floating Animation Elements -->
    <div class="absolute top-20 left-10 animate-float-slow">
        <div class="w-20 h-20 bg-white bg-opacity-10 rounded-full"></div>
    </div>
    <div class="absolute bottom-20 right-10 animate-float-medium">
        <div class="w-16 h-16 bg-white bg-opacity-10 rounded-full"></div>
    </div>
    <div class="absolute top-1/2 left-1/4 animate-float-fast">
        <div class="w-12 h-12 bg-white bg-opacity-10 rounded-full"></div>
    </div>
</div>

<!-- Features Section -->
<div class="py-20 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">ຄຸນສົມບັດຫຼັກ</h2>
            <p class="text-xl text-gray-600 max-w-2xl mx-auto">
                ລະບົບທີ່ຄົບຄົບຄາວສຳລັບການຄຸ້ມຄອງຂໍ້ມູນນັກສຶກສາ ແລະ ການລົງທະບຽນ
            </p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            <!-- Feature 1 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-user-plus text-3xl text-blue-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">ລົງທະບຽນງ່າຍດາຍ</h3>
                <p class="text-gray-600">
                    ລົງທະບຽນຂໍ້ມູນນັກສຶກສາໃໝ່ໄດ້ຢ່າງງ່າຍດາຍ ພ້ອມການອັບໂຫລດຮູບພາບ
                </p>
            </div>
            
            <!-- Feature 2 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-search text-3xl text-green-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">ຄົ້ນຫາລາຍລະອຽດ</h3>
                <p class="text-gray-600">
                    ຄົ້ນຫາຂໍ້ມູນນັກສຶກສາໄດ້ຢ່າງລະອຽດ ດ້ວຍຕົວກອງຫຼາຍແບບ
                </p>
            </div>
            
            <!-- Feature 3 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-id-card text-3xl text-purple-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">ບັດນັກສຶກສາ</h3>
                <p class="text-gray-600">
                    ສ້າງ ແລະ ພິມບັດນັກສຶກສາພ້ອມ QR Code ສຳລັບການຢັ້ງຢືນ
                </p>
            </div>
            
            <!-- Feature 4 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
                <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-chart-bar text-3xl text-yellow-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">ລາຍງານສະຖິຕິ</h3>
                <p class="text-gray-600">
                    ເບິ່ງສະຖິຕິຕ່າງໆ ຂອງນັກສຶກສາແບບ Real-time Dashboard
                </p>
            </div>
            
            <!-- Feature 5 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-shield-alt text-3xl text-red-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">ປອດໄພສູງ</h3>
                <p class="text-gray-600">
                    ລະບົບການຮັບຮອງຕົວຕົນທີ່ປອດໄພ ແລະ ການຄຸ້ມຄອງສິດທິຜູ້ໃຊ້
                </p>
            </div>
            
            <!-- Feature 6 -->
            <div class="bg-white rounded-xl shadow-lg p-8 text-center hover:shadow-xl transition-shadow duration-300">
                <div class="w-16 h-16 bg-indigo-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <i class="fas fa-mobile-alt text-3xl text-indigo-600"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-800 mb-4">Responsive Design</h3>
                <p class="text-gray-600">
                    ໃຊ້ງານໄດ້ທຸກອຸປະກອນ ທັງ Desktop, Tablet ແລະ Mobile
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Section (only for logged-in users) -->
<?php if ($is_logged_in): ?>
<div class="py-20 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">ສະຖິຕິລະບົບ</h2>
            <p class="text-xl text-gray-600">ຂໍ້ມູນພາບລວມຂອງລະບົບ</p>
        </div>
        
        <?php
        // Get basic statistics if database is available
        $stats = [];
        if (isset($db) && $db) {
            try {
                // Count students
                $stmt = $db->query("SELECT COUNT(*) as total FROM students");
                $stats['students'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                
                // Count majors
                $stmt = $db->query("SELECT COUNT(*) as total FROM majors");
                $stats['majors'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                
                // Count academic years
                $stmt = $db->query("SELECT COUNT(*) as total FROM academic_years");
                $stats['years'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                
                // Count users
                $stmt = $db->query("SELECT COUNT(*) as total FROM users");
                $stats['users'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                
            } catch (Exception $e) {
                $stats = ['students' => 0, 'majors' => 0, 'years' => 0, 'users' => 0];
            }
        }
        ?>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="text-center">
                <div class="text-4xl font-bold text-blue-600 mb-2"><?= number_format($stats['students'] ?? 0) ?></div>
                <div class="text-gray-600">ນັກສຶກສາທັງໝົດ</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-green-600 mb-2"><?= number_format($stats['majors'] ?? 0) ?></div>
                <div class="text-gray-600">ສາຂາວິຊາ</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-purple-600 mb-2"><?= number_format($stats['years'] ?? 0) ?></div>
                <div class="text-gray-600">ປີການສຶກສາ</div>
            </div>
            <div class="text-center">
                <div class="text-4xl font-bold text-orange-600 mb-2"><?= number_format($stats['users'] ?? 0) ?></div>
                <div class="text-gray-600">ຜູ້ໃຊ້ລະບົບ</div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Quick Actions Section -->
<div class="py-20 bg-gradient-to-r from-gray-800 to-gray-900 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-4xl font-bold mb-8">ເລີ່ມຕົ້ນໃຊ້ງານ</h2>
        <p class="text-xl mb-12 opacity-90 max-w-2xl mx-auto">
            ເລືອກການດຳເນີນການທີ່ທ່ານຕ້ອງການ
        </p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 max-w-4xl mx-auto">
            <?php if ($is_logged_in): ?>
                <a href="<?= BASE_URL ?>index.php?page=register" 
                   class="bg-blue-600 hover:bg-blue-700 p-6 rounded-xl transition-colors duration-300 transform hover:-translate-y-2">
                    <i class="fas fa-user-plus text-3xl mb-4"></i>
                    <h3 class="font-bold mb-2">ລົງທະບຽນໃໝ່</h3>
                    <p class="text-sm opacity-80">ເພີ່ມນັກສຶກສາໃໝ່</p>
                </a>
                
                <a href="<?= BASE_URL ?>index.php?page=students" 
                   class="bg-green-600 hover:bg-green-700 p-6 rounded-xl transition-colors duration-300 transform hover:-translate-y-2">
                    <i class="fas fa-users text-3xl mb-4"></i>
                    <h3 class="font-bold mb-2">ລາຍຊື່ນັກສຶກສາ</h3>
                    <p class="text-sm opacity-80">ເບິ່ງຂໍ້ມູນທັງໝົດ</p>
                </a>
                
                <a href="<?= BASE_URL ?>index.php?page=qrcode" 
                   class="bg-purple-600 hover:bg-purple-700 p-6 rounded-xl transition-colors duration-300 transform hover:-translate-y-2">
                    <i class="fas fa-qrcode text-3xl mb-4"></i>
                    <h3 class="font-bold mb-2">QR Code</h3>
                    <p class="text-sm opacity-80">ສ້າງ QR Code</p>
                </a>
                
                <a href="<?= BASE_URL ?>index.php?page=dashboard" 
                   class="bg-orange-600 hover:bg-orange-700 p-6 rounded-xl transition-colors duration-300 transform hover:-translate-y-2">
                    <i class="fas fa-tachometer-alt text-3xl mb-4"></i>
                    <h3 class="font-bold mb-2">Dashboard</h3>
                    <p class="text-sm opacity-80">ຫນ້າຄວບຄຸມ</p>
                </a>
            <?php else: ?>
                <a href="<?= BASE_URL ?>index.php?page=login" 
                   class="bg-blue-600 hover:bg-blue-700 p-6 rounded-xl transition-colors duration-300 transform hover:-translate-y-2">
                    <i class="fas fa-sign-in-alt text-3xl mb-4"></i>
                    <h3 class="font-bold mb-2">ເຂົ້າສູ່ລະບົບ</h3>
                    <p class="text-sm opacity-80">ສຳລັບຜູ້ດູແລລະບົບ</p>
                </a>
                
                <a href="<?= BASE_URL ?>index.php?page=register" 
                   class="bg-green-600 hover:bg-green-700 p-6 rounded-xl transition-colors duration-300 transform hover:-translate-y-2">
                    <i class="fas fa-user-plus text-3xl mb-4"></i>
                    <h3 class="font-bold mb-2">ລົງທະບຽນ</h3>
                    <p class="text-sm opacity-80">ລົງທະບຽນນັກສຶກສາໃໝ່</p>
                </a>
                
                <a href="<?= BASE_URL ?>index.php?page=user-register" 
                   class="bg-purple-600 hover:bg-purple-700 p-6 rounded-xl transition-colors duration-300 transform hover:-translate-y-2">
                    <i class="fas fa-user-shield text-3xl mb-4"></i>
                    <h3 class="font-bold mb-2">ສະໝັກບັນຊີ</h3>
                    <p class="text-sm opacity-80">ສຳລັບຜູ້ດູແລ</p>
                </a>
                
                <a href="<?= BASE_URL ?>index.php?page=qr-examples" 
                   class="bg-orange-600 hover:bg-orange-700 p-6 rounded-xl transition-colors duration-300 transform hover:-translate-y-2">
                    <i class="fas fa-qrcode text-3xl mb-4"></i>
                    <h3 class="font-bold mb-2">ຕົວຢ່າງ QR</h3>
                    <p class="text-sm opacity-80">ເບິ່ງຕົວຢ່າງ</p>
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Footer Section -->


<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes float-slow {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

@keyframes float-medium {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-15px); }
}

@keyframes float-fast {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

.animate-fade-in {
    animation: fade-in 1s ease-out;
}

.animate-float-slow {
    animation: float-slow 6s ease-in-out infinite;
}

.animate-float-medium {
    animation: float-medium 4s ease-in-out infinite;
}

.animate-float-fast {
    animation: float-fast 3s ease-in-out infinite;
}
</style>