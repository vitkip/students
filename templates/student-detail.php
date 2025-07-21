<?php
// filepath: c:\xampp\htdocs\register-learning\templates\student-detail.php
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

// ดึงข้อมูลสาขา
$majorObj = new Major($db);
$major = $majorObj->readOne($studentData['major_id']);

// ดึงข้อมูลปีการศึกษา
$yearObj = new AcademicYear($db);
$academicYear = $yearObj->readOne($studentData['academic_year_id']);

// สร้าง QR Code data
$qr_data = "ນັກສຶກສາ: " . $studentData['first_name'] . " " . $studentData['last_name'] . "\n";
$qr_data .= "ລະຫັດ: " . ($studentData['student_id'] ?? $studentData['id']) . "\n";
$qr_data .= "ສາຂາ: " . ($major['name'] ?? 'N/A') . "\n";
$qr_data .= "ປີການສຶກສາ: " . ($academicYear['year'] ?? 'N/A') . "\n";
$qr_data .= "ວັນທີລົງທະບຽນ: " . date('d/m/Y H:i:s', strtotime($studentData['registered_at'] ?? 'now'));

// สร้าง URL สำหรับ QR Code
$qr_code_url = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&format=png&margin=10&data=" . urlencode($qr_data);
?>

<div class="min-h-screen bg-gradient-to-br from-amber-50 via-orange-50 to-yellow-50 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-6xl mx-auto">
            
            <!-- Header Section -->
            <div class="text-center mb-8 fade-in">
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-user-graduate text-amber-500 mr-3"></i>
                    ລາຍລະອຽດນັກສຶກສາ
                </h1>
                <p class="text-gray-600 text-lg md:text-xl">ຂໍ້ມູນສົມບູນຂອງນັກສຶກສາ</p>
            </div>
            
            <div class="detail-card fade-in">
                <div class="p-6 md:p-8">
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons mb-8">
                        <a href="<?= BASE_URL ?>index.php?page=student-edit&id=<?= $student_id ?>" class="btn btn-primary">
                            <i class="fas fa-edit"></i>
                            ແກ້ໄຂຂໍ້ມູນ
                        </a>
                        <a  href="<?= BASE_URL ?>index.php?page=student-card&id=<?= $student_id ?>" class="btn btn-secondary">
                            <i class="fas fa-print"></i>
                            ພິມບັດນັກສຶກສາ
                        </a>
                        <a href="<?= $qr_code_url ?>" target="_blank" download="qrcode-<?= htmlspecialchars($studentData['student_id'] ?? $studentData['id']) ?>.png" class="btn btn-success">
                            <i class="fas fa-download"></i>
                            ດາວໂຫລດ QR Code
                        </a>
                        <a href="<?= BASE_URL ?>index.php?page=students" class="btn btn-gray">
                            <i class="fas fa-arrow-left"></i>
                            ກັບຄືນ
                        </a>
                    </div>

                    <div class="grid lg:grid-cols-3 gap-8">
                        
                        <!-- Student Photo & QR Code -->
                        <div class="photo-container fade-in">
                            <!-- Student Photo -->
                            <?php if (!empty($studentData['photo']) && file_exists(BASE_PATH . "/public/uploads/photos/" . $studentData['photo'])): ?>
                                <img src="<?= BASE_URL ?>uploads/photos/<?= htmlspecialchars($studentData['photo']) ?>" 
                                     alt="ຮູບນັກສຶກສາ" 
                                     class="student-photo">
                            <?php else: ?>
                                <div class="photo-placeholder">
                                    <i class="fas fa-user"></i>
                                    <span>ບໍ່ມີຮູບ</span>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Student Name Card -->
                            <div class="student-name w-full">
                                <h2><?= htmlspecialchars($studentData['first_name'] . ' ' . $studentData['last_name']) ?></h2>
                                <p class="student-id">
                                    <i class="fas fa-id-card mr-1"></i>
                                    ລະຫັດ: <?= htmlspecialchars($studentData['student_id'] ?? $studentData['id']) ?>
                                </p>
                            </div>
                            
                            <!-- QR Code -->
                            <div class="qr-container w-full">
                                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                                    <i class="fas fa-qrcode text-amber-500"></i>
                                    QR Code ຂອງນັກສຶກສາ
                                </h3>
                                <div class="qr-code">
                                    <img src="<?= $qr_code_url ?>" alt="QR Code" 
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                                    <div class="w-full h-48 bg-gray-100 flex items-center justify-center text-gray-500 rounded-lg" style="display: none;">
                                        <div class="text-center">
                                            <i class="fas fa-qrcode text-4xl mb-2"></i>
                                            <p>QR Code ບໍ່ສາມາດໂຫລດໄດ້</p>
                                        </div>
                                    </div>
                                </div>
                                <p class="text-sm text-gray-600 text-center">ສະແກນເພື່ອເບິ່ງຂໍ້ມູນ</p>
                            </div>
                        </div>

                        <!-- Student Information -->
                        <div class="lg:col-span-2 space-y-6">
                            
                            <!-- ຂໍ້ມູນພື້ນຖານ -->
                            <div class="info-card fade-in">
                                <div class="info-title">
                                    <i class="fas fa-user"></i>
                                    ຂໍ້ມູນພື້ນຖານ
                                </div>
                                
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-signature"></i>
                                            ຊື່
                                        </div>
                                        <div class="info-value"><?= htmlspecialchars($studentData['first_name']) ?></div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-user-tag"></i>
                                            ນາມສະກຸນ
                                        </div>
                                        <div class="info-value"><?= htmlspecialchars($studentData['last_name']) ?></div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-venus-mars"></i>
                                            ເພດ
                                        </div>
                                        <div class="info-value">
                                            <span class="inline-block px-3 py-1 text-sm font-medium rounded-full <?= 
                                                $studentData['gender'] === 'ຊາຍ' ? 'bg-blue-100 text-blue-800' : 
                                                ($studentData['gender'] === 'ຍິງ' ? 'bg-pink-100 text-pink-800' : 
                                                ($studentData['gender'] === 'ພຣະ' ? 'bg-orange-100 text-orange-800' : 'bg-gray-100 text-gray-800'))
                                            ?>">
                                                <?= htmlspecialchars($studentData['gender']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-birthday-cake"></i>
                                            ວັນເດືອນປີເກີດ
                                        </div>
                                        <div class="info-value"><?= htmlspecialchars(date('d/m/Y', strtotime($studentData['dob']))) ?></div>
                                    </div>
                                    
                                    <?php if (!empty($studentData['email'])): ?>
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-envelope"></i>
                                            ອີເມວ
                                        </div>
                                        <div class="info-value">
                                            <a href="mailto:<?= htmlspecialchars($studentData['email']) ?>" class="text-blue-600 hover:text-blue-800">
                                                <?= htmlspecialchars($studentData['email']) ?>
                                            </a>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if (!empty($studentData['phone'])): ?>
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-phone"></i>
                                            ເບີໂທ
                                        </div>
                                        <div class="info-value">
                                            <a href="tel:<?= htmlspecialchars($studentData['phone']) ?>" class="text-green-600 hover:text-green-800">
                                                <i class="fab fa-whatsapp mr-1"></i>
                                                <?= htmlspecialchars($studentData['phone']) ?>
                                            </a>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- ຂໍ້ມູນທີ່ຢູ່ -->
                            <div class="info-card fade-in">
                                <div class="info-title">
                                    <i class="fas fa-map-marker-alt"></i>
                                    ຂໍ້ມູນທີ່ຢູ່
                                </div>
                                
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-home"></i>
                                            ບ້ານ
                                        </div>
                                        <div class="info-value"><?= htmlspecialchars($studentData['village'] ?? '-') ?></div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-city"></i>
                                            ເມືອງ
                                        </div>
                                        <div class="info-value"><?= htmlspecialchars($studentData['district'] ?? '-') ?></div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-flag"></i>
                                            ແຂວງ
                                        </div>
                                        <div class="info-value"><?= htmlspecialchars($studentData['province'] ?? '-') ?></div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-bed"></i>
                                            ທີ່ພັກອາໄສ
                                        </div>
                                        <div class="info-value">
                                            <span class="inline-block px-3 py-1 text-sm font-medium rounded-full <?= 
                                                $studentData['accommodation_type'] === 'ມີວັດຢູ່ແລ້ວ' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'
                                            ?>">
                                                <?= htmlspecialchars($studentData['accommodation_type'] ?? '-') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- ຂໍ້ມູນການສຶກສາ -->
                            <div class="info-card fade-in">
                                <div class="info-title">
                                    <i class="fas fa-graduation-cap"></i>
                                    ຂໍ້ມູນການສຶກສາ
                                </div>
                                
                                <div class="info-grid">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-school"></i>
                                            ໂຮງຮຽນເດີມ
                                        </div>
                                        <div class="info-value"><?= htmlspecialchars($studentData['previous_school'] ?? '-') ?></div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-book"></i>
                                            ສາຂາຮຽນ
                                        </div>
                                        <div class="info-value">
                                            <span class="inline-block px-3 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800">
                                                <?= htmlspecialchars($major['name'] ?? '-') ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-calendar-alt"></i>
                                            ປີການສຶກສາ
                                        </div>
                                        <div class="info-value">
                                            <span class="inline-block px-3 py-1 text-sm font-medium rounded-full bg-indigo-100 text-indigo-800">
                                                <?= htmlspecialchars($academicYear['year'] ?? '-') ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-clock"></i>
                                            ວັນທີລົງທະບຽນ
                                        </div>
                                        <div class="info-value">
                                            <?php 
                                            if (!empty($studentData['registered_at'])) {
                                                echo date('d/m/Y H:i', strtotime($studentData['registered_at']));
                                            } else {
                                                echo '-';
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-scroll to top for better UX
    window.scrollTo(0, 0);
    
    // Add fade-in animation to elements
    const elements = document.querySelectorAll('.fade-in');
    elements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        
        setTimeout(() => {
            element.style.transition = 'opacity 0.6s ease-out, transform 0.6s ease-out';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 100 * index);
    });
    
    console.log('Student detail page loaded successfully!');
});
</script>