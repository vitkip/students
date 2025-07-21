<?php
/**
 * ຟັງຊັ່ນສໍາລັບການກວດສອບຂໍ້ມູນຈາກແບບຟອມ
 */

/**
 * ກວດສອບວ່າຊື່ແມ່ນເປັນໄປຕາມເງື່ອນໄຂຫຼືບໍ່
 * @param string $name ຊື່ຫຼືນາມສະກຸນທີ່ຕ້ອງການກວດສອບ
 * @return boolean ຜົນການກວດສອບ
 */
function validateName($name) {
    return !empty($name) && strlen($name) >= 2 && strlen($name) <= 100;
}

/**
 * ກວດສອບວັນເກີດ
 * @param string $dob ວັນເກີດໃນຮູບແບບ Y-m-d
 * @return boolean ຜົນການກວດສອບ
 */
function validateDOB($dob) {
    if (empty($dob)) return false;
    
    $dobDate = new DateTime($dob);
    $now = new DateTime();
    $minAge = new DateTime('-80 years'); // ອາຍຸສູງສຸດທີ່ອະນຸຍາດ
    $maxAge = new DateTime('-15 years'); // ອາຍຸຕໍ່າສຸດທີ່ອະນຸຍາດ
    
    return $dobDate >= $minAge && $dobDate <= $maxAge;
}

/**
 * ກວດສອບວ່າເປັນອີເມວທີ່ຖືກຕ້ອງຫຼືບໍ່
 * @param string $email ອີເມວທີ່ຕ້ອງການກວດສອບ
 * @return boolean ຜົນການກວດສອບ
 */
function validateEmail($email) {
    if (empty($email)) return true; // ອີເມວບໍ່ຈໍາເປັນ
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * ກວດສອບເບີໂທລະສັບ
 * @param string $phone ເບີໂທທີ່ຕ້ອງການກວດສອບ
 * @return boolean ຜົນການກວດສອບ
 */
function validatePhone($phone) {
    if (empty($phone)) return true; // ເບີໂທບໍ່ຈໍາເປັນ
    return preg_match('/^[0-9+\s-]{8,15}$/', $phone);
}

/**
 * ກວດສອບຮູບພາບທີ່ອັບໂຫຼດ
 * @param array $file ຂໍ້ມູນຮູບພາບຈາກ $_FILES
 * @return array ຜົນການກວດສອບ [status, message]
 */
function validateImage($file) {
    if (empty($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return [true, null]; // ຮູບພາບບໍ່ຈໍາເປັນ
    }
    
    // ກວດສອບຂໍ້ຜິດພາດໃນການອັບໂຫຼດ
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return [false, fileUploadError($file['error'])];
    }
    
    // ກວດສອບຂະໜາດຟາຍ
    if ($file['size'] > MAX_FILE_SIZE) {
        return [false, "ຮູບພາບໃຫຍ່ເກີນ " . (MAX_FILE_SIZE / 1024 / 1024) . " MB"];
    }
    
    // ກວດສອບນາມສະກຸນຟາຍ
    $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($file_ext, ALLOWED_EXTENSIONS)) {
        return [false, "ນາມສະກຸນຟາຍບໍ່ຖືກຕ້ອງ. ອະນຸຍາດ: " . implode(', ', ALLOWED_EXTENSIONS)];
    }
    
    return [true, null];
}

/**
 * ທົດສອບຄວາມຖືກຕ້ອງຂອງຂໍ້ມູນຟອມທັງໝົດ
 * @param array $data ຂໍ້ມູນຈາກຟອມ
 * @param array $files ຂໍ້ມູນຮູບພາບ (ຈາກ $_FILES)
 * @return array [isValid, errors]
 */
function validateStudentForm($data, $files) {
    $errors = [];
    
    // ກວດສອບຊື່
    if (!validateName($data['first_name'])) {
        $errors[] = "ຊື່ຕ້ອງມີຄວາມຍາວລະຫວ່າງ 2-100 ຕົວອັກສອນ";
    }
    
    // ກວດສອບນາມສະກຸນ
    if (!validateName($data['last_name'])) {
        $errors[] = "ນາມສະກຸນຕ້ອງມີຄວາມຍາວລະຫວ່າງ 2-100 ຕົວອັກສອນ";
    }
    
    // ກວດສອບເພດ
    if (empty($data['gender'])) {
        $errors[] = "ກະລຸນາເລືອກເພດ";
    }
    
    // ກວດສອບວັນເກີດ
    if (!validateDOB($data['dob'])) {
        $errors[] = "ກະລຸນາລະບຸວັນເກີດທີ່ຖືກຕ້ອງ (ອາຍຸລະຫວ່າງ 15-80 ປີ)";
    }
    
    // ກວດສອບອີເມວ
    if (!validateEmail($data['email'])) {
        $errors[] = "ກະລຸນາລະບຸອີເມວທີ່ຖືກຕ້ອງ";
    }
    
    // ກວດສອບເບີໂທ
    if (!validatePhone($data['phone'])) {
        $errors[] = "ກະລຸນາລະບຸເບີໂທທີ່ຖືກຕ້ອງ";
    }
    
    // ກວດສອບສາຂາ
    if (empty($data['major_id'])) {
        $errors[] = "ກະລຸນາເລືອກສາຂາ";
    }
    
    // ກວດສອບປີການສຶກສາ
    if (empty($data['academic_year_id'])) {
        $errors[] = "ກະລຸນາເລືອກປີການສຶກສາ";
    }
    
    // ກວດສອບຮູບພາບ
    if (isset($files['photo'])) {
        list($valid, $message) = validateImage($files['photo']);
        if (!$valid && $message !== null) {
            $errors[] = $message;
        }
    }
    
    return [empty($errors), $errors];
}
?>