<?php
// filepath: c:\xampp\htdocs\register-learning\src\processors\student_update_processor.php

// ตรวจสอบว่ามีการเรียกใช้ผ่าน index.php หรือไม่
if (!defined('BASE_PATH')) {
    die('Access denied');
}

// ตรวจสอบว่ามีข้อมูลการอัปเดต
if (!isset($_POST['update']) || !isset($_POST['id'])) {
    $_SESSION['message'] = 'ບໍ່ມີຂໍ້ມູນການແກ້ໄຂ';
    $_SESSION['message_type'] = 'error';
    header("Location: " . BASE_URL . "?page=students");
    exit;
}

$student_id = (int)$_POST['id'];

try {
    // ตรวจสอบข้อมูลที่จำเป็น
    $required_fields = [
        'first_name' => 'ຊື່',
        'last_name' => 'ນາມສະກຸນ',
        'gender' => 'ເພດ',
        'dob' => 'ວັນເກິດ',
        'major_id' => 'ສາຂາ',
        'academic_year_id' => 'ປີການສຶກສາ'
    ];
    
    foreach ($required_fields as $field => $label) {
        if (empty($_POST[$field])) {
            throw new Exception("ກະລຸນາປ້ອນ{$label}");
        }
    }
    
    // ดึงข้อมูลนักศึกษาปัจจุบัน
    $student = new Student($db);
    $current_student = $student->read($student_id);
    
    if (!$current_student) {
        throw new Exception("ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ");
    }
    
    // จัดการรูปภาพ
    $photo_name = $current_student['photo'];
    
    // หากมีการอัปโหลดรูปภาพใหม่
    if (!empty($_FILES['photo']['name'])) {
        // ตรวจสอบประเภทไฟล์
        $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!in_array($_FILES['photo']['type'], $allowed_types)) {
            throw new Exception("ຮູບແບບໄຟລ໌ບໍ່ຖືກຕ້ອງ. ອະນຸຍາດສະເພາະ JPG, PNG, ຫລື GIF ເທົ່ານັ້ນ");
        }
        
        // ตรวจสอบขนาดไฟล์ (5MB)
        if ($_FILES['photo']['size'] > 5 * 1024 * 1024) {
            throw new Exception("ຂະໜາດໄຟລ໌ໃຫຍ່ເກີນໄປ. ຈຳກັດທີ່ 5MB");
        }
        
        // สร้างชื่อไฟล์ใหม่
        $upload_dir = BASE_PATH . '/public/uploads/photos/';
        $file_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_name = uniqid() . '.' . $file_extension;
        $upload_path = $upload_dir . $photo_name;
        
        // สร้างไดเร็กทอรีถ้ายังไม่มี
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // อัพโหลดไฟล์
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $upload_path)) {
            throw new Exception("ບໍ່ສາມາດອັບໂຫລດຮູບຖ່າຍໄດ້. ກະລຸນາລອງອີກຄັ້ງ");
        }
        
        // ลบรูปภาพเก่า
        if (!empty($current_student['photo'])) {
            $old_photo = BASE_PATH . '/public/uploads/photos/' . $current_student['photo'];
            if (file_exists($old_photo)) {
                unlink($old_photo);
            }
        }
    }
    
    // ดึงข้อมูลจาก form
    $student->id = $student_id;
    $student->first_name = trim($_POST['first_name']);
    $student->last_name = trim($_POST['last_name']);
    $student->gender = $_POST['gender'];
    $student->date_of_birth = $_POST['dob'];
    $student->phone = !empty($_POST['phone']) ? trim($_POST['phone']) : null;
    $student->email = !empty($_POST['email']) ? trim($_POST['email']) : null;
    
    // ตรวจสอบอีเมลซ้ำ
    if (!empty($student->email) && $student->email !== $current_student['email']) {
        if ($student->emailExists($student->email, $student_id)) {
            // หากมีการอัปโหลดรูปภาพใหม่ ให้ลบ
            if ($photo_name !== $current_student['photo'] && file_exists($upload_path)) {
                unlink($upload_path);
            }
            throw new Exception("ອີເມວນີ້ຖືກໃຊ້ແລ້ວ. ກະລຸນາໃຊ້ອີເມວອື່ນ");
        }
    }
    
    // ข้อมูลที่อยู่
    $student->village = !empty($_POST['village']) ? trim($_POST['village']) : null;
    $student->district = !empty($_POST['district']) ? trim($_POST['district']) : null;
    $student->province = !empty($_POST['province']) ? trim($_POST['province']) : null;
    
    // ข้อมูลเพิ่มเติม
    $student->previous_school = !empty($_POST['previous_school']) ? trim($_POST['previous_school']) : null;
    $student->accommodation_type = !empty($_POST['accommodation_type']) ? $_POST['accommodation_type'] : 'ມີວັດຢູ່ແລ້ວ';
    $student->major_id = (int)$_POST['major_id'];
    $student->academic_year_id = (int)$_POST['academic_year_id'];
    $student->photo = $photo_name;
    
    // บันทึกข้อมูล
    if ($student->update()) {
        $_SESSION['message'] = 'ແກ້ໄຂຂໍ້ມູນສຳເລັດແລ້ວ';
        $_SESSION['message_type'] = 'success';
        header("Location: " . BASE_URL . "?page=student-edit&id=" . $student_id);
        exit;
    } else {
        // หากมีการอัปโหลดรูปภาพใหม่และการบันทึกไม่สำเร็จ ให้ลบรูปภาพใหม่
        if ($photo_name !== $current_student['photo'] && file_exists($upload_path)) {
            unlink($upload_path);
        }
        throw new Exception("ເກີດຂໍ້ຜິດພາດໃນການບັນທຶກຂໍ້ມູນ");
    }
    
} catch (Exception $e) {
    $_SESSION['message'] = $e->getMessage();
    $_SESSION['message_type'] = 'error';
    header("Location: " . BASE_URL . "?page=student-edit&id=" . $student_id);
    exit;
}