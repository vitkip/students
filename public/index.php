<?php
// ป้องกัน output ก่อน headers
ob_start();

// การตั้งค่าพื้นฐาน
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);

// เส้นทางพื้นฐาน
define('BASE_PATH', dirname(__DIR__));

// ไฟล์การตั้งค่า
if (file_exists(BASE_PATH . '/config/config.php')) {
    require_once BASE_PATH . '/config/config.php';
} else {
    die('ບໍ່ພົບໄຟລ໌ການຕັ້ງຄ່າ (config.php)');
}

// ไฟล์ฐานข้อมูล
if (file_exists(BASE_PATH . '/config/database.php')) {
    require_once BASE_PATH . '/config/database.php';
    
    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        $error = "ບໍ່ສາມາດເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນໄດ້";
    }
} else {
    $error = "ບໍ່ພົບໄຟລ໌ຖານຂໍ້ມູນ (database.php)";
}

// ไฟล์ช่วยเหลือการยืนยันตัวตน
require_once BASE_PATH . '/src/helpers/auth.php';

// เริ่ม session อย่างปลอดภัย
safeSessionStart();

// จัดการคำขอ logout ก่อนอื่น (ก่อน output ใดๆ)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    // ทำ logout
    logoutUser();
    
    // Redirect ไปหน้า login พร้อมข้อความ
    if (!headers_sent()) {
        header("Location: " . BASE_URL . "?page=login&logout=1");
        exit;
    } else {
        // ใช้ JavaScript redirect
        ob_end_clean();
        echo "<!DOCTYPE html><html><head><meta charset='UTF-8'>";
        echo "<script>window.location.href = '" . BASE_URL . "?page=login&logout=1';</script>";
        echo "<meta http-equiv='refresh' content='0;url=" . BASE_URL . "?page=login&logout=1'>";
        echo "</head><body>";
        echo "<p>ກຳລັງອອກຈາກລະບົບ... <a href='" . BASE_URL . "?page=login&logout=1'>ກົດທີ່ນີ້ຖ້າບໍ່ຖືກນຳທາງໂດຍອັດຕະໂນມັດ</a></p>";
        echo "</body></html>";
        exit;
    }
}

// Handle logout request ด้วยฟังก์ชัน (สำรอง)
if (function_exists('handleLogoutRequest')) {
    handleLogoutRequest();
}

// รับค่า page และ action
$page = $_GET['page'] ?? 'home';
$action = $_GET['action'] ?? '';

// ตรวจสอบข้อมูลนักเรียนสำหรับหน้า student-card
$student_data = null;
if ($page === 'student-card' && isset($_GET['id'])) {
    if (!$student_data) {
        require_once BASE_PATH . '/src/classes/Student.php';
        $student = new Student($db);
        $student_data = $student->read($_GET['id']);
        
        if (!$student_data) {
            $_SESSION['message'] = 'ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ';
            $_SESSION['message_type'] = 'error';
            header("Location: " . BASE_URL . "?page=students");
            exit;
        }
    }
    
    include '../templates/student-card.php';
    exit;
}

// Load students data for students page
if ($page === 'students') {
    require_once BASE_PATH . '/src/classes/Student.php';
    require_once BASE_PATH . '/src/classes/Major.php';
    require_once BASE_PATH . '/src/classes/AcademicYear.php';
    
    // Initialize classes
    $student = new Student($db);
    $majorClass = new Major($db);
    $academicYearClass = new AcademicYear($db);
    
    // Get search parameters
    $current_search = trim($_GET['search'] ?? '');
    $current_major = (int)($_GET['major'] ?? 0);
    $current_year = (int)($_GET['year'] ?? 0);
    $current_page = max(1, (int)($_GET['p'] ?? 1));
    $students_per_page = (int)($_GET['students_per_page'] ?? 10);
    
    // Validate students_per_page
    $allowed_per_page = [10, 25, 50, 100];
    if (!in_array($students_per_page, $allowed_per_page)) {
        $students_per_page = 10;
    }
    
    // Handle delete action
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
        $delete_id = (int)$_GET['id'];
        
        if ($student->delete($delete_id)) {
            $_SESSION['message'] = 'ລຶບຂໍ້ມູນນັກສຶກສາສຳເລັດແລ້ວ';
            $_SESSION['message_type'] = 'success';
        } else {
            $_SESSION['message'] = 'ເກີດຂໍ້ຜິດພາດໃນການລຶບຂໍ້ມູນ';
            $_SESSION['message_type'] = 'error';
        }
        
        // Redirect to remove action from URL
        $redirect_params = [];
        if (!empty($current_search)) $redirect_params['search'] = $current_search;
        if ($current_major > 0) $redirect_params['major'] = $current_major;
        if ($current_year > 0) $redirect_params['year'] = $current_year;
        if ($students_per_page != 10) $redirect_params['students_per_page'] = $students_per_page;
        $redirect_params['p'] = $current_page;
        $redirect_params['page'] = 'students';
        
        $redirect_url = BASE_URL . 'index.php?' . http_build_query($redirect_params);
        header("Location: " . $redirect_url);
        exit;
    }
    
    // Get total count for pagination
    $total_students = $student->countWithFilter($current_search, $current_major, $current_year);
    $total_pages = ceil($total_students / $students_per_page);
    
    // Adjust current page if it's out of range
    if ($current_page > $total_pages && $total_pages > 0) {
        $current_page = $total_pages;
    }
    
    // Calculate offset
    $offset = ($current_page - 1) * $students_per_page;
    
    // Get students data
    $students = $student->readAllWithFilter(
        $current_search, 
        $current_major, 
        $current_year, 
        $students_per_page, 
        $offset
    );
    
    // Get majors and academic years for filters
    $majors = $majorClass->readAll();
    $academicYears = $academicYearClass->readAll();
    
    // Ensure arrays are not null
    $students = $students ?? [];
    $majors = $majors ?? [];
    $academicYears = $academicYears ?? [];
}

// ตรวจสอบข้อผิดพลาดการเชื่อมต่อฐานข้อมูล
if (isset($error)) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4" role="alert">';
    echo '<strong>ຂໍ້ຜິດພາດ!</strong> ' . $error;
    echo '</div>';
} else {
    // ประมวลผล POST requests ก่อน
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        switch ($page) {
            case 'register':
                if (isset($_POST['register'])) {
                    $processor_file = BASE_PATH . '/src/processors/register_processor.php';
                    if (file_exists($processor_file)) {
                        include $processor_file;
                        exit;
                    } else {
                        $_SESSION['message'] = 'ບໍ່ພົບໄຟລ໌ປະມວນຜົນ';
                        $_SESSION['message_type'] = 'error';
                    }
                }
                break;
                
            case 'student-edit':
                if (isset($_POST['update'])) {
                    $processor_file = BASE_PATH . '/src/processors/student_update_processor.php';
                    if (file_exists($processor_file)) {
                        include $processor_file;
                        exit;
                    } else {
                        $_SESSION['message'] = 'ບໍ່ພົບໄຟລ໌ປະມວນຜົນການອັປເດດ';
                        $_SESSION['message_type'] = 'error';
                    }
                }
                break;
        }
    }
    
    // ประมวลผล GET actions
    if (!empty($action)) {
        switch ($action) {
            case 'delete':
                if ($page === 'students' && isset($_GET['id'])) {
                    $student_id = (int)$_GET['id'];
                    require_once BASE_PATH . '/src/classes/Student.php';
                    $student = new Student($db);
                    
                    if ($student->delete($student_id)) {
                        $_SESSION['message'] = 'ລຶບຂໍ້ມູນນັກສຶກສາສຳເລັດແລ້ວ';
                        $_SESSION['message_type'] = 'success';
                    } else {
                        $_SESSION['message'] = 'ເກີດຂໍ້ຜິດພາດໃນການລຶບຂໍ້ມູນ';
                        $_SESSION['message_type'] = 'error';
                    }
                    
                    header("Location: " . BASE_URL . "?page=students");
                    exit;
                }
                break;
        }
    }
    
    // แสดงหน้าที่ถูกร้องขอ
    switch ($page) {
        case 'register':
            require_once BASE_PATH . '/src/classes/Major.php';
            require_once BASE_PATH . '/src/classes/AcademicYear.php';
            
            $majorClass = new Major($db);
            $academicYearClass = new AcademicYear($db);
            
            $majors = $majorClass->readAll();
            $academicYears = $academicYearClass->readAll();
            
            include BASE_PATH . '/templates/components/header.php';
            include BASE_PATH . '/templates/register.php';
            include BASE_PATH . '/templates/components/footer.php';
            break;
            
        case 'students':
            include BASE_PATH . '/templates/components/header.php';
            include BASE_PATH . '/templates/students-list.php';
            include BASE_PATH . '/templates/components/footer.php';
            break;
            
        case 'student-detail':
            if (isset($_GET['id'])) {
                require_once BASE_PATH . '/src/classes/Student.php';
                $student = new Student($db);
                $student_data = $student->read($_GET['id']);
                
                if ($student_data) {
                    include BASE_PATH . '/templates/components/header.php';
                    include BASE_PATH . '/templates/student-detail.php';
                    include BASE_PATH . '/templates/components/footer.php';
                } else {
                    $_SESSION['message'] = 'ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ';
                    $_SESSION['message_type'] = 'error';
                    header("Location: " . BASE_URL . "?page=students");
                    exit;
                }
            } else {
                header("Location: " . BASE_URL . "?page=students");
                exit;
            }
            break;
            
        case 'student-edit':
            if (isset($_GET['id'])) {
                require_once BASE_PATH . '/src/classes/Student.php';
                require_once BASE_PATH . '/src/classes/Major.php';
                require_once BASE_PATH . '/src/classes/AcademicYear.php';
                
                $student = new Student($db);
                $majorClass = new Major($db);
                $academicYearClass = new AcademicYear($db);
                
                $student_data = $student->read($_GET['id']);
                $majors = $majorClass->readAll();
                $academicYears = $academicYearClass->readAll();
                
                if ($student_data) {
                    include BASE_PATH . '/templates/components/header.php';
                    include BASE_PATH . '/templates/student-edit.php';
                    include BASE_PATH . '/templates/components/footer.php';
                } else {
                    $_SESSION['message'] = 'ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ';
                    $_SESSION['message_type'] = 'error';
                    header("Location: " . BASE_URL . "?page=students");
                    exit;
                }
            } else {
                header("Location: " . BASE_URL . "?page=students");
                exit;
            }
            break;
            
        case 'registration-success':
            include BASE_PATH . '/templates/components/header.php';
            include BASE_PATH . '/templates/registration-success.php';
            include BASE_PATH . '/templates/components/footer.php';
            break;
            
        case 'dashboard':
            include BASE_PATH . '/templates/components/header.php';
            include BASE_PATH . '/templates/dashboard.php';
            include BASE_PATH . '/templates/components/footer.php';
            break;
            
        case 'home':
        default:
            include BASE_PATH . '/templates/components/header.php';
            // ตรวจสอบว่ามีไฟล์ home.php หรือไม่
            if (file_exists(BASE_PATH . '/templates/home.php')) {
                include BASE_PATH . '/templates/home.php';
            } else {
                // ถ้าไม่มีให้สร้างหน้า home พื้นฐาน
                echo '<div class="container mx-auto px-4 py-8">';
                echo '<h1 class="text-3xl font-bold text-center mb-8">ລະບົບລົງທະບຽນນັກສຶກສາ</h1>';
                echo '<div class="grid md:grid-cols-2 gap-6">';
                echo '<a href="' . BASE_URL . '?page=register" class="bg-blue-500 hover:bg-blue-600 text-white p-6 rounded-lg text-center">';
                echo '<h2 class="text-xl font-bold mb-2">ລົງທະບຽນນັກສຶກສາ</h2>';
                echo '<p>ສຳລັບການລົງທະບຽນນັກສຶກສາໃໝ່</p>';
                echo '</a>';
                echo '<a href="' . BASE_URL . '?page=students" class="bg-green-500 hover:bg-green-600 text-white p-6 rounded-lg text-center">';
                echo '<h2 class="text-xl font-bold mb-2">ລາຍຊື່ນັກສຶກສາ</h2>';
                echo '<p>ເບິ່ງລາຍຊື່ນັກສຶກສາທີ່ລົງທະບຽນແລ້ວ</p>';
                echo '</a>';
                echo '</div>';
                echo '</div>';
            }
            include BASE_PATH . '/templates/components/footer.php';
            break;
    }
}

// ตรวจสอบและตั้งค่าสิทธิ์โฟลเดอร์
$upload_dir = BASE_PATH . '/public/uploads/photos/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}
?>
