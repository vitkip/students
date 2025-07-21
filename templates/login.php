<?php
// filepath: c:\xampp\htdocs\register-learning\templates\login.php
// Login page template

// ເລີ່ມ session ກ່ອນອື່ນ
safeSessionStart();

// ປ້ອງກັນການ output ໂດຍບໍ່ຈຳເປັນ
ob_start();

// ຈັດການການ login ກ່ອນທີ່ຈະມີ output ໃດໆ
$should_redirect = false;
$redirect_url = '';
$error_message = '';
$success_message = '';

// Check if already logged in
if (function_exists('isLoggedIn') && isLoggedIn()) {
    $should_redirect = true;
    $redirect_url = BASE_URL . "?page=dashboard";
}

// Check for logout message
if (isset($_GET['logout'])) {
    $success_message = 'ອອກຈາກລະບົບສຳເລັດແລ້ວ';
}

// Check for timeout message
if (isset($_GET['timeout'])) {
    $error_message = 'ໝົດເວລາການເຂົ້າໃຊ້ລະບົບ ກະລຸນາເຂົ້າສູ່ລະບົບໃໝ່';
}

// ຈັດການການ submit form
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    require_once BASE_PATH . '/src/classes/User.php';
    
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error_message = 'ກະລຸນາປ້ອນຊື່ຜູ້ໃຊ້ແລະລະຫັດຜ່ານ';
    } else {
        // Debug logging
        error_log("Login attempt for username: " . $username);
        
        // Attempt authentication
        $user = new User($db);
        $userData = $user->authenticate($username, $password);
        
        if ($userData) {
            // Login successful
            error_log("Login successful for user: " . $username);
            
            if (loginUser($userData)) {
                // ຕັ້ງຄ່າສຳລັບ redirect
                $should_redirect = true;
                $redirect_url = BASE_URL . ($_GET['redirect'] ?? '?page=dashboard');
            } else {
                $error_message = 'ເກີດຂໍ້ຜິດພາດໃນການເຂົ້າສູ່ລະບົບ';
            }
        } else {
            error_log("Login failed for user: " . $username);
            $error_message = 'ຊື່ຜູໃຊ້ຫຼືລະຫັດຜ່ານບໍ່ຖືກຕ້ອງ';
        }
    }
}

// ຖ້າຕ້ອງ redirect ໃຫ້ໃຊ້ JavaScript
if ($should_redirect) {
    ob_end_clean(); // ລຶບ output buffer
    echo "<!DOCTYPE html>";
    echo "<html><head><meta charset='UTF-8'>";
    echo "<script>window.location.href = '" . htmlspecialchars($redirect_url) . "';</script>";
    echo "<meta http-equiv='refresh' content='0;url=" . htmlspecialchars($redirect_url) . "'>";
    echo "</head><body>";
    echo "<p>ກຳລັງນຳທາງ... <a href='" . htmlspecialchars($redirect_url) . "'>ກົດທີ່ນີ້ຖ້າບໍ່ຖືກນຳທາງໂດຍອັດຕະໂນມັດ</a></p>";
    echo "</body></html>";
    exit;
}

// ເລີ່ມ output ຫນ້າ login
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ເຂົ້າສູ່ລະບົບ - ລະບົບລົງທະບຽນນັກສຶກສາ</title>
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Google Fonts for Lao -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Noto Sans Lao', sans-serif;
        }
        
     
        
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
        }
        
        .login-form-input:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.3);
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.4);
        }
        
        .floating-animation {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
    </style>
</head>
<body>
    <div class="login-container flex items-center justify-center p-4">
        <div class="w-full max-w-md">
            <!-- Login Card -->
            <div class="login-card rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-amber-500 to-orange-500 p-8 text-center text-white">
                    <div class="floating-animation">
                        <i class="fas fa-graduation-cap text-5xl mb-4"></i>
                    </div>
                    <h1 class="text-3xl font-bold mb-2">ເຂົ້າສູ່ລະບົບ</h1>
                    <p class="text-amber-100">ລະບົບລົງທະບຽນນັກສຶກສາ</p>
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
                    
                    <!-- Login Form -->
                    <form method="POST" action="" class="space-y-6">
                        <!-- Username Field -->
                        <div class="space-y-2">
                            <label for="username" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-user mr-2 text-amber-500"></i>ຊື່ຜູ້ໃຊ້
                            </label>
                            <input type="text" 
                                   id="username" 
                                   name="username" 
                                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                   class="login-form-input w-full px-4 py-3 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-amber-500 transition-all duration-300"
                                   placeholder="ປ້ອນຊື່ຜູ້ໃຊ້"
                                   required>
                        </div>
                        
                        <!-- Password Field -->
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-semibold text-gray-700">
                                <i class="fas fa-lock mr-2 text-amber-500"></i>ລະຫັດຜ່ານ
                            </label>
                            <div class="relative">
                                <input type="password" 
                                       id="password" 
                                       name="password" 
                                       class="login-form-input w-full px-4 py-3 pr-12 border-2 border-gray-200 rounded-lg focus:outline-none focus:border-amber-500 transition-all duration-300"
                                       placeholder="ປ້ອນລະຫັດຜ່ານ"
                                       required>
                                <button type="button" 
                                        onclick="togglePassword()"
                                        class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-500 hover:text-amber-500 transition-colors">
                                    <i id="password-icon" class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="pt-4">
                            <button type="submit" 
                                    name="login"
                                    class="login-btn w-full bg-gradient-to-r from-amber-500 to-orange-500 text-white py-3 px-6 rounded-lg font-semibold hover:from-amber-600 hover:to-orange-600 transition-all duration-300 transform">
                                <i class="fas fa-sign-in-alt mr-2"></i>ເຂົ້າສູ່ລະບົບ
                            </button>
                        </div>
                    </form>
                    
                    <!-- Demo Credentials Info -->
                    <div class="mt-8 p-4 bg-gray-50 rounded-lg border-l-4 border-blue-400">
                        <h3 class="text-sm font-semibold text-gray-700 mb-2">
                            <i class="fas fa-info-circle mr-2 text-blue-500"></i>ຂໍ້ມູນທົດສອບ:
                        </h3>
                        <div class="text-xs text-gray-600 space-y-1">
                            <div><strong>ຜູ້ຄຸ້ມຄອງ:</strong> username: <code class="bg-gray-200 px-1 rounded">admin</code>, password: <code class="bg-gray-200 px-1 rounded">123456</code></div>
                            <div><strong>ຜູ້ໃຊ້:</strong> username: <code class="bg-gray-200 px-1 rounded">user</code>, password: <code class="bg-gray-200 px-1 rounded">123456</code></div>
                        </div>
                    </div>
                    
                    <!-- Register Link -->
                    <div class="mt-6 text-center">
                        <p class="text-gray-600">ຍັງບໍ່ມີບັນຊີ? 
                            <a href="<?= BASE_URL ?>?page=user-register" class="text-amber-500 hover:text-amber-600 font-semibold transition-colors">
                                ລົງທະບຽນໃໝ່
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
    <script>
        // Toggle password visibility
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }
        
        // Auto-focus username field
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
        
        // Add enter key support
        document.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                const form = document.querySelector('form');
                if (form) form.submit();
            }
        });
    </script>
</body>
</html>