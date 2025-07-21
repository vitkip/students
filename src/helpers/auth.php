<?php
// filepath: c:\xampp\htdocs\register-learning\src\helpers\auth.php
// ฟังก์ชันช่วยสำหรับการจัดการการเข้าสู่ระบบ

/**
 * ตั้งค่า session ที่ปลอดภัย
 */
function initializeSessionSettings() {
    if (session_status() === PHP_SESSION_NONE) {
        ini_set('session.cookie_httponly', 1);
        ini_set('session.use_only_cookies', 1);
        ini_set('session.cookie_secure', 0);
        ini_set('session.cookie_samesite', 'Strict');
        session_name('REGISTER_LEARNING_SESSION');
    }
}

/**
 * เริ่ม session อย่างปลอดภัย
 */
function safeSessionStart() {
    if (session_status() === PHP_SESSION_NONE) {
        initializeSessionSettings();
        session_start();
    }
}

/**
 * ตรวจสอบว่าผู้ใช้เข้าสู่ระบบแล้วหรือไม่
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * ตรวจสอบว่าผู้ใช้เป็น admin หรือไม่
 */
function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

/**
 * รับ user ID ปัจจุบัน
 */
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * รับ username ปัจจุบัน
 */
function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}

/**
 * รับ user role ปัจจุบัน
 */
function getCurrentUserRole() {
    return $_SESSION['user_role'] ?? null;
}

/**
 * เข้าสู่ระบบ
 */
function loginUser($user_data) {
    // ตรวจสอบว่า headers ถูกส่งแล้วหรือไม่
    if (headers_sent($filename, $line)) {
        error_log("Headers already sent in {$filename} on line {$line}");
        return false;
    }
    
    // เริ่ม session ถ้ายังไม่มี
    safeSessionStart();
    
    // สร้าง session ID ใหม่อย่างปลอดภัย
    if (!headers_sent()) {
        session_regenerate_id(true);
    }
    
    // ตั้งค่า session variables
    $_SESSION['user_id'] = $user_data['id'];
    $_SESSION['username'] = $user_data['username'];
    $_SESSION['user_role'] = $user_data['role'];
    $_SESSION['full_name'] = $user_data['full_name'] ?? '';
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
    
    // บันทึก log
    error_log("User login successful: " . $user_data['username']);
    
    return true;
}

/**
 * ออกจากระบบ
 */
function logoutUser() {
    safeSessionStart();
    
    // บันทึกการ logout ใน log
    if (isset($_SESSION['username'])) {
        error_log("User logout: " . $_SESSION['username']);
    }
    
    // ลบข้อมูลทั้งหมดใน session
    $_SESSION = array();
    
    // ลบ session cookie ถ้ามี
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // ทำลาย session
    session_destroy();
    
    return true;
}

/**
 * ตรวจสอบหมดเวลา session
 */
function checkSessionTimeout($timeout = 3600) { // 1 hour default
    if (isset($_SESSION['last_activity'])) {
        if ((time() - $_SESSION['last_activity']) > $timeout) {
            logoutUser();
            return true; // หมดเวลาแล้ว
        }
    }
    
    $_SESSION['last_activity'] = time();
    return false;
}

/**
 * บังคับให้เข้าสู่ระบบ
 */
function requireLogin($redirect_url = '?page=login') {
    if (!isLoggedIn()) {
        // ไม่ใช้ header redirect เพื่อป้องกัน headers already sent
        echo "<script>window.location.href = '" . BASE_URL . $redirect_url . "';</script>";
        echo '<meta http-equiv="refresh" content="0;url=' . BASE_URL . $redirect_url . '">';
        exit;
    }
    
    // ตรวจสอบหมดเวลา
    if (checkSessionTimeout()) {
        echo "<script>window.location.href = '" . BASE_URL . $redirect_url . "&timeout=1';</script>";
        echo '<meta http-equiv="refresh" content="0;url=' . BASE_URL . $redirect_url . '&timeout=1">';
        exit;
    }
}

/**
 * บังคับให้เป็น admin
 */
function requireAdmin($redirect_url = '?page=dashboard') {
    requireLogin();
    
    if (!isAdmin()) {
        $_SESSION['message'] = 'ท่านไม่มีสิทธิเข้าถึงหน้านี้';
        $_SESSION['message_type'] = 'error';
        echo "<script>window.location.href = '" . BASE_URL . $redirect_url . "';</script>";
        echo '<meta http-equiv="refresh" content="0;url=' . BASE_URL . $redirect_url . '">';
        exit;
    }
}

/**
 * สำหรับการ logout โดย URL parameter (สำรอง - ไม่ใช้แล้ว)
 */
function handleLogoutRequest() {
    if (isset($_GET['action']) && $_GET['action'] === 'logout') {
        logoutUser();
        
        // ใช้ JavaScript redirect แทน header
        echo "<script>window.location.href = '" . BASE_URL . "?page=login&logout=1';</script>";
        echo '<meta http-equiv="refresh" content="0;url=' . BASE_URL . '?page=login&logout=1">';
        exit;
    }
}

/**
 * แก้ไขการ redirect โดยไม่ใช้ header
 */
function safeRedirect($url) {
    if (!headers_sent()) {
        header("Location: " . $url);
        exit;
    } else {
        // ใช้ JavaScript และ meta refresh แทน
        echo "<script>window.location.href = '" . htmlspecialchars($url) . "';</script>";
        echo '<meta http-equiv="refresh" content="0;url=' . htmlspecialchars($url) . '">';
        echo '<p>กำลังนำทาง... <a href="' . htmlspecialchars($url) . '">กดที่นี้ถ้าไม่ถูกนำทางโดยอัตโนมัติ</a></p>';
        exit;
    }
}
?>