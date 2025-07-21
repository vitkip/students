<?php
require_once '../config/config.php';
require_once '../config/database.php';
require_once '../src/helpers/functions.php';
require_once '../src/classes/Student.php';

// เริ่มหรือต่อ session
session_start();

// ตรวจสอบว่าส่งมาจาก form จริงๆ
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    try {
        // เชื่อมต่อฐานข้อมูล
        $database = new Database();
        $db = $database->getConnection();
        
        // สร้างอ็อบเจกต์ Student
        $student = new Student($db);
        
        // ดึงข้อมูลจาก form และทำความสะอาด
        $student->first_name = sanitize($_POST['first_name']);
        $student->last_name = sanitize($_POST['last_name']);
        $student->gender = sanitize($_POST['gender']);
        $student->dob = sanitize($_POST['dob']);
        $student->email = !empty($_POST['email']) ? sanitize($_POST['email']) : null;
        $student->phone = !empty($_POST['phone']) ? sanitize($_POST['phone']) : null;
        $student->village = !empty($_POST['village']) ? sanitize($_POST['village']) : null;
        $student->district = !empty($_POST['district']) ? sanitize($_POST['district']) : null;
        $student->province = !empty($_POST['province']) ? sanitize($_POST['province']) : null;
        $student->accommodation_type = sanitize($_POST['accommodation_type']);
        $student->previous_school = !empty($_POST['previous_school']) ? sanitize($_POST['previous_school']) : null;
        $student->major_id = (int)$_POST['major_id'];
        $student->academic_year_id = (int)$_POST['academic_year_id'];
        
        // จัดการอัปโหลดรูปภาพ
        if (!empty($_FILES['photo']['name'])) {
            $upload_dir = UPLOAD_DIR;
            $file_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            $file_name = uniqid() . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            
            // ตรวจสอบนามสกุลไฟล์
            $allowed_exts = ALLOWED_EXTENSIONS;
            if (!in_array($file_ext, $allowed_exts)) {
                throw new Exception("ນາມສະກຸນຂອງຟາຍບໍ່ຖືກຕ້ອງ. ອະນຸຍາດສະເພາະ: " . implode(', ', $allowed_exts));
            }
            
            // ตรวจสอบขนาดไฟล์
            if ($_FILES['photo']['size'] > MAX_FILE_SIZE) {
                throw new Exception("ຂະໜາດຟາຍໃຫຍ່ເກີນໄປ. ຂະໜາດສູງສຸດແມ່ນ " . (MAX_FILE_SIZE / 1024 / 1024) . "MB");
            }
            
            // ย้ายไฟล์ที่อัปโหลดมา
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $file_path)) {
                throw new Exception("ເກີດຂໍ້ຜິດພາດໃນການອັບໂຫຼດຮູບພາບ");
            }
            
            $student->photo = $file_name;
        }
        
        // บันทึกข้อมูล
        if ($student->create()) {
            $_SESSION['message'] = "ລົງທະບຽນສຳເລັດແລ້ວ!";
            $_SESSION['message_type'] = "success";
            header("Location: " . url('students'));
            exit;
        } else {
            throw new Exception("ເກີດຂໍ້ຜິດພາດໃນການລົງທະບຽນ");
        }
        
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = "error";
        header("Location: " . url('register'));
        exit;
    }
} else {
    // ถ้าไม่ได้ส่งมาจากการกรอกฟอร์ม
    header("Location: " . url('register'));
    exit;
}
?>