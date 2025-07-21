<?php
// filepath: c:\xampp\htdocs\register-learning\config\config.php

// ການຕັ້ງຄ່າພື້ນຖານ
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

define("BASE_URL", "http://localhost/register-learning/");
define("SITE_NAME", "ວິທະຍາໄລຄູສົງ ອົງຕື້ ລະບົບສະໝັກຮຽນ Online");

// ຕັ້ງຄ່າ timezone
date_default_timezone_set('Asia/Vientiane');

// ຕັ້ງຄ່າການສະແດງຜິດພາດ (ປິດໃນການຜະລິດ)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ການຕັ້ງຄ່າອັບໂຫລດຟາຍ - ກວດເບິ່ງແລະເລື່ອນທາງໃຫ້ຖືກຕ້ອງ
define("UPLOAD_DIR", BASE_PATH . "/public/uploads/photos/");
define("MAX_FILE_SIZE", 5 * 1024 * 1024); // 5MB
define("ALLOWED_EXTENSIONS", ["jpg", "jpeg", "png", "gif"]);

// ກວດເບິ່ງແລະສ້າງໂຟລເດີ uploads ຖ້າບໍ່ມີ
if (!file_exists(UPLOAD_DIR)) {
    mkdir(UPLOAD_DIR, 0755, true);
}
?>