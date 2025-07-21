<?php
// messages.php - ບັນທຶກຂໍໍ່ສະແດງຂໍໍ່ສຳເລັດ ຫຼື ບັນຫາ

// เริ่ม session เฉพาะเมื่อยังไม่มีการเริ่ม session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ສະແດງຂໍໍ່ສຳເລັດ
if (isset($_SESSION['success'])) {
    echo '<div class="bg-green-500 text-white p-4 rounded mb-4">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

// ສະແດງຂໍໍ່ບັນຫາ
if (isset($_SESSION['error'])) {
    echo '<div class="bg-red-500 text-white p-4 rounded mb-4">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>