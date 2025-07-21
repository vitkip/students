<?php
// filepath: c:\xampp\htdocs\register-learning\templates\logout.php
// Logout page template

// ป้องกันการเข้าถึงโดยตรง
if (!defined('BASE_PATH')) {
    header('Location: ../public/index.php?page=logout');
    exit;
}

// Include authentication helpers
require_once BASE_PATH . '/src/helpers/auth.php';

// เริ่ม session อย่างปลอดภัย
safeSessionStart();

// ตรวจสอบว่าผู้ใช้เข้าสู่ระบบอยู่หรือไม่
$was_logged_in = isLoggedIn();
$username = getCurrentUsername();

// ทำการ logout
logoutUser();

// ข้อความยืนยันการ logout
$logout_message = $was_logged_in ? 
    "ອອກຈາກລະບົບສຳເລັດແລ້ວ" . ($username ? " (" . htmlspecialchars($username) . ")" : "") :
    "ທ່ານຍັງບໍ່ໄດ້ເຂົ້າສູ່ລະບົບ";
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ອອກຈາກລະບົບ - ລະບົບລົງທະບຽນນັກສຶກສາ</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts for Lao -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Auto redirect after 3 seconds -->
    <meta http-equiv="refresh" content="3;url=<?= BASE_URL ?>?page=login">
    
    <style>
        body {
            font-family: 'Noto Sans Lao', sans-serif;
        }
        
        .logout-container {
            background: linear-gradient(135deg, #6b7280 0%, #374151 100%);
            min-height: 100vh;
        }
        
        .logout-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .countdown {
            animation: countdown 3s linear;
        }
        
        @keyframes countdown {
            from { width: 100%; }
            to { width: 0%; }
        }
    </style>
</head>
<body>
    <div class="logout-container flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Logout Card -->
            <div class="logout-card rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-gray-500 to-gray-700 p-8 text-center text-white">
                    <div class="floating-animation">
                        <i class="fas fa-sign-out-alt text-5xl mb-4"></i>
                    </div>
                    <h1 class="text-3xl font-bold mb-2">ອອກຈາກລະບົບ</h1>
                    <p class="text-gray-200">ລະບົບລົງທະບຽນນັກສຶກສາ</p>
                </div>
                
                <!-- Content -->
                <div class="p-8 text-center">
                    <!-- Success Message -->
                    <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                        <div class="flex items-center justify-center">
                            <i class="fas fa-check-circle mr-3 text-xl"></i>
                            <span class="font-medium"><?= $logout_message ?></span>
                        </div>
                    </div>
                    
                    <!-- Countdown Information -->
                    <div class="mb-6">
                        <p class="text-gray-600 mb-4">
                            <i class="fas fa-clock mr-2 text-blue-500"></i>
                            ກຳລັງນຳທາງກັບໄປຫນ້າເຂົ້າສູ່ລະບົບໃນ <span id="countdown">3</span> ວິນາທີ
                        </p>
                        
                        <!-- Progress Bar -->
                        <div class="w-full bg-gray-200 rounded-full h-2 mb-4">
                            <div class="countdown bg-gradient-to-r from-blue-500 to-purple-600 h-2 rounded-full"></div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="space-y-3">
                        <a href="<?= BASE_URL ?>?page=login" 
                           class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-blue-500 to-blue-600 text-white font-semibold rounded-lg hover:from-blue-600 hover:to-blue-700 transition-all duration-300 transform hover:-translate-y-1">
                            <i class="fas fa-sign-in-alt mr-2"></i>ເຂົ້າສູ່ລະບົບໃໝ່
                        </a>
                        
                        <a href="<?= BASE_URL ?>?page=user-register" 
                           class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-green-500 to-green-600 text-white font-semibold rounded-lg hover:from-green-600 hover:to-green-700 transition-all duration-300 transform hover:-translate-y-1">
                            <i class="fas fa-user-plus mr-2"></i>ລົງທະບຽນບັນຊີໃໝ່
                        </a>
                        
                        <a href="<?= BASE_URL ?>" 
                           class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-gray-400 to-gray-500 text-white font-semibold rounded-lg hover:from-gray-500 hover:to-gray-600 transition-all duration-300 transform hover:-translate-y-1">
                            <i class="fas fa-home mr-2"></i>ກັບໄປໜ້າຫຼັກ
                        </a>
                    </div>
                    
                    <!-- Security Notice -->
                    <div class="mt-8 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded">
                        <div class="flex items-center">
                            <i class="fas fa-shield-alt mr-3 text-yellow-600"></i>
                            <div class="text-left">
                                <p class="text-sm font-medium text-yellow-800">ການແນະນຳດ້ານຄວາມປອດໄພ:</p>
                                <p class="text-xs text-yellow-700 mt-1">
                                    ກະລຸນາປິດບຣາວເຊີຍ໌ຫຼືລົງອອກຈາກບັນຊີເມື່ອໃຊ້ງານເສັດສິ້ນ
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer -->
            <div class="text-center mt-6">
                <p class="text-white text-sm opacity-80">© 2024 ວິທະຍາໄລການສຶກສາ</p>
            </div>
        </div>
    </div>
    
    <script>
        // Countdown timer
        let timeLeft = 3;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(function() {
            timeLeft--;
            countdownElement.textContent = timeLeft;
            
            if (timeLeft <= 0) {
                clearInterval(timer);
                // Redirect to login page
                window.location.href = '<?= BASE_URL ?>?page=login';
            }
        }, 1000);
        
        // Allow user to cancel auto-redirect by clicking anywhere
        let autoRedirectCancelled = false;
        
        document.addEventListener('click', function(e) {
            if (!autoRedirectCancelled && e.target.tagName !== 'A') {
                autoRedirectCancelled = true;
                clearInterval(timer);
                countdownElement.textContent = 'ຍົກເລີກ';
                
                // Show message
                const message = document.createElement('div');
                message.className = 'text-sm text-gray-600 mt-2';
                message.innerHTML = '<i class="fas fa-info-circle mr-1"></i>ຍົກເລີກການນຳທາງອັດຕະໂນມັດແລ້ວ';
                countdownElement.parentNode.appendChild(message);
            }
        });
        
        // Prevent form submission and other unwanted behaviors
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>