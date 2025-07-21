<?php
// ตรวจสอบว่ามีการเรียกใช้ผ่าน index.php หรือไม่
if (!defined('BASE_PATH')) {
    header('Location: ../public/index.php');
    exit('Access denied. Please use proper navigation.');
}

// ตรวจสอบว่ามีข้อมูลใน session หรือไม่
if (!isset($studentData)) {
    $_SESSION['message'] = "ບໍ່ພົບຂໍ້ມູນການລົງທະບຽນ";
    $_SESSION['message_type'] = "error";
    header("Location: " . BASE_URL . "index.php?page=register");
    exit;
}

// ลบข้อมูลออกจาก session หลังจากโหลดหน้าเสร็จ
if (isset($_SESSION['student_data'])) {
    unset($_SESSION['student_data']);
    unset($_SESSION['qr_code_data']);
    unset($_SESSION['registration_success']);
    unset($_SESSION['show_success_alert']);
}
?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ລົງທະບຽນສຳເລັດ - ວິທະຍາໄລການສຶກສາ</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>public/assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
         body, html {
            font-family: 'Noto Sans Lao', sans-serif;
        }
        h1, h2, h3, h4, h5, h6, p, span, div, button, a {
            font-family: 'Noto Sans Lao', sans-serif;
        }
        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% { transform: translate3d(0,0,0); }
            40%, 43% { transform: translate3d(0, -30px, 0); }
            70% { transform: translate3d(0, -15px, 0); }
            90% { transform: translate3d(0, -4px, 0); }
        }
        .animate-bounce { animation: bounce 2s infinite; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.6s ease-out; }
    </style>
</head>
<body class="bg-gradient-to-br from-green-50 via-blue-50 to-purple-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            
            <!-- Success Header -->
            <div class="text-center mb-8 animate-fade-in">
                <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-6">
                    <i class="fas fa-check-circle text-5xl text-green-500 animate-bounce"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    🎉 ລົງທະບຽນສຳເລັດແລ້ວ!
                </h1>
                <p class="text-gray-600 text-lg md:text-xl mb-4">
                    ຂໍ້ມູນຂອງທ່ານຖືກບັນທຶກໄວ້ໃນລະບົບແລ້ວ
                </p>
                <div class="inline-flex items-center px-6 py-3 bg-green-100 rounded-full">
                    <i class="fas fa-graduation-cap text-green-600 mr-2"></i>
                    <span class="text-green-800 font-semibold">ຍິນດີຕ້ອນຮັບສູ່ວິທະຍາໄລການສຶກສາ</span>
                </div>
            </div>
            
            <!-- Student Info and QR Code Card -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 mb-8 animate-fade-in">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    
                    <!-- Student Details -->
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-user-circle mr-3 text-blue-500"></i>
                            ຂໍ້ມູນນັກສຶກສາ
                        </h2>
                        
                        <div class="space-y-4">
                            <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                                <i class="fas fa-user text-gray-500 w-5 mr-3"></i>
                                <div>
                                    <span class="text-gray-600 text-sm">ຊື່ - ນາມສະກຸນ:</span>
                                    <div class="font-semibold text-gray-800">
                                        <?= htmlspecialchars($studentData['first_name'] . ' ' . $studentData['last_name']) ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                                <i class="fas fa-id-card text-blue-500 w-5 mr-3"></i>
                                <div>
                                    <span class="text-gray-600 text-sm">ລະຫັດນັກສຶກສາ:</span>
                                    <div class="font-bold text-blue-600 text-lg">
                                        <?= htmlspecialchars($studentData['student_id'] ?? 'STU' . str_pad($studentData['id'], 6, '0', STR_PAD_LEFT)) ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center p-3 bg-purple-50 rounded-lg">
                                <i class="fas fa-book text-purple-500 w-5 mr-3"></i>
                                <div>
                                    <span class="text-gray-600 text-sm">ສາຂາວິຊາ:</span>
                                    <div class="font-semibold text-purple-700">
                                        <?= htmlspecialchars($studentData['major_name'] ?? 'ບໍ່ລະບຸ') ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center p-3 bg-green-50 rounded-lg">
                                <i class="fas fa-calendar text-green-500 w-5 mr-3"></i>
                                <div>
                                    <span class="text-gray-600 text-sm">ປີການສຶກສາ:</span>
                                    <div class="font-semibold text-green-700">
                                        <?= htmlspecialchars($studentData['academic_year_name'] ?? 'ບໍ່ລະບຸ') ?>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center p-3 bg-orange-50 rounded-lg">
                                <i class="fas fa-envelope text-orange-500 w-5 mr-3"></i>
                                <div>
                                    <span class="text-gray-600 text-sm">ອີເມວ:</span>
                                    <div class="font-semibold text-orange-700">
                                        <?= htmlspecialchars($studentData['email'] ?? 'ບໍ່ລະບຸ') ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- QR Code Section -->
                    <div class="text-center">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center justify-center">
                            <i class="fas fa-qrcode mr-3 text-purple-500"></i>
                            QR Code ນັກສຶກສາ
                        </h2>
                        
                        <?php if (isset($qrCodeData) && $qrCodeData['success']): ?>
                            <div class="bg-gradient-to-br from-purple-100 to-blue-100 rounded-2xl p-6 border-2 border-purple-200">
                                <div class="bg-white rounded-xl p-4 shadow-inner">
                                    <img src="<?= $qrCodeData['data_url'] ?>" 
                                         alt="QR Code" 
                                         class="w-48 h-48 mx-auto rounded-lg shadow-lg"
                                         onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgdmlld0JveD0iMCAwIDIwMCAyMDAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMjAwIiBmaWxsPSIjRjNGNEY2Ii8+CjxwYXRoIGQ9Ik0xMDAgNTBMMTUwIDEwMEwxMDAgMTUwTDUwIDEwMEwxMDAgNTBaIiBzdHJva2U9IiM2QjcyODAiIHN0cm9rZS13aWR0aD0iMiIgZmlsbD0ibm9uZSIvPgo8dGV4dCB4PSIxMDAiIHk9IjE4MCIgdGV4dC1hbmNob3I9Im1pZGRsZSIgZmlsbD0iIzZCNzI4MCIgZm9udC1zaXplPSIxMiI+UVIgQ29kZSBFcnJvcjwvdGV4dD4KPC9zdmc+Cg=='">
                                </div>
                                
                                <div class="mt-4 text-sm text-gray-700">
                                    <p class="font-medium mb-2">📱 ສະແກນເພື່ອເບິ່ງຂໍ້ມູນ</p>
                                    <p class="text-xs text-gray-600">
                                        ຫຼື ເຂົ້າເບິ່ງທີ່ເວັບໄຊ ສ່ວນຂໍ້ມູນນັກສຶກສາ
                                    </p>
                                    <?php if (isset($qrCodeData['fallback']) && $qrCodeData['fallback']): ?>
                                        <p class="text-xs text-orange-600 mt-1">
                                            <i class="fas fa-info-circle"></i> ໃຊ້ Fallback API
                                        </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="bg-red-50 rounded-2xl p-6 border-2 border-red-200">
                                <div class="text-red-500 mb-4">
                                    <i class="fas fa-exclamation-triangle text-4xl"></i>
                                </div>
                                <p class="text-red-700 font-medium mb-2">ເກີດຂໍ້ຜິດພາດໃນການສ້າງ QR Code</p>
                                <p class="text-xs text-red-600 mb-4">
                                    <?= htmlspecialchars($qrCodeData['error'] ?? 'ຂໍ້ຜິດພາດບໍ່ທຶກ') ?>
                                </p>
                                
                                <!-- Manual Link -->
                                <div class="bg-white rounded-lg p-4 border">
                                    <p class="text-sm text-gray-600 mb-2">ເຂົ້າເບິ່ງຂໍ້ມູນໂດຍກົງ:</p>
                                    <a href="<?= BASE_URL ?>index.php?page=student-detail&id=<?= $studentData['id'] ?>" 
                                       class="inline-flex items-center px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition-colors">
                                        <i class="fas fa-external-link-alt mr-2"></i>
                                        ເບິ່ງຂໍ້ມູນນັກສຶກສາ
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="text-center">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
                    
                    <?php if (isset($qrCodeData) && $qrCodeData['success']): ?>
                        <button onclick="downloadQRCode()" 
                                class="flex items-center justify-center px-6 py-3 bg-purple-500 hover:bg-purple-600 text-white rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl">
                            <i class="fas fa-download mr-2"></i>
                            ດາວໂຫຼດ QR Code
                        </button>
                    <?php endif; ?>
                    
                    <a href="<?= BASE_URL ?>index.php?page=student-detail&id=<?= $studentData['id'] ?>" 
                       class="flex items-center justify-center px-6 py-3 bg-blue-500 hover:bg-blue-600 text-white rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-eye mr-2"></i>
                        ເບິ່ງຂໍ້ມູນລະອຽດ
                    </a>
                    
                    <a href="<?= BASE_URL ?>index.php?page=register" 
                       class="flex items-center justify-center px-6 py-3 bg-green-500 hover:bg-green-600 text-white rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-plus mr-2"></i>
                        ລົງທະບຽນເພີ່ມ
                    </a>
                    
                    <a href="<?= BASE_URL ?>index.php?page=students" 
                       class="flex items-center justify-center px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl">
                        <i class="fas fa-list mr-2"></i>
                        ລາຍຊື່ນັກສຶກສາ
                    </a>
                </div>
                
                <!-- Back to Home -->
                <a href="<?= BASE_URL ?>index.php" 
                   class="inline-flex items-center px-8 py-3 bg-amber-500 hover:bg-amber-600 text-white rounded-lg transition-colors duration-200 shadow-lg hover:shadow-xl">
                    <i class="fas fa-home mr-2"></i>
                    ກັບໜ້າຫຼັກ
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // ดาวน์โหลด QR Code
        function downloadQRCode() {
            <?php if (isset($qrCodeData) && $qrCodeData['success']): ?>
                try {
                    const link = document.createElement('a');
                    link.href = '<?= $qrCodeData['data_url'] ?>';
                    link.download = 'qr-code-<?= htmlspecialchars($studentData['student_id'] ?? 'student') ?>.png';
                    link.target = '_blank';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    // แสดงข้อความยืนยัน
                    Swal.fire({
                        title: 'ກຳລັງດາວໂຫຼດ...',
                        text: 'QR Code ກຳລັງຖືກດາວໂຫຼດ',
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } catch (error) {
                    Swal.fire({
                        title: 'ເກີດຂໍ້ຜິດພາດ!',
                        text: 'ບໍ່ສາມາດດາວໂຫຼດ QR Code ໄດ້',
                        icon: 'error',
                        confirmButtonText: 'ຮູ້ແລ້ວ'
                    });
                }
            <?php endif; ?>
        }
        
        // แสดง success alert เมื่อโหลดหน้า
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($showSuccessAlert) && $showSuccessAlert): ?>
                Swal.fire({
                    title: '🎉 ຍິນດີຕ້ອນຮັບ!',
                    html: `
                        <div class="text-center">
                            <div class="text-6xl mb-4">🎓</div>
                            <h3 class="text-xl font-bold mb-4 text-gray-800">ລົງທະບຽນສຳເລັດ!</h3>
                            <p class="text-gray-600 mb-6">ທ່ານໄດ້ເປັນນັກສຶກສາຂອງວິທະຍາໄລການສຶກສາແລ້ວ</p>
                            <div class="bg-gradient-to-r from-blue-50 to-purple-50 p-4 rounded-lg border-2 border-blue-200">
                                <p class="text-sm font-medium text-blue-800 mb-1">ລະຫັດນັກສຶກສາ</p>
                                <p class="text-2xl font-bold text-blue-600">
                                    <?= htmlspecialchars($studentData['student_id'] ?? 'STU' . str_pad($studentData['id'], 6, '0', STR_PAD_LEFT)) ?>
                                </p>
                            </div>
                        </div>
                    `,
                    confirmButtonText: 'ເບິ່ງ QR Code',
                    confirmButtonColor: '#8b5cf6',
                    allowOutsideClick: false,
                    width: '500px'
                });
            <?php endif; ?>
        });
    </script>
</body>
</html>