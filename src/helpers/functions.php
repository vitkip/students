<?php
// filepath: /register-learning/register-learning/src/helpers/functions.php

/**
 * ຟັງຊັນເພື່ອສໍາລວດວ່າອີ່ມີຄ່າທີ່ສົມບູນບໍ່
 * @param mixed $value ຄ່າທີ່ຈະສໍາລວດ
 * @return bool ຄືນຄ່າ true ຖ້າຄ່າສົມບູນ, ອື່ນໆຄືນຄ່າ false
 */
function isValid($value) {
    return isset($value) && !empty(trim($value));
}

/**
 * ຟັງຊັນເພື່ອອັບໂຫລດຮູບພາບ
 * @param array $file ຂໍໍາລະບຽບຂອງຮູບພາບ
 * @return string|bool ຄືນຄ່າສິນຄ້າທີ່ອັບໂຫລດສຳເລັດ, ອື່ນໆຄືນຄ່າ false
 */
function uploadPhoto($file) {
    $targetDir = __DIR__ . '/../../public/assets/uploads/photos/';
    $targetFile = $targetDir . basename($file["name"]);
    $imageFileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    
    // ກວດສອບຮູບພາບ
    $check = getimagesize($file["tmp_name"]);
    if ($check === false) {
        return false;
    }

    // ກວດສອບວ່າຮູບພາບສົມບູນ
    if (move_uploaded_file($file["tmp_name"], $targetFile)) {
        return $targetFile;
    } else {
        return false;
    }
}

/**
 * ຟັງຊັນເພື່ອສໍາລວດອີ່ມສະຖານທີ່ສົມບູນ
 * @param string $value ສະຖານທີ່ຈະສໍາລວດ
 * @return bool ຄືນຄ່າ true ຖ້າສະຖານທີ່ສົມບູນ, ອື່ນໆຄືນຄ່າ false
 */
function isValidLocation($value) {
    return isValid($value) && preg_match("/^[a-zA-Z0-9\s\-]+$/", $value);
}

/**
 * ຟັງຊັນຊ່ວຍຫຼາຍຢ່າງສຳລັບລະບົບ
 */

/**
 * ຮັກສາຄວາມປອດໄພຂໍ້ມູນທີ່ເຂົ້າມາ
 * @param string $data ຂໍ້ມູນທີ່ຕ້ອງການລ້າງ
 * @return string ຂໍ້ມູນທີ່ສະອາດ
 */
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * ສ້າງ URL ສຳລັບໜ້າຕ່າງໆ
 * @param string $page ຊື່ໜ້າ
 * @param array $params ພາຣາມິເຕີເພີ່ມເຕີມ (optional)
 * @return string URL ສົມບູນ
 */
function url($page, $params = []) {
    $url = BASE_URL . "?page=" . $page;
    
    if (!empty($params)) {
        foreach ($params as $key => $value) {
            $url .= "&" . $key . "=" . urlencode($value);
        }
    }
    
    return $url;
}

/**
 * ສະແດງຂໍ້ຄວາມແຈ້ງເຕືອນ
 * @param string $message ຂໍ້ຄວາມ
 * @param string $type ປະເພດຂໍ້ຄວາມ (success, error, warning, info)
 * @return string HTML ສຳລັບຂໍ້ຄວາມ
 */
function showAlert($message, $type = 'info') {
    $colors = [
        'success' => 'bg-green-100 border-green-400 text-green-700',
        'error' => 'bg-red-100 border-red-400 text-red-700',
        'warning' => 'bg-yellow-100 border-yellow-400 text-yellow-700',
        'info' => 'bg-blue-100 border-blue-400 text-blue-700'
    ];
    
    $class = isset($colors[$type]) ? $colors[$type] : $colors['info'];
    
    return "<div class='{$class} px-4 py-3 mb-4 rounded border' role='alert'>{$message}</div>";
}

/**
 * ແປງວັນທີເປັນຮູບແບບພາສາລາວ
 * @param string $date ວັນທີໃນຮູບແບບ Y-m-d
 * @return string ວັນທີໃນຮູບແບບພາສາລາວ
 */
function formatLaoDate($date) {
    if (empty($date)) return "";
    
    $timestamp = strtotime($date);
    $day = date('j', $timestamp);
    $month = date('n', $timestamp);
    $year = date('Y', $timestamp);
    
    $laoMonths = [
        1 => 'ມັງກອນ',
        2 => 'ກຸມພາ',
        3 => 'ມີນາ',
        4 => 'ເມສາ',
        5 => 'ພຶດສະພາ',
        6 => 'ມິຖຸນາ',
        7 => 'ກໍລະກົດ',
        8 => 'ສິງຫາ',
        9 => 'ກັນຍາ',
        10 => 'ຕຸລາ',
        11 => 'ພະຈິກ',
        12 => 'ທັນວາ'
    ];
    
    return $day . ' ' . $laoMonths[$month] . ' ' . $year;
}
?>