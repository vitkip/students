<?php
// filepath: c:\xampp\htdocs\register-learning\public\logout.php
// Direct logout script (backup method)

// Include configuration and authentication helpers
require_once '../config/config.php';
require_once BASE_PATH . '/src/helpers/auth.php';

// เริ่ม session อย่างปลอดภัย
safeSessionStart();

// ทำการ logout
logoutUser();

// Redirect ไปยัง main routing system
safeRedirect(BASE_URL . '?page=logout');
?>