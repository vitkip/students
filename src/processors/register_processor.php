<?php
// ເຊື່ອມຕໍ່ຖານຂໍ້ມູນແລະໂຫລດຄລາສຕ່າງໆ
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../src/helpers/functions.php';
require_once __DIR__ . '/../../src/classes/Student.php';

// ເລີ່ມ session
session_start();

// ກວດສອບວ່າສົ່ງມາຈາກຟອມຈິງບໍ່
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    try {
        // ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
        $database = new Database();
        $db = $database->getConnection();
        
        // ສ້າງອອບເຈັກ Student
        $student = new Student($db);
        
        // ດຶງຂໍ້ມູນຈາກຟອມ
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
        
        // ບັນທຶກຂໍ້ມູນນັກສຶກສາ
        if ($student->create()) {
            $_SESSION['message'] = "ລົງທະບຽນສຳເລັດແລ້ວ!";
            $_SESSION['message_type'] = "success";
            header("Location: " . BASE_URL . "?page=students");
            exit;
        } else {
            throw new Exception("ເກີດຂໍ້ຜິດພາດໃນການລົງທະບຽນ");
        }
        
    } catch (Exception $e) {
        $_SESSION['message'] = $e->getMessage();
        $_SESSION['message_type'] = "error";
        header("Location: " . BASE_URL . "?page=register");
        exit;
    }
} else {
    // ຖ້າບໍ່ໄດ້ສົ່ງມາຈາກຟອມ
    header("Location: " . BASE_URL);
    exit;
}
?>