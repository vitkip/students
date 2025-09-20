<?php
/**
 * ໄຟລ໌ສຳລັບປະມວນຜົນການລົງທະບຽນນັກສຶກສາ
 */

// ເຊື່ອມຕໍ່ຖານຂໍ້ມູນແລະໂຫລດຄລາສຕ່າງໆ
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/helpers/functions.php';
require_once __DIR__ . '/../../src/classes/Student.php';

// ເລີ່ມ session
session_start();

// ເພິ່ມການບັນທຶກ log ໃນໄຟລ໌
ini_set('log_errors', 1);
ini_set('error_log', BASE_PATH . '/logs/php-errors.log');
error_reporting(E_ALL);

// ສ້າງໂຟລເດີ logs ຖ້າຍັງບໍ່ມີ
if (!is_dir(BASE_PATH . '/logs')) {
    mkdir(BASE_PATH . '/logs', 0755, true);
}

/**
 * ຟັງຊັນສຳລັບສ້າງ Student ID ອັດຕະໂນມັດ
 */
function generateStudentId($major_code, $academic_year, $db) {
    try {
        // ແຍກປີຈາກປີການສຶກສາ (ເຊັ່ນ: "2025-2026" -> "25")
        $year_parts = explode('-', $academic_year);
        $year_short = substr($year_parts[0], -2);
        
        // ຄົ້ນຫາເລກລຳດັບສຸດທ້າຍ
        $stmt = $db->prepare("SELECT student_id FROM students WHERE student_id LIKE :pattern ORDER BY student_id DESC LIMIT 1");
        $pattern = $year_short . $major_code . '%';
        $stmt->bindParam(':pattern', $pattern);
        $stmt->execute();
        
        $last_student = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($last_student) {
            // ດຶງເລກລຳດັບຈາກ student_id ສຸດທ້າຍ
            $last_number = (int)substr($last_student['student_id'], -3);
            $next_number = $last_number + 1;
        } else {
            $next_number = 1;
        }
        
        // ສ້າງ student_id ໃໝ່
        return $year_short . $major_code . str_pad($next_number, 3, '0', STR_PAD_LEFT);
        
    } catch (Exception $e) {
        error_log("Error generating student ID: " . $e->getMessage());
        return null;
    }
}

// ກວດສອບວ່າສົ່ງມາຈາກຟອມຈິງບໍ່
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    try {
        // ກວດສອບການປ້ອນຂໍ້ມູນພື້ນຖານ
        $required_fields = ['first_name', 'last_name', 'gender', 'dob', 'major_id', 'academic_year_id', 'accommodation_type'];
        foreach ($required_fields as $field) {
            if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
                throw new Exception("ກະລຸນາປ້ອນຂໍ້ມູນໃຫ້ຄົບຖ້ວນ: " . $field);
            }
        }
        
        // ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
        $database = new Database();
        $db = $database->getConnection();
        
        if (!$db) {
            throw new Exception("ບໍ່ສາມາດເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນໄດ້");
        }
        
        // ຫາຂໍ້ມູນສາຂາແລະປີການສຶກສາ
        $stmt = $db->prepare("SELECT code FROM majors WHERE id = :id");
        $stmt->bindParam(':id', $_POST['major_id']);
        $stmt->execute();
        $major = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$major) {
            throw new Exception("ບໍ່ພົບຂໍ້ມູນສາຂາວິຊາ");
        }
        
        $stmt = $db->prepare("SELECT year FROM academic_years WHERE id = :id");
        $stmt->bindParam(':id', $_POST['academic_year_id']);
        $stmt->execute();
        $academic_year = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$academic_year) {
            throw new Exception("ບໍ່ພົບຂໍ້ມູນປີການສຶກສາ");
        }
        
        // ສ້າງ Student ID ອັດຕະໂນມັດ
        $student_id = generateStudentId($major['code'], $academic_year['year'], $db);
        if (!$student_id) {
            throw new Exception("ບໍ່ສາມາດສ້າງລະຫັດນັກສຶກສາໄດ້");
        }
        
        // ສ້າງອອບເຈັກ Student
        $student = new Student($db);
        
        // ດຶງຂໍ້ມູນຈາກຟອມ
        $student->student_id = $student_id;
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
        
        // ຈັດການອັບໂຫຼດຮູບພາບ
        if (!empty($_FILES['photo']['name'])) {
            $upload_dir = UPLOAD_DIR;
            
            // ສ້າງໂຟລເດີ້ຖ້າບໍ່ມີ
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // ກວດສອບນາມສະກຸນຂອງໄຟລ໌
            $file_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
            if (!in_array($file_ext, ALLOWED_EXTENSIONS)) {
                throw new Exception("ນາມສະກຸນໄຟລ໌ບໍ່ຖືກຕ້ອງ. ອະນຸຍາດສະເພາະ: " . implode(', ', ALLOWED_EXTENSIONS));
            }
            
            // ກວດສອບຂະໜາດໄຟລ໌
            if ($_FILES['photo']['size'] > MAX_FILE_SIZE) {
                throw new Exception("ຂະໜາດໄຟລ໌ໃຫຍ່ເກີນໄປ. ຂະໜາດສູງສຸດແມ່ນ " . (MAX_FILE_SIZE / 1024 / 1024) . "MB");
            }
            
            // ຕັ້ງຊື່ໄຟລ໌
            $file_name = uniqid() . '.' . $file_ext;
            $file_path = $upload_dir . $file_name;
            
            // ຍ້າຍໄຟລ໌ໄປເກັບໄວ້
            if (!move_uploaded_file($_FILES['photo']['tmp_name'], $file_path)) {
                throw new Exception("ເກີດຂໍ້ຜິດພາດໃນການອັບໂຫຼດຮູບພາບ");
            }
            
            $student->photo = $file_name;
        }
        
        // ກວດສອບ email ທີ່ຊ້ຳກັນ (ຖ້າມີ)
        if (!empty($student->email)) {
            $stmt = $db->prepare("SELECT id FROM students WHERE email = :email");
            $stmt->bindParam(':email', $student->email);
            $stmt->execute();
            if ($stmt->fetch()) {
                throw new Exception("ອີເມວນີ້ຖືກໃຊ້ແລ້ວ");
            }
        }
        
        // ບັນທຶກຂໍ້ມູນນັກສຶກສາ
        if ($student->create()) {
            $_SESSION['message'] = "ລົງທະບຽນສຳເລັດແລ້ວ! ລະຫັດນັກສຶກສາ: " . $student_id;
            $_SESSION['message_type'] = "success";
            $_SESSION['registered_student_id'] = $student_id;
            
            // ບັນທຶກ log
            error_log("Student registered successfully: ID = " . $student_id);
            
            header("Location: " . BASE_URL . "?page=registration-success&id=" . $student_id);
            exit;
        } else {
            throw new Exception("ເກີດຂໍ້ຜິດພາດໃນການລົງທະບຽນ");
        }
        
    } catch (Exception $e) {
        // ບັນທຶກ error log
        error_log("Registration error: " . $e->getMessage());
        
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = "error";
        
        // ສົ່ງຂໍ້ມູນເດີມກັບຄືນ
        $_SESSION['form_data'] = $_POST;
        
        header("Location: " . BASE_URL . "?page=register");
        exit;
    }
} else {
    // ຖ້າບໍ່ໄດ້ສົ່ງມາຈາກຟອມ
    $_SESSION['message'] = "ບໍ່ອະນຸຍາດໃຫ້ເຂົ້າເຖິງໂດຍກົງ";
    $_SESSION['message_type'] = "error";
    header("Location: " . BASE_URL);
    exit;
}
?>
    
    // ตรวจสอบอีเมลซ้ำ
    if (!empty($email)) {
        $student = new Student($db);
        if ($student->emailExists($email)) {
            // ลบรูปภาพที่อัพโหลด
            if (file_exists($upload_path)) {
                unlink($upload_path);
            }
            throw new Exception("ອີເມວນີ້ຖືກໃຊ້ແລ້ວ. ກະລຸນາໃຊ້ອີເມວອື່ນ");
        }
    }
    
    // สร้าง student object
    $student = new Student($db);
    
    // กำหนดค่า properties ตามโครงสร้างฐานข้อมูล
    $student->first_name = $first_name;
    $student->last_name = $last_name;
    $student->gender = $gender;
    $student->date_of_birth = $dob;
    $student->email = $email;
    $student->phone = $phone;
    $student->village = $village;
    $student->district = $district;
    $student->province = $province;
    $student->accommodation_type = $accommodation_type;
    $student->photo = $new_filename;
    $student->major_id = $major_id;
    $student->academic_year_id = $academic_year_id;
    $student->previous_school = $previous_school;
    
    // บันทึกข้อมูล
    if ($student->create()) {
        $_SESSION['message'] = "ລົງທະບຽນສຳເລັດແລ້ວ. ລະຫັດ: " . $student->id;
        $_SESSION['message_type'] = 'success';
        header("Location: " . BASE_URL . "?page=students");
        exit;
    } else {
        // ลบรูปภาพถ้าบันทึกไม่สำเร็จ
        if (file_exists($upload_path)) {
            unlink($upload_path);
        }
        throw new Exception("ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກຂໍ້ມູນ");
    }
    
} catch (Exception $e) {
    $_SESSION['message'] = $e->getMessage();
    $_SESSION['message_type'] = 'error';
    header("Location: " . BASE_URL . "?page=register");
    exit;
}