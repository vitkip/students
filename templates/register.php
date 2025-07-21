<?php
// ตรวจสอบว่ามีการเรียกใช้ผ่าน index.php หรือไม่
if (!defined('BASE_PATH')) {
    header('Location: ../public/index.php?page=register');
    exit('Access denied. Please use proper navigation.');
}

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!isset($db) || !$db) {
    die('Database connection not available');
}

// นำเข้าไฟล์ helper functions
if (file_exists(BASE_PATH . '/src/helpers/functions.php')) {
    require_once BASE_PATH . '/src/helpers/functions.php';
}

// ดึงข้อมูลสาขาและปีการศึกษาที่ส่งมาจาก index.php
// หากไม่มี ให้ดึงเอง
if (!isset($majors)) {
    if (file_exists(BASE_PATH . '/src/classes/Major.php')) {
        require_once BASE_PATH . '/src/classes/Major.php';
        $majorObj = new Major($db);
        $majors = $majorObj->readAll();
    } else {
        $majors = [];
    }
}

if (!isset($academicYears)) {
    try {
        if (file_exists(BASE_PATH . '/src/classes/AcademicYear.php')) {
            require_once BASE_PATH . '/src/classes/AcademicYear.php';
            $yearObj = new AcademicYear($db);
            $academicYears = $yearObj->readAll();
            
            // Debug check
            if (empty($academicYears)) {
                error_log('Academic years array is empty after readAll()');
            }
        } else {
            error_log('AcademicYear.php file not found at: ' . BASE_PATH . '/src/classes/AcademicYear.php');
            $academicYears = [];
        }
    } catch (Exception $e) {
        error_log('Error loading academic years: ' . $e->getMessage());
        $academicYears = [];
    }
}

// ตรวจสอบข้อความแจ้งเตือน
if (isset($_SESSION['message']) && function_exists('showAlert')) {
    echo showAlert($_SESSION['message'], $_SESSION['message_type'] ?? 'info');
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
// ตรวจสอบข้อความแจ้งเตือน
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'] ?? 'info';
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
    
    echo "<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            title: '" . ($message_type === 'error' ? 'ເກີດຂໍ້ຜິດພາດ!' : 'ແຈ້ງເຕືອນ') . "',
            text: '" . addslashes($message) . "',
            icon: '" . ($message_type === 'error' ? 'error' : 'info') . "',
            confirmButtonText: 'ຮູ້ແລ້ວ',
            confirmButtonColor: '#f59e0b'
        });
    });
    </script>";
}
?>

<body class="bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 min-h-screen">
    <!-- Enhanced Progress Bar -->
    <div class="progress-bar">
        <div class="progress-fill" id="progressFill"></div>
    </div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Enhanced Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-graduation-cap text-amber-500 mr-3"></i>
                    ລົງທະບຽນນັກສຶກສາໃໝ່
                </h1>
                <p class="text-gray-600 text-lg md:text-xl">ກະລຸນາຕື່ມຂໍ້ມູນໃຫ້ຄົບຖ້ວນ</p>
                <div class="mt-4 inline-flex items-center px-4 py-2 bg-white rounded-full shadow-md">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    <span class="text-sm text-gray-600">ຊ່ອງທີ່ມີ <span class="text-red-500">*</span> ຈຳເປັນຕ້ອງຕື່ມ</span>
                </div>
            </div>
            
            <div class="form-card">
                <div class="p-6 md:p-8">
                    <form action="<?= BASE_URL ?>index.php?page=register&action=process" method="POST" enctype="multipart/form-data" id="registrationForm">
                        
                        <!-- ຂໍ້ມູນທົ່ວໄປ -->
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-user"></i>
                                ຂໍ້ມູນສ່ວນຕົວ
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="input-group">
                                    <input type="text" name="first_name" id="first_name" required placeholder=" ">
                                    <label for="first_name">ຊື່ <span class="required-asterisk">*</span></label>
                                </div>
                                <div class="input-group">
                                    <input type="text" name="last_name" id="last_name" required placeholder=" ">
                                    <label for="last_name">ນາມສະກຸນ <span class="required-asterisk">*</span></label>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="input-group">
                                    <select name="gender" id="gender" required>
                                        <option value="">ເລືອກເພດ</option>
                                        <option value="ພຣະ">ພຣະ</option>
                                        <option value="ສ.ນ">ສ.ນ</option>
                                        <option value="ຊາຍ">ຊາຍ</option>
                                        <option value="ຍິງ">ຍິງ</option>
                                        <option value="ອຶ່ນໆ">ອຶ່ນໆ</option>
                                    </select>
                                    <label for="gender">ເພດ <span class="required-asterisk">*</span></label>
                                </div>
                                <div class="input-group">
                                    <input type="date" name="dob" id="dob" required>
                                    <label for="dob">ວັນເກິດ <span class="required-asterisk">*</span></label>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="input-group">
                                    <input type="tel" name="phone" id="phone" placeholder=" ">
                                    <label for="phone">ເບີໂທ</label>
                                </div>
                                <div class="input-group">
                                    <input type="email" name="email" id="email" placeholder=" ">
                                    <label for="email">ອີເມວ</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ຂໍ້ມູນທີ່ຢູ່ -->
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-map-marker-alt"></i>
                                ທີ່ຢູ່ປັດຈຸບັນ
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="input-group">
                                    <input type="text" name="village" id="village" placeholder=" ">
                                    <label for="village">ບ້ານ</label>
                                </div>
                                <div class="input-group">
                                    <input type="text" name="district" id="district" placeholder=" ">
                                    <label for="district">ເມືອງ</label>
                                </div>
                                <div class="input-group">
                                    <select name="province" id="province">
                                        <option value="">ເລືອກແຂວງ</option>
                                        <option value="ນະຄອນຫຼວງວຽງຈັນ">ນະຄອນຫຼວງວຽງຈັນ</option>
                                        <option value="ຜົ້ງສາລີ">ຜົ້ງສາລີ</option>
                                        <option value="ຫຼວງນໍ້າທາ">ຫຼວງນໍ້າທາ</option>
                                        <option value="ອຸດົມໄຊ">ອຸດົມໄຊ</option>
                                        <option value="ບໍ່ແກ້ວ">ບໍ່ແກ້ວ</option>
                                        <option value="ຫຼວງພະບາງ">ຫຼວງພະບາງ</option>
                                        <option value="ຫົວພັນ">ຫົວພັນ</option>
                                        <option value="ໄຊຍະບູລີ">ໄຊຍະບູລີ</option>
                                        <option value="ຊຽງຂວາງ">ຊຽງຂວາງ</option>
                                        <option value="ວຽງຈັນ">ວຽງຈັນ</option>
                                        <option value="ບໍລິຄໍາໄຊ">ບໍລິຄໍາໄຊ</option>
                                        <option value="ຄໍາມ່ວນ">ຄໍາມ່ວນ</option>
                                        <option value="ສະຫວັນນະເຂດ">ສະຫວັນນະເຂດ</option>
                                        <option value="ສາລະວັນ">ສາລະວັນ</option>
                                        <option value="ເຊກອງ">ເຊກອງ</option>
                                        <option value="ຈໍາປາສັກ">ຈໍາປາສັກ</option>
                                        <option value="ອັດຕະປື">ອັດຕະປື</option>
                                        <option value="ໄຊສົມບູນ">ໄຊສົມບູນ</option>
                                    </select>
                                    <label for="province">ແຂວງ</label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ຂໍ້ມູນການສຶກສາ -->
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-school"></i>
                                ປະຫວັດການສຶກສາ
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="input-group">
                                    <input type="text" name="previous_school" id="previous_school" placeholder=" ">
                                    <label for="previous_school">ຈົບມໍປາຍຈາກ</label>
                                </div>
                                <div class="input-group">
                                    <select name="accommodation_type" id="accommodation_type" required>
                                        <option value="">ເລືອກປະເພດທີ່ພັກ</option>
                                        <option value="ມີວັດຢູ່ແລ້ວ">ມີວັດຢູ່ແລ້ວ</option>
                                        <option value="ຫາວັດໃຫ້">ຫາວັດໃຫ້</option>
                                    </select>
                                    <label for="accommodation_type">ປະເພດທີ່ພັກ <span class="required-asterisk">*</span></label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ຂໍ້ມູນການລົງທະບຽນ -->
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-book"></i>
                                ຂໍ້ມູນການລົງທະບຽນ
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="input-group">
                                    <select name="major_id" id="major_id" required>
                                        <option value="">ເລືອກສາຂາ</option>
                                        <?php if (!empty($majors)): ?>
                                            <?php foreach ($majors as $major): ?>
                                                <option value="<?= htmlspecialchars($major['id']) ?>"><?= htmlspecialchars($major['name']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="major_id">ສາຂາ <span class="required-asterisk">*</span></label>
                                </div>
                                <div class="input-group">
                                    <select name="academic_year_id" id="academic_year_id" required>
                                        <option value="">ເລືອກປີການສຶກສາ</option>
                                        <?php if (!empty($academicYears)): ?>
                                            <?php foreach ($academicYears as $year): ?>
                                                <option value="<?= htmlspecialchars($year['id']) ?>"><?= htmlspecialchars($year['year']) ?></option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <label for="academic_year_id">ປີການສຶກສາ <span class="required-asterisk">*</span></label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Enhanced Upload Area -->
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-camera"></i>
                                ຮູບຖ່າຍປະຈຳຕົວ
                            </div>
                            
                            <div class="upload-area" id="uploadArea">
                                <input type="file" name="photo" id="photo" accept="image/jpeg,image/png,image/gif" required>
                                <label for="photo">
                                    <i class="fas fa-id-card upload-icon"></i>
                                    <span class="upload-text">ອັບໂຫຼດຮູບຖ່າຍປະຈຳຕົວຈິງ <span class="required-asterisk">*</span></span>
                                    <span class="upload-subtext">ກະລຸນາໃຊ້ຮູບຖ່າຍໜ້າຕົງ ພື້ນຫຼັງສີຂາວ</span>
                                    <div class="mt-2 px-4 py-2 bg-amber-50 rounded-lg border border-amber-200 text-amber-800">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        ກະລຸນາໃຊ້ຮູບຖ່າຍທີ່ເປັນທາງການເທົ່ານັ້ນ (ບໍ່ແມ່ນຮູບເຊວຟີ້)
                                    </div>
                                    <span class="upload-info mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>
                                        JPG, PNG, GIF (ຂະໜາດສູງສຸດ 5MB)
                                    </span>
                                </label>
                            </div>
                            
                            <!-- Enhanced Image Preview -->
                            <div id="imagePreview" class="image-preview-container hidden">
                                <div class="preview-header">
                                    <div class="preview-title">
                                        <i class="fas fa-image text-green-500"></i>
                                        ຮູບທີ່ເລືອກ
                                    </div>
                                    <button type="button" class="remove-image" id="removeImage">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                                <div class="text-center">
                                    <img id="previewImg" src="" alt="Preview" class="preview-image mx-auto">
                                </div>
                                <div class="file-info" id="fileInfo">
                                    <div class="file-info-item">
                                        <span class="file-info-label">ຊື່ໄຟລ์:</span>
                                        <span class="file-info-value" id="fileName"></span>
                                    </div>
                                    <div class="file-info-item">
                                        <span class="file-info-label">ຂະໜາດ:</span>
                                        <span class="file-info-value" id="fileSize"></span>
                                    </div>
                                    <div class="file-info-item">
                                        <span class="file-info-label">ປະເພດ:</span>
                                        <span class="file-info-value" id="fileType"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Enhanced Submit Button -->
                        <div class="form-section">
                            <div class="mt-8">
                                <button type="submit" name="register" class="submit-btn" id="submitBtn">
                                    <i class="fas fa-user-plus mr-2"></i>
                                    ລົງທະບຽນ
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <script>
        // Enhanced Progress Bar
        function updateProgressBar() {
            const form = document.getElementById('registrationForm');
            const inputs = form.querySelectorAll('input[required], select[required]');
            let filledInputs = 0;
            
            inputs.forEach(input => {
                if (input.type === 'radio') {
                    if (document.querySelector(`input[name="${input.name}"]:checked`)) {
                        filledInputs++;
                    }
                } else if (input.value.trim() !== '') {
                    filledInputs++;
                }
            });
            
            const progress = (filledInputs / inputs.length) * 100;
            document.getElementById('progressFill').style.width = progress + '%';
        }
        
        // Enhanced Image Upload with Preview
        function setupImageUpload() {
            const photoInput = document.getElementById('photo');
            const uploadArea = document.getElementById('uploadArea');
            const imagePreview = document.getElementById('imagePreview');
            const previewImg = document.getElementById('previewImg');
            const removeBtn = document.getElementById('removeImage');
            
            // File input change event
            photoInput.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    handleFileUpload(file);
                }
            });
            
            // Drag and drop functionality
            uploadArea.addEventListener('dragover', function(e) {
                e.preventDefault();
                uploadArea.classList.add('dragover');
            });
            
            uploadArea.addEventListener('dragleave', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
            });
            
            uploadArea.addEventListener('drop', function(e) {
                e.preventDefault();
                uploadArea.classList.remove('dragover');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith('image/')) {
                        photoInput.files = files;
                        handleFileUpload(file);
                    } else {
                        showNotification('ກະລຸນາເລືອກໄຟລ์ຮູບພາບເທົ່ານັ້ນ', 'error');
                    }
                }
            });
            
            // Remove image functionality
            removeBtn.addEventListener('click', function() {
                photoInput.value = '';
                imagePreview.classList.add('hidden');
                uploadArea.style.display = 'flex';
                updateProgressBar();
            });
            
            function handleFileUpload(file) {
                // Validate file size (5MB max)
                if (file.size > 5 * 1024 * 1024) {
                    showNotification('ຂະໜາດໄຟລ້ນໃຫຍ່ເກີນໄປ! ກະລຸນາເລືອກໄຟລ໌ທີ່ນ້ອຍກວ່າ 5MB', 'error');
                    return;
                }
                
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    showNotification('ປະເພດໄຟລ່ບໍ່ຖືກຕ້ອງ! ກະລຸນາເລືອກ JPG, PNG ຫຼື GIF', 'error');
                    return;
                }
                
                // Create file reader
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    uploadArea.style.display = 'none';
                    imagePreview.classList.remove('hidden');
                    
                    // Update file info
                    document.getElementById('fileName').textContent = file.name;
                    document.getElementById('fileSize').textContent = formatFileSize(file.size);
                    document.getElementById('fileType').textContent = file.type.split('/')[1].toUpperCase();
                    
                    updateProgressBar();
                };
                reader.readAsDataURL(file);
            }
        }
        
        // Format file size
        function formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }
        
        // Enhanced Form Validation
        function setupFormValidation() {
            const form = document.getElementById('registrationForm');
            const inputs = form.querySelectorAll('input, select');
            
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });
                
                input.addEventListener('input', function() {
                    clearFieldError(this);
                    updateProgressBar();
                });
                
                input.addEventListener('change', function() {
                    validateField(this);
                    updateProgressBar();
                });
            });
            
            // Form submit validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const requiredFields = form.querySelectorAll('input[required], select[required]');
                
                requiredFields.forEach(field => {
                    if (!validateField(field)) {
                        isValid = false;
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    showNotification('ກະລຸນາຕື່ມຂໍ້ມູນໃຫ້ຄົບຖ້ວນ', 'error');
                    return;
                }
                
                // Show loading
                showLoading(true);
                const submitBtn = document.getElementById('submitBtn');
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>ກຳລັງປະມວນຜົນ...';
                submitBtn.disabled = true;
            });
        }
        
        // Field validation
        function validateField(field) {
            const value = field.value.trim();
            const fieldName = field.getAttribute('name');
            
            clearFieldError(field);
            
            if (field.hasAttribute('required') && !value) {
                showFieldError(field, 'ກະລຸນາຕື່ມຂໍ້ມູນໃນຊ່ອງນີ້');
                return false;
            }
            
            // Email validation
            if (fieldName === 'email' && value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(value)) {
                    showFieldError(field, 'ຮູບແບບອີເມວບໍ່ຖືກຕ້ອງ');
                    return false;
                }
            }
            
            // Phone validation
            if (fieldName === 'phone' && value) {
                const phoneRegex = /^[0-9+\-\s()]+$/;
                if (!phoneRegex.test(value)) {
                    showFieldError(field, 'ເບີໂທບໍ່ຖືກຕ້ອງ');
                    return false;
                }
            }
            
            // Date validation
            if (field.type === 'date' && value) {
                const today = new Date();
                const birthDate = new Date(value);
                const age = today.getFullYear() - birthDate.getFullYear();
                
                if (age < 10 || age > 100) {
                    showFieldError(field, 'ວັນເກິດບໍ່ສົມເຫດສົມຜົນ');
                    return false;
                }
            }
            
            field.classList.add('input-success');
            return true;
        }
        
        // Show field error
        function showFieldError(field, message) {
            field.classList.add('input-error');
            field.classList.remove('input-success');
            
            // Remove existing error message
            const existingError = field.parentNode.querySelector('.error-message');
            if (existingError) {
                existingError.remove();
            }
            
            // Add error message
            const errorDiv = document.createElement('div');
            errorDiv.className = 'error-message';
            errorDiv.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${message}`;
            field.parentNode.appendChild(errorDiv);
        }
        
        // Clear field error
        function clearFieldError(field) {
            field.classList.remove('input-error');
            const errorMessage = field.parentNode.querySelector('.error-message');
            if (errorMessage) {
                errorMessage.remove();
            }
        }
        
        // Show notification
        function showNotification(message, type = 'info') {
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300 ${
                type === 'error' ? 'bg-red-500 text-white' : 
                type === 'success' ? 'bg-green-500 text-white' : 
                'bg-blue-500 text-white'
            }`;
            notification.innerHTML = `
                <div class="flex items-center">
                    <i class="fas ${type === 'error' ? 'fa-times-circle' : type === 'success' ? 'fa-check-circle' : 'fa-info-circle'} mr-2"></i>
                    <span>${message}</span>
                </div>
            `;
            
            document.body.appendChild(notification);
            
            // Show notification
            setTimeout(() => {
                notification.classList.remove('translate-x-full');
            }, 100);
            
            // Hide notification after 5 seconds
            setTimeout(() => {
                notification.classList.add('translate-x-full');
                setTimeout(() => {
                    document.body.removeChild(notification);
                }, 300);
            }, 5000);
        }
        
        // Show/hide loading overlay
        function showLoading(show) {
            const overlay = document.getElementById('loadingOverlay');
            if (show) {
                overlay.classList.add('active');
            } else {
                overlay.classList.remove('active');
            }
        }
        
        // Initialize everything when DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            setupImageUpload();
            setupFormValidation();
            updateProgressBar();
            
            // Smooth scrolling for better UX
            document.querySelectorAll('input, select').forEach(element => {
                element.addEventListener('focus', function() {
                    this.scrollIntoView({ behavior: 'smooth', block: 'center' });
                });
            });
            
            console.log('Enhanced registration form initialized successfully!');
        });
    </script>