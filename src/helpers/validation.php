<?php
// filepath: /register-learning/register-learning/src/helpers/validation.php

// ຟັງຊັນສໍາລັບການກວດສອບຂໍໍ່ອນຂອງຟອມ
function validateStudentData($data) {
    $errors = [];

    // ກວດສອບຊື່ທຳອິດ
    if (empty($data['first_name'])) {
        $errors['first_name'] = 'ຊື່ທຳອິດບໍ່ສາມາດສົມບູນ';
    }

    // ກວດສອບນາມສະກຸນ
    if (empty($data['last_name'])) {
        $errors['last_name'] = 'ນາມສະກຸນບໍ່ສາມາດສົມບູນ';
    }

    // ກວດສອບເພດ
    if (empty($data['gender'])) {
        $errors['gender'] = 'ກະລຸນາເລືອກເພດ';
    }

    // ກວດສອບວັນເກີດ
    if (empty($data['dob'])) {
        $errors['dob'] = 'ກະລຸນາໃສ່ວັນເກີດ';
    }

    // ກວດສອບອີເມວ
    if (empty($data['email'])) {
        $errors['email'] = 'ອີເມວບໍ່ສາມາດສົມບູນ';
    } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'ອີເມວບໍ່ຖືກຕາມຮູບແບບ';
    }

    // ກວດສອບເບີໂທ
    if (empty($data['phone'])) {
        $errors['phone'] = 'ເບີໂທບໍ່ສາມາດສົມບູນ';
    }

    // ກວດສອບບໍ່ສະຖານທີ່
    if (empty($data['village']) || empty($data['district']) || empty($data['province'])) {
        $errors['address'] = 'ກະລຸນາໃສ່ບໍ່ສະຖານທີ່';
    }

    // ກວດສອບຮູບພາບ
    if (isset($data['photo']) && $data['photo']['error'] !== UPLOAD_ERR_OK) {
        $errors['photo'] = 'ກະລຸນາເພີ່ມຮູບພາບ';
    }

    // ກວດສອບປະເພດສະຖານທີ່
    if (empty($data['accommodation_type'])) {
        $errors['accommodation_type'] = 'ກະລຸນາເລືອກປະເພດສະຖານທີ່';
    }

    // ກວດສອບສະຖານທີ່ກ່ຽວກັບສະຖານທີ່ກ່ຽວກັບສະຖານທີ່
    if (empty($data['previous_school'])) {
        $errors['previous_school'] = 'ກະລຸນາໃສ່ສະຖານທີ່ກ່ຽວກັບສະຖານທີ່';
    }

    // ກວດສອບສາຍສະຖານທີ່
    if (empty($data['major_id'])) {
        $errors['major_id'] = 'ກະລຸນາເລືອກສາຍສະຖານທີ່';
    }

    // ກວດສອບປີສຶກສາ
    if (empty($data['academic_year_id'])) {
        $errors['academic_year_id'] = 'ກະລຸນາເລືອກປີສຶກສາ';
    }

    return $errors;
}
?>