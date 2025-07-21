<?php
// filepath: c:\xampp\htdocs\register-learning\templates\student-edit.php
// ตรวจสอบว่ามีการเรียกใช้ผ่าน index.php หรือไม่
if (!defined('BASE_PATH')) {
    header('Location: ../public/index.php');
    exit('Access denied. Please use proper navigation.');
}

// ดึงข้อมูลนักศึกษาตาม ID ที่ส่งมา
require_once BASE_PATH . '/src/classes/Student.php';
require_once BASE_PATH . '/src/classes/Major.php';
require_once BASE_PATH . '/src/classes/AcademicYear.php';

// กวดสอบว่ามี ID ถูกส่งมาหรือไม่
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['message'] = "ບໍ່ພົບຂໍ້ມູນນັກສຶກສາທີ່ຕ້ອງການ";
    $_SESSION['message_type'] = "error";
    header("Location: " . BASE_URL . "index.php?page=students");
    exit;
}

$student_id = (int)$_GET['id'];

// สร้างอ็อบเจ็ก Student
$student = new Student($db);

// ดึงข้อมูลนักศึกษาตาม ID
$studentData = $student->readOne($student_id);

if (!$studentData) {
    $_SESSION['message'] = "ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ ID: " . $student_id;
    $_SESSION['message_type'] = "error";
    header("Location: " . BASE_URL . "index.php?page=students");
    exit;
}

// ดึงข้อมูลสาขาทั้งหมด
$majorObj = new Major($db);
$majors = $majorObj->readAll();

// ดึงข้อมูลปีการศึกษาทั้งหมด
$yearObj = new AcademicYear($db);
$academicYears = $yearObj->readAll();

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
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
    
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Enhanced Header -->
            <div class="text-center mb-8">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-user-edit text-amber-500 mr-3"></i>
                    ແກ້ໄຂຂໍ້ມູນນັກສຶກສາ
                </h1>
                <p class="text-gray-600 text-lg md:text-xl">ປັບປຸງຂໍ້ມູນໃຫ້ເປັນປັດຈຸບັນ</p>
                <div class="mt-4 inline-flex items-center px-4 py-2 bg-white rounded-full shadow-md">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    <span class="text-sm text-gray-600">ຊ່ອງທີ່ມີ <span class="text-red-500">*</span> ຈຳເປັນຕ້ອງຕື່ມ</span>
                </div>
            </div>
            
            <div class="form-card">
                <div class="p-6 md:p-8">
                    <form action="<?= BASE_URL ?>index.php?page=student-edit&action=update" method="POST" enctype="multipart/form-data" id="editForm">
                        <input type="hidden" name="id" value="<?= $student_id ?>">

                        <!-- ຮູບນັກສຶກສາ -->
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-camera"></i>
                                ຮູບຖ່າຍປະຈຳຕົວ
                            </div>
                            
                            <div class="photo-upload-area">
                                <?php if (!empty($studentData['photo']) && file_exists(BASE_PATH . "/public/uploads/photos/" . $studentData['photo'])): ?>
                                    <img src="<?= BASE_URL ?>uploads/photos/<?= htmlspecialchars($studentData['photo']) ?>" 
                                         alt="ຮູບນັກສຶກສາ" 
                                         id="photo-preview"
                                         class="current-photo">
                                <?php else: ?>
                                    <div class="photo-placeholder" id="photo-placeholder">
                                        <i class="fas fa-user"></i>
                                        <span>ບໍ່ມີຮູບ</span>
                                    </div>
                                    <img src="" alt="ຮູບທີເລືອກ" id="photo-preview" class="current-photo hidden">
                                <?php endif; ?>
                                
                                <div class="text-center">
                                    <label class="file-upload-btn">
                                        <i class="fas fa-upload mr-2"></i>
                                        ເລືອກຮູບໃໝ່
                                        <input type="file" name="photo" id="photo" accept="image/*">
                                    </label>
                                    <input type="hidden" name="current_photo" value="<?= htmlspecialchars($studentData['photo'] ?? '') ?>">
                                    <p class="text-sm text-gray-500 mt-2">JPG, PNG, GIF (ຂະໜາດສູງສຸດ 5MB)</p>
                                </div>
                            </div>
                        </div>

                        <!-- ຂໍ້ມູນສ່ວນຕົວ -->
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-user"></i>
                                ຂໍ້ມູນສ່ວນຕົວ
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="input-group">
                                    <input type="text" name="first_name" id="first_name" required 
                                           value="<?= htmlspecialchars($studentData['first_name']) ?>" placeholder=" ">
                                    <label for="first_name">ຊື່ <span class="required-asterisk">*</span></label>
                                </div>
                                <div class="input-group">
                                    <input type="text" name="last_name" id="last_name" required 
                                           value="<?= htmlspecialchars($studentData['last_name']) ?>" placeholder=" ">
                                    <label for="last_name">ນາມສະກຸນ <span class="required-asterisk">*</span></label>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="input-group">
                                    <select name="gender" id="gender" required>
                                        <option value="">ເລືອກເພດ</option>
                                        <option value="ພຣະ" <?= $studentData['gender'] === 'ພຣະ' ? 'selected' : '' ?>>ພຣະ</option>
                                        <option value="ສ.ນ" <?= $studentData['gender'] === 'ສ.ນ' ? 'selected' : '' ?>>ສ.ນ</option>
                                        <option value="ຊາຍ" <?= $studentData['gender'] === 'ຊາຍ' ? 'selected' : '' ?>>ຊາຍ</option>
                                        <option value="ຍິງ" <?= $studentData['gender'] === 'ຍິງ' ? 'selected' : '' ?>>ຍິງ</option>
                                        <option value="ອຶ່ນໆ" <?= $studentData['gender'] === 'ອຶ່ນໆ' ? 'selected' : '' ?>>ອຶ່ນໆ</option>
                                    </select>
                                    <label for="gender">ເພດ <span class="required-asterisk">*</span></label>
                                </div>
                                <div class="input-group">
                                    <input type="date" name="dob" id="dob" required 
                                           value="<?= htmlspecialchars($studentData['dob']) ?>">
                                    <label for="dob">ວັນເກິດ <span class="required-asterisk">*</span></label>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="input-group">
                                    <input type="tel" name="phone" id="phone" 
                                           value="<?= htmlspecialchars($studentData['phone'] ?? '') ?>" placeholder=" ">
                                    <label for="phone">ເບີໂທ</label>
                                </div>
                                <div class="input-group">
                                    <input type="email" name="email" id="email" 
                                           value="<?= htmlspecialchars($studentData['email'] ?? '') ?>" placeholder=" ">
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
                                    <input type="text" name="village" id="village" 
                                           value="<?= htmlspecialchars($studentData['village'] ?? '') ?>" placeholder=" ">
                                    <label for="village">ບ້ານ</label>
                                </div>
                                <div class="input-group">
                                    <input type="text" name="district" id="district" 
                                           value="<?= htmlspecialchars($studentData['district'] ?? '') ?>" placeholder=" ">
                                    <label for="district">ເມືອງ</label>
                                </div>
                                <div class="input-group">
                                    <select name="province" id="province" required>
                                        <option value="">ເລືອກແຂວງ</option>
                                        <option value="ນະຄອນຫຼວງວຽງຈັນ" <?= ($studentData['province'] ?? '') === 'ນະຄອນຫຼວງວຽງຈັນ' ? 'selected' : '' ?>>ນະຄອນຫຼວງວຽງຈັນ</option>
                                        <option value="ຜົ້ງສາລີ" <?= ($studentData['province'] ?? '') === 'ຜົ້ງສາລີ' ? 'selected' : '' ?>>ຜົ້ງສາລີ</option>
                                        <option value="ຫຼວງນ້ຳທາ" <?= ($studentData['province'] ?? '') === 'ຫຼວງນ້ຳທາ' ? 'selected' : '' ?>>ຫຼວງນ້ຳທາ</option>
                                        <option value="ອຸດົມໄຊ" <?= ($studentData['province'] ?? '') === 'ອຸດົມໄຊ' ? 'selected' : '' ?>>ອຸດົມໄຊ</option>
                                        <option value="ບໍ່ແກ້ວ" <?= ($studentData['province'] ?? '') === 'ບໍ່ແກ້ວ' ? 'selected' : '' ?>>ບໍ່ແກ້ວ</option>
                                        <option value="ຫຼວງພະບາງ" <?= ($studentData['province'] ?? '') === 'ຫຼວງພະບາງ' ? 'selected' : '' ?>>ຫຼວງພະບາງ</option>
                                        <option value="ຫົວພັນ" <?= ($studentData['province'] ?? '') === 'ຫົວພັນ' ? 'selected' : '' ?>>ຫົວພັນ</option>
                                        <option value="ໄຊຍະບູລີ" <?= ($studentData['province'] ?? '') === 'ໄຊຍະບູລີ' ? 'selected' : '' ?>>ໄຊຍະບູລີ</option>
                                        <option value="ຊຽງຂວາງ" <?= ($studentData['province'] ?? '') === 'ຊຽງຂວາງ' ? 'selected' : '' ?>>ຊຽງຂວາງ</option>
                                        <option value="ວຽງຈັນ" <?= ($studentData['province'] ?? '') === 'ວຽງຈັນ' ? 'selected' : '' ?>>ວຽງຈັນ</option>
                                        <option value="ບໍລິຄຳໄຊ" <?= ($studentData['province'] ?? '') === 'ບໍລິຄຳໄຊ' ? 'selected' : '' ?>>ບໍລິຄຳໄຊ</option>
                                        <option value="ຄຳມ່ວນ" <?= ($studentData['province'] ?? '') === 'ຄຳມ່ວນ' ? 'selected' : '' ?>>ຄຳມ່ວນ</option>
                                        <option value="ສະຫວັນນະເຂດ" <?= ($studentData['province'] ?? '') === 'ສະຫວັນນະເຂດ' ? 'selected' : '' ?>>ສະຫວັນນະເຂດ</option>
                                        <option value="ສາລະວັນ" <?= ($studentData['province'] ?? '') === 'ສາລະວັນ' ? 'selected' : '' ?>>ສາລະວັນ</option>
                                        <option value="ເຊກອງ" <?= ($studentData['province'] ?? '') === 'ເຊກອງ' ? 'selected' : '' ?>>ເຊກອງ</option>
                                        <option value="ຈຳປາສັກ" <?= ($studentData['province'] ?? '') === 'ຈຳປາສັກ' ? 'selected' : '' ?>>ຈຳປາສັກ</option>
                                        <option value="ອັດຕະປື" <?= ($studentData['province'] ?? '') === 'ອັດຕະປື' ? 'selected' : '' ?>>ອັດຕະປື</option>
                                        <option value="ໄຊສົມບູນ" <?= ($studentData['province'] ?? '') === 'ໄຊສົມບູນ' ? 'selected' : '' ?>>ໄຊສົມບູນ</option>
                                    </select>
                                    <label for="province">ແຂວງ <span class="required-asterisk">*</span></label>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="input-group">
                                    <select name="accommodation_type" id="accommodation_type" required>
                                        <option value="">ເລືອກປະເພດທີ່ພັກ</option>
                                        <option value="ມີວັດຢູ່ແລ້ວ" <?= $studentData['accommodation_type'] === 'ມີວັດຢູ່ແລ້ວ' ? 'selected' : '' ?>>ມີວັດຢູ່ແລ້ວ</option>
                                        <option value="ຫາວັດໃຫ້" <?= $studentData['accommodation_type'] === 'ຫາວັດໃຫ້' ? 'selected' : '' ?>>ຫາວັດໃຫ້</option>
                                    </select>
                                    <label for="accommodation_type">ທີ່ພັກອາໄສ <span class="required-asterisk">*</span></label>
                                </div>
                                <div class="input-group">
                                    <select name="major_id" id="major_id" required>
                                        <option value="">ເລືອກສາຂາ</option>
                                        <?php foreach ($majors as $major): ?>
                                            <option value="<?= $major['id'] ?>" <?= $studentData['major_id'] == $major['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($major['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="major_id">ສາຂາຮຽນ <span class="required-asterisk">*</span></label>
                                </div>
                            </div>
                        </div>

                        <!-- ຂໍ້ມູນການສຶກສາ -->
                        <div class="form-section">
                            <div class="section-header">
                                <i class="fas fa-book-open"></i>
                                ຂໍ້ມູນການສຶກສາ
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="input-group">
                                    <input type="text" name="previous_school" id="previous_school"
                                        value="<?= htmlspecialchars($studentData['previous_school'] ?? '') ?>" 
                                        placeholder=" ">
                                    <label for="previous_school">ໂຮງຮຽນເດີມ</label>
                                </div>

                                <div class="input-group">
                                    <select name="academic_year_id" id="academic_year_id" required>
                                        <option value="">ເລືອກປີການສຶກສາ</option>
                                        <?php foreach ($academicYears as $year): ?>
                                            <option value="<?= $year['id'] ?>" <?= $studentData['academic_year_id'] == $year['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($year['year']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <label for="academic_year_id">ປີການສຶກສາ <span class="required-asterisk">*</span></label>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end mt-6">
                            <button type="submit" name="update" class="submit-btn">
                                <i class="fas fa-save mr-2"></i>
                                ບັນທຶກການປ່ຽນແປງ
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Script ເພື່ອສະແດງຮູບທີ່ເລືອກ
        document.getElementById('photo').addEventListener('change', function(e) {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('photo-preview').src = e.target.result;
                    document.getElementById('photo-preview').classList.remove('hidden');
                    const placeholder = document.getElementById('photo-placeholder');
                    if (placeholder) placeholder.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>