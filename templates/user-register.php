<?php
// filepath: c:\xampp\htdocs\register-learning\templates\register.php
// Registration page template

// Check if already logged in
if (function_exists('isLoggedIn') && isLoggedIn()) {
    header("Location: " . BASE_URL . "?page=dashboard");
    exit;
}

// Handle registration form submission
$error_message = '';
$success_message = '';
$form_data = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['register'])) {
    require_once BASE_PATH . '/src/classes/User.php';
    
    // Get form data
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    // Store form data for repopulating on error
    $form_data = [
        'username' => $username,
        'full_name' => $full_name,
        'email' => $email
    ];
    
    // Validation
    if (empty($username) || empty($password) || empty($confirm_password) || empty($full_name)) {
        $error_message = 'ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນ';
    } elseif (strlen($username) < 3) {
        $error_message = 'ຊື່ຜູ້ໃຊ້ຕ້ອງມີຢ່າງໜ້ອຍ 3 ຕົວອັກສອນ';
    } elseif (strlen($password) < 6) {
        $error_message = 'ລະຫັດຜ່ານຕ້ອງມີຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ';
    } elseif ($password !== $confirm_password) {
        $error_message = 'ລະຫັດຜ່ານແລະການຢືນຢັນລະຫັດຜ່ານບໍ່ຕົງກັນ';
    } elseif (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'ອີເມວບໍ່ຖືກຕ້ອງ';
    } else {
        // Check if username already exists
        $user = new User($db);
        
        if ($user->usernameExists($username)) {
            $error_message = 'ຊື່ຜູ້ໃຊ້ນີ້ມີຄົນໃຊ້ແລ້ວ ກະລຸນາເລືອກຊື່ອື່ນ';
        } else {
            // Create new user
            if ($user->create($username, $password, 'user', $full_name, $email)) {
                $success_message = 'ລົງທະບຽນສຳເລັດ! ກະລຸນາເຂົ້າສູ່ລະບົບ';
                $form_data = []; // Clear form data on success
            } else {
                $error_message = 'ເກີດຂໍ້ຜິດພາດໃນການລົງທະບຽນ ກະລຸນາລອງໃໝ່';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລົງທະບຽນ - ລະບົບລົງທະບຽນນັກສຶກສາ</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts for Lao -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Noto Sans Lao', sans-serif;
        }
        
   
        
        .register-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .register-form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        }
        
        .register-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
        }
        
        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .password-strength {
            height: 4px;
            border-radius: 2px;
            transition: all 0.3s ease;
        }
        
        .strength-weak { background: #ef4444; width: 25%; }
        .strength-fair { background: #f59e0b; width: 50%; }
        .strength-good { background: #10b981; width: 75%; }
        .strength-strong { background: #059669; width: 100%; }
    </style>
</head>
<body>
    <div class="register-container flex items-center justify-center p-4">
        <div class="w-full max-w-lg">
            <!-- Register Card -->
            <div class="register-card rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-8 text-center text-white">
                    <div class="floating-animation">
                        <i class="fas fa-user-plus text-5xl mb-4"></i>
                    </div>
                    <h1 class="text-3xl font-bold mb-2">ລົງທະບຽນ</h1>
                    <p class="text-emerald-100">ສ້າງບັນຊີໃໝ່</p>
                </div>
                
                <!-- Form Container -->
                <div class="p-8">
                    <!-- Error/Success Messages -->
                    <?php if (!empty($error_message)): ?>
                        <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded">
                            <div class="flex items-center">
                                <i class="fas fa-exclamation-triangle mr-3"></i>
                                <span><?= htmlspecialchars($error_message) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded">
                            <div class="flex items-center">
                                <i class="fas fa-check-circle mr-3"></i>
                                <span><?= htmlspecialchars($success_message) ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Registration Form -->
                    <form method="POST" action="" class="space-y-6" id="registerForm">
                        <!-- Username Field -->
                        <div class="space-y-2">
                            <label for="username" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-user mr-2 text-emerald-500"></i>ຊື່ຜູ້ໃຊ້ <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   value="<?= htmlspecialchars($form_data['username'] ?? '') ?>"
                                   class="register-form-input w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500 transition-all duration-300"
                                   placeholder="ປ້ອນຊື່ຜູ້ໃຊ້"
                                   minlength="3"
                                   required>
                            <p class="text-xs text-gray-500">ຊື່ຜູ້ໃຊ້ຕ້ອງມີຢ່າງໜ້ອຍ 3 ຕົວອັກສອນ</p>
                        </div>
                        
                        <!-- Full Name Field -->
                        <div class="space-y-2">
                            <label for="full_name" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-id-card mr-2 text-emerald-500"></i>ຊື່ເຕັມ <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   id="full_name" 
                                   name="full_name" 
                                   value="<?= htmlspecialchars($form_data['full_name'] ?? '') ?>"
                                   class="register-form-input w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500 transition-all duration-300"
                                   placeholder="ປ້ອນຊື່ເຕັມ"
                                   required>
                        </div>
                        
                        <!-- Email Field -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-envelope mr-2 text-emerald-500"></i>ອີເມວ
                            </label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="<?= htmlspecialchars($form_data['email'] ?? '') ?>"
                                   class="register-form-input w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500 transition-all duration-300"
                                   placeholder="ປ້ອນອີເມວ (ບໍ່ບັງຄັບ)">
                        </div>
                        
                        <!-- Password Field -->
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-lock mr-2 text-emerald-500"></i>ລະຫັດຜ່ານ <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="register-form-input w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500 transition-all duration-300"
                                       placeholder="ປ້ອນລະຫັດຜ່ານ"
                                       minlength="6"
                                       onkeyup="checkPasswordStrength()"
                                       required>
                                <button type="button" 
                                        onclick="togglePassword('password', 'password-icon')"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-emerald-500 transition-colors">
                                    <i id="password-icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                            <!-- Password Strength Indicator -->
                            <div class="w-full bg-gray-200 rounded-full h-1">
                                <div id="password-strength" class="password-strength bg-gray-200"></div>
                            </div>
                            <p id="password-text" class="text-xs text-gray-500">ລະຫັດຜ່ານຕ້ອງມີຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ</p>
                        </div>
                        
                        <!-- Confirm Password Field -->
                        <div class="space-y-2">
                            <label for="confirm_password" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-lock mr-2 text-emerald-500"></i>ຢືນຢັນລະຫັດຜ່ານ <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="confirm_password" 
                                       name="confirm_password" 
                                       class="register-form-input w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-emerald-500 transition-all duration-300"
                                       placeholder="ປ້ອນລະຫັດຜ່ານອີກຄັ້ງ"
                                       onkeyup="checkPasswordMatch()"
                                       required>
                                <button type="button" 
                                        onclick="togglePassword('confirm_password', 'confirm-password-icon')"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-emerald-500 transition-colors">
                                    <i id="confirm-password-icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                            <p id="password-match-text" class="text-xs text-gray-500">ລະຫັດຜ່ານຕ້ອງຕົງກັນ</p>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" 
                                    name="register"
                                    id="register-btn"
                                    class="register-btn w-full bg-gradient-to-r from-emerald-500 to-green-600 text-white py-3 px-6 rounded-lg font-semibold hover:from-emerald-600 hover:to-green-700 transition-all duration-300 transform">
                                <i class="fas fa-user-plus mr-2"></i>ລົງທະບຽນ
                            </button>
                        </div>
                    </form>
                    
                    <!-- Login Link -->
                    <div class="mt-6 text-center">
                        <p class="text-gray-600">ມີບັນຊີແລ້ວ? 
                            <a href="<?= BASE_URL ?>?page=login" class="text-emerald-500 hover:text-emerald-600 font-semibold transition-colors">
                                ເຂົ້າສູ່ລະບົບ
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Toggle password visibility
        function togglePassword(inputId, iconId) {
            const passwordInput = document.getElementById(inputId);
            const passwordIcon = document.getElementById(iconId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }
        
        // Check password strength
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthBar = document.getElementById('password-strength');
            const strengthText = document.getElementById('password-text');
            
            let strength = 0;
            let text = '';
            let className = '';
            
            if (password.length >= 6) strength++;
            if (password.match(/[a-z]/)) strength++;
            if (password.match(/[A-Z]/)) strength++;
            if (password.match(/[0-9]/)) strength++;
            if (password.match(/[^a-zA-Z0-9]/)) strength++;
            
            switch (strength) {
                case 0:
                case 1:
                    className = 'strength-weak';
                    text = 'ລະຫັດຜ່ານອ່ອນ';
                    break;
                case 2:
                    className = 'strength-fair';
                    text = 'ລະຫັດຜ່ານປານກາງ';
                    break;
                case 3:
                case 4:
                    className = 'strength-good';
                    text = 'ລະຫັດຜ່ານດີ';
                    break;
                case 5:
                    className = 'strength-strong';
                    text = 'ລະຫັດຜ່ານແຂງແຮງ';
                    break;
            }
            
            strengthBar.className = `password-strength ${className}`;
            strengthText.textContent = text;
            strengthText.className = `text-xs ${className.includes('weak') ? 'text-red-500' : className.includes('fair') ? 'text-yellow-500' : 'text-green-500'}`;
        }
        
        // Check password match
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchText = document.getElementById('password-match-text');
            
            if (confirmPassword === '') {
                matchText.textContent = 'ລະຫັດຜ່ານຕ້ອງຕົງກັນ';
                matchText.className = 'text-xs text-gray-500';
            } else if (password === confirmPassword) {
                matchText.textContent = 'ລະຫັດຜ່ານຕົງກັນ ✓';
                matchText.className = 'text-xs text-green-500';
            } else {
                matchText.textContent = 'ລະຫັດຜ່ານບໍ່ຕົງກັນ';
                matchText.className = 'text-xs text-red-500';
            }
        }
        
        // Form validation
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                e.preventDefault();
                alert('ລະຫັດຜ່ານແລະການຢືນຢັນລະຫັດຜ່ານບໍ່ຕົງກັນ');
                return;
            }
            
            if (password.length < 6) {
                e.preventDefault();
                alert('ລະຫັດຜ່ານຕ້ອງມີຢ່າງໜ້ອຍ 6 ຕົວອັກສອນ');
                return;
            }
        });
        
        // Auto-focus username field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
    </script>
</body>
</html>