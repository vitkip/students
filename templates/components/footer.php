<?php
// ตรวจสอบว่ามีการเรียกใช้ผ่าน index.php หรือไม่
if (!defined('BASE_PATH')) {
    exit('Direct access not allowed');
}
?>

    </main>
    
    <!-- Back to Top Button -->
    <button id="backToTop" 
            class="fixed bottom-4 right-4 bg-amber-500 hover:bg-amber-600 text-white p-3 rounded-full shadow-lg transition-all duration-300 transform hover:scale-110 opacity-0 invisible z-40"
            onclick="scrollToTop()"
            aria-label="กลับไปด้านบน">
        <i class="fas fa-chevron-up"></i>
    </button>
    
    <!-- Footer -->
    <footer class="mt-16 bg-gradient-to-br from-gray-800 via-gray-900 to-black text-white">
        <div class="max-w-7xl mx-auto">
            <!-- Main Footer Content -->
            <div class="px-4 py-12 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    <!-- Company Info -->
                    <div class="lg:col-span-2">
                        <div class="flex items-center mb-6">
                            <div class="w-12 h-12 bg-gradient-to-br from-amber-400 to-orange-500 rounded-lg flex items-center justify-center mr-4">
                                <i class="fas fa-graduation-cap text-white text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-white">ລະບົບລົງທະບຽນນັກສຶກສາ</h3>
                                <p class="text-sm text-gray-300">Student Registration System</p>
                            </div>
                        </div>
                        <p class="text-gray-300 text-sm leading-relaxed mb-6">
                            ລະບົບຄຸ້ມຄອງຂໍ້ມູນນັກສຶກສາແບບຄົບວົງຈອນ ທີ່ອອກແບບມາສຳລັບສະຖາບັນການສຶກສາ 
                            ເພື່ອອຳນວຍຄວາມສະດວກໃນການລົງທະບຽນ ແລະ ຄຸ້ມຄອງຂໍ້ມູນນັກສຶກສາ
                        </p>
                        
                        <!-- Statistics -->
                        <div class="grid grid-cols-2 gap-4">
                            <?php
                            $stats = [];
                            if (isset($db) && $db) {
                                try {
                                    $stmt = $db->query("SELECT COUNT(*) as total FROM students");
                                    $stats['students'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                                    
                                    $stmt = $db->query("SELECT COUNT(*) as total FROM majors");
                                    $stats['majors'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0;
                                } catch (Exception $e) {
                                    $stats = ['students' => 0, 'majors' => 0];
                                }
                            }
                            ?>
                            <div class="text-center p-3 bg-gray-800 rounded-lg">
                                <div class="text-2xl font-bold text-amber-400"><?= number_format($stats['students'] ?? 0) ?></div>
                                <div class="text-xs text-gray-400">ນັກສຶກສາທັງໝົດ</div>
                            </div>
                            <div class="text-center p-3 bg-gray-800 rounded-lg">
                                <div class="text-2xl font-bold text-amber-400"><?= number_format($stats['majors'] ?? 0) ?></div>
                                <div class="text-xs text-gray-400">ສາຂາວິຊາ</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Links -->
                    <div>
                        <h4 class="text-lg font-semibold text-white mb-6 flex items-center">
                            <i class="fas fa-link mr-2 text-amber-400"></i>ທາງລັດ
                        </h4>
                        <ul class="space-y-3">
                            <li>
                                <a href="<?= BASE_URL ?>" 
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 flex items-center group">
                                    <i class="fas fa-home mr-2 text-xs w-4 group-hover:text-amber-400"></i>
                                    <span>ໜ້າຫຼັກ</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>?page=register" 
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 flex items-center group">
                                    <i class="fas fa-user-plus mr-2 text-xs w-4 group-hover:text-amber-400"></i>
                                    <span>ລົງທະບຽນນັກສຶກສາ</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>?page=students" 
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 flex items-center group">
                                    <i class="fas fa-users mr-2 text-xs w-4 group-hover:text-amber-400"></i>
                                    <span>ລາຍຊື່ນັກສຶກສາ</span>
                                </a>
                            </li>
                            <li>
                                <a href="<?= BASE_URL ?>?page=qrcode" 
                                   class="text-gray-300 hover:text-amber-400 transition-colors duration-200 flex items-center group">
                                    <i class="fas fa-qrcode mr-2 text-xs w-4 group-hover:text-amber-400"></i>
                                    <span>QR Code Generator</span>
                                </a>
                            </li>
                            <?php if (function_exists('isLoggedIn') && isLoggedIn()): ?>
                                <li>
                                    <a href="<?= BASE_URL ?>?page=dashboard" 
                                       class="text-gray-300 hover:text-amber-400 transition-colors duration-200 flex items-center group">
                                        <i class="fas fa-tachometer-alt mr-2 text-xs w-4 group-hover:text-amber-400"></i>
                                        <span>Dashboard</span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <!-- Contact & Support -->
                    <div>
                        <h4 class="text-lg font-semibold text-white mb-6 flex items-center">
                            <i class="fas fa-headset mr-2 text-amber-400"></i>ຕິດຕໍ່ & ສະໜັບສະໜູນ
                        </h4>
                        <div class="space-y-3">
                            <div class="flex items-start">
                                <i class="fas fa-map-marker-alt text-amber-400 mr-3 mt-1 text-sm"></i>
                                <div class="text-sm text-gray-300">
                                    <p>ວິທະຍາໄລຄູສົງ ອົງຕື້</p>
                                    <p>ວັດອົງຕື້ວໍຣະມະຫາວິຫານ ນະຄອນຫຼວງວຽງຈັນ, ລາວ</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-envelope text-amber-400 mr-3 text-sm"></i>
                                <a href="mailto:info@college.edu.la" class="text-sm text-gray-300 hover:text-amber-400 transition-colors">
                                    info@ongtue-ttc.edu.la
                                </a>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-phone text-amber-400 mr-3 text-sm"></i>
                                <span class="text-sm text-gray-300">+856 20 7777 2338</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-clock text-amber-400 mr-3 text-sm"></i>
                                <span class="text-sm text-gray-300">ຈັນ - ສຸກ: 8:00 - 17:00</span>
                            </div>
                        </div>
                        
                        <!-- Social Media -->
                        <div class="mt-6">
                            <h5 class="text-sm font-medium text-gray-400 mb-3">ຕິດຕາມເຮົາ</h5>
                            <div class="flex space-x-3">
                                <a href="#" class="w-10 h-10 bg-blue-600 hover:bg-blue-700 rounded-full flex items-center justify-center transition-colors group">
                                    <i class="fab fa-facebook-f text-white text-sm group-hover:scale-110 transition-transform"></i>
                                </a>
                                <a href="#" class="w-10 h-10 bg-green-500 hover:bg-green-600 rounded-full flex items-center justify-center transition-colors group">
                                    <i class="fab fa-whatsapp text-white text-sm group-hover:scale-110 transition-transform"></i>
                                </a>
                                <a href="#" class="w-10 h-10 bg-red-600 hover:bg-red-700 rounded-full flex items-center justify-center transition-colors group">
                                    <i class="fab fa-youtube text-white text-sm group-hover:scale-110 transition-transform"></i>
                                </a>
                                <a href="#" class="w-10 h-10 bg-blue-400 hover:bg-blue-500 rounded-full flex items-center justify-center transition-colors group">
                                    <i class="fab fa-telegram text-white text-sm group-hover:scale-110 transition-transform"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Footer Bottom -->
            <div class="border-t border-gray-700">
                <div class="px-4 py-6 sm:px-6 lg:px-8">
                    <div class="md:flex md:items-center md:justify-between">
                        <div class="text-center md:text-left">
                            <p class="text-sm text-gray-400">
                                &copy; <?= date('Y') ?> ລະບົບລົງທະບຽນນັກສຶກສາ. ສະຫງວນລິຂະສິດທັງໝົດ.
                            </p>
                            <p class="text-xs text-gray-500 mt-1">
                                ພັດທະນາໂດຍ : ປອ ອານັນທະສັກ ພັດທະສີລາ
                            </p>
                        </div>
                        <div class="mt-4 md:mt-0 text-center md:text-right">
                            <div class="flex justify-center md:justify-end space-x-4 text-xs text-gray-500">
                                <a href="#" class="hover:text-amber-400 transition-colors">ນະໂຍບາຍຄວາມເປັນສ່ວນຕົວ</a>
                                <span>|</span>
                                <a href="#" class="hover:text-amber-400 transition-colors">ເງື່ອນໄຂການໃຊ້ງານ</a>
                                <span>|</span>
                                <a href="#" class="hover:text-amber-400 transition-colors">ວິທີການໃຊ້ງານ</a>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">
                                ເວີຊັນ 1.0.0 | ສ້າງດ້ວຍ <i class="fas fa-heart text-red-500 mx-1"></i> ໃນປະເທດລາວ
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Custom JavaScript -->
    <script src="<?= BASE_URL ?>public/assets/js/form.js"></script>
    
    <script>
        // Back to top functionality
        function scrollToTop() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
        
        // Show/hide back to top button
        window.addEventListener('scroll', function() {
            const backToTop = document.getElementById('backToTop');
            if (window.pageYOffset > 300) {
                backToTop.classList.remove('opacity-0', 'invisible');
                backToTop.classList.add('opacity-100', 'visible');
            } else {
                backToTop.classList.add('opacity-0', 'invisible');
                backToTop.classList.remove('opacity-100', 'visible');
            }
        });
        
        // Enhanced SweetAlert for Messages
        <?php if (isset($_SESSION['message'])): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const messageType = '<?= $_SESSION['message_type'] ?? 'info' ?>';
            const message = <?= json_encode($_SESSION['message']) ?>;
            
            let icon = 'info';
            let title = 'ແຈ້ງເຕືອນ';
            let confirmButtonColor = '#f59e0b';
            
            switch(messageType) {
                case 'success':
                    icon = 'success';
                    title = 'ສຳເລັດ!';
                    confirmButtonColor = '#10b981';
                    break;
                case 'error':
                    icon = 'error';
                    title = 'ຂໍ້ຜິດພາດ!';
                    confirmButtonColor = '#ef4444';
                    break;
                case 'warning':
                    icon = 'warning';
                    title = 'ແຈ້ງເຕືອນ!';
                    confirmButtonColor = '#f59e0b';
                    break;
            }
            
            Swal.fire({
                title: title,
                text: message,
                icon: icon,
                confirmButtonText: 'ຮູ້ແລ້ວ',
                confirmButtonColor: confirmButtonColor,
                timer: messageType === 'success' ? 4000 : null,
                timerProgressBar: messageType === 'success',
                backdrop: 'rgba(0,0,0,0.7)',
                allowOutsideClick: true,
                showClass: {
                    popup: 'animate__animated animate__fadeInDown animate__faster'
                },
                hideClass: {
                    popup: 'animate__animated animate__fadeOutUp animate__faster'
                },
                customClass: {
                    container: 'font-lao',
                    popup: 'rounded-xl',
                    title: 'text-lg font-semibold',
                    content: 'text-sm',
                    confirmButton: 'px-6 py-2 font-medium rounded-lg'
                }
            });
        });
        <?php 
            // ลบ session message หลังแสดงแล้ว
            unset($_SESSION['message']);
            unset($_SESSION['message_type']);
        endif; 
        ?>
        
        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Print functionality
        function printPage() {
            window.print();
        }
        
        // Copy to clipboard functionality
        function copyToClipboard(text) {
            navigator.clipboard.writeText(text).then(function() {
                Swal.fire({
                    icon: 'success',
                    title: 'ສຳເລັດ!',
                    text: 'ຄັດລອກລົງຄລິບບອດແລ້ວ',
                    timer: 1500,
                    showConfirmButton: false
                });
            });
        }
        
        // Enhanced form validation feedback
        function validateForm(form) {
            const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!input.value.trim()) {
                    input.classList.add('border-red-500');
                    isValid = false;
                } else {
                    input.classList.remove('border-red-500');
                }
            });
            
            return isValid;
        }
        
        // Auto-resize textareas
        document.addEventListener('DOMContentLoaded', function() {
            const textareas = document.querySelectorAll('textarea[data-auto-resize]');
            textareas.forEach(textarea => {
                textarea.addEventListener('input', function() {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
            });
        });
        
        // Lazy loading for images
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });
            
            document.querySelectorAll('img[data-src]').forEach(img => {
                imageObserver.observe(img);
            });
        }
        
        // Service Worker registration for PWA (if needed)
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js')
                    .then(function(registration) {
                        console.log('ServiceWorker registration successful');
                    })
                    .catch(function(err) {
                        console.log('ServiceWorker registration failed: ', err);
                    });
            });
        }
    </script>

</body>
</html>