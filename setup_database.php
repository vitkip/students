<?php
/**
 * ໄຟລ໌ສຳລັບສ້າງແລະທົດສອບຖານຂໍ້ມູນ
 */

// ກຳນົດພາດ
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>ກຳລັງທົດສອບການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ...</h2>";

// ການຕັ້ງຄ່າຖານຂໍ້ມູນ
$host = 'localhost';
$username = 'root';
$password = ''; // XAMPP default
$old_dbname = 'person';
$new_dbname = 'register_learning';

try {
    // ທົດສອບການເຊື່ອມຕໍ່ MySQL server
    echo "<p>1. ທົດສອບການເຊື່ອມຕໍ່ MySQL server...</p>";
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ ເຊື່ອມຕໍ່ MySQL server ສຳເລັດ</p>";

    // ກວດເບິ່ງວ່າມີຖານຂໍ້ມູນເກົ່າຫຼືບໍ່
    echo "<p>2. ກວດເບິ່ງຖານຂໍ້ມູນທີ່ມີຢູ່...</p>";
    $stmt = $pdo->query("SHOW DATABASES");
    $databases = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<ul>";
    foreach ($databases as $db) {
        echo "<li>$db</li>";
    }
    echo "</ul>";

    // ສ້າງຖານຂໍ້ມູນໃໝ່
    echo "<p>3. ສ້າງຖານຂໍ້ມູນ '$new_dbname'...</p>";
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$new_dbname` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci");
    echo "<p style='color: green;'>✓ ສ້າງຖານຂໍ້ມູນ '$new_dbname' ສຳເລັດ</p>";

    // ເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນໃໝ່
    echo "<p>4. ເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນ '$new_dbname'...</p>";
    $pdo = new PDO("mysql:host=$host;dbname=$new_dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✓ ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ '$new_dbname' ສຳເລັດ</p>";

    // ສ້າງຕາຕະລາງ
    echo "<p>5. ສ້າງຕາຕະລາງ...</p>";
    
    // ຕາຕະລາງປີການສຶກສາ
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `academic_years` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `year` varchar(9) NOT NULL,
            `is_active` tinyint(1) DEFAULT 1,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "<p style='color: green;'>✓ ສ້າງຕາຕະລາງ 'academic_years' ສຳເລັດ</p>";

    // ຕາຕະລາງສາຂາວິຊາ
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `majors` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `name` varchar(200) NOT NULL,
            `code` varchar(10) DEFAULT NULL,
            `description` text,
            `is_active` tinyint(1) DEFAULT 1,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `code` (`code`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "<p style='color: green;'>✓ ສ້າງຕາຕະລາງ 'majors' ສຳເລັດ</p>";

    // ຕາຕະລາງນັກຮຽນ
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `students` (
            `id` int(11) NOT NULL AUTO_INCREMENT,
            `student_id` varchar(20) DEFAULT NULL,
            `first_name` varchar(100) NOT NULL,
            `last_name` varchar(100) NOT NULL,
            `gender` enum('ພຣະ','ສ.ນ','ຊາຍ','ຍິງ','ອຶ່ນໆ') NOT NULL,
            `dob` date NOT NULL,
            `email` varchar(150) DEFAULT NULL,
            `phone` varchar(20) DEFAULT NULL,
            `village` varchar(100) DEFAULT NULL,
            `district` varchar(100) DEFAULT NULL,
            `province` varchar(100) DEFAULT NULL,
            `accommodation_type` enum('ຫາວັດໃຫ້','ມີວັດຢູ່ແລ້ວ') DEFAULT 'ມີວັດຢູ່ແລ້ວ',
            `photo` varchar(255) DEFAULT NULL,
            `previous_school` varchar(255) DEFAULT NULL,
            `major_id` int(11) DEFAULT NULL,
            `academic_year_id` int(11) DEFAULT NULL,
            `status` enum('active','inactive','graduated') DEFAULT 'active',
            `registered_at` datetime DEFAULT CURRENT_TIMESTAMP,
            `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `student_id` (`student_id`),
            KEY `major_id` (`major_id`),
            KEY `academic_year_id` (`academic_year_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");
    echo "<p style='color: green;'>✓ ສ້າງຕາຕະລາງ 'students' ສຳເລັດ</p>";

    // ໃສ່ຂໍ້ມູນຕົວຢ່າງ
    echo "<p>6. ໃສ່ຂໍ້ມູນຕົວຢ່າງ...</p>";
    
    // ຂໍ້ມູນປີການສຶກສາ
    $pdo->exec("
        INSERT IGNORE INTO `academic_years` (`year`, `is_active`) VALUES
        ('2025-2026', 1),
        ('2026-2027', 0),
        ('2027-2028', 0)
    ");
    echo "<p style='color: green;'>✓ ໃສ່ຂໍ້ມູນປີການສຶກສາສຳເລັດ</p>";

    // ຂໍ້ມູນສາຂາວິຊາ
    $pdo->exec("
        INSERT IGNORE INTO `majors` (`name`, `code`, `description`, `is_active`) VALUES
        ('ສາຍ ຄູພຸດທະສາດສະໜາ ແລະ ພາສາລາວ-ວັນນະຄະດີ', 'BL', 'ສາຂາວິຊາພຸດທະສາດສະໜາ ແລະ ພາສາລາວ-ວັນນະຄະດີ', 1),
        ('ສາຍຄູ ພາສາອັງກິດ', 'ENG', 'ສາຂາວິຊາພາສາອັງກິດ', 1),
        ('ຕໍ່ເນື່ອງ ສາຍ ຄູພຸດທະສາດສະໜາ-ພາສາລາວ', 'LINK', 'ສາຂາວິຊາຕໍ່ເນື່ອງພຸດທະສາດສະໜາ-ພາສາລາວ', 1)
    ");
    echo "<p style='color: green;'>✓ ໃສ່ຂໍ້ມູນສາຂາວິຊາສຳເລັດ</p>";

    // ກວດເບິ່ງຂໍ້ມູນ
    echo "<p>7. ກວດເບິ່ງຂໍ້ມູນທີ່ສ້າງແລ້ວ...</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM academic_years");
    $count = $stmt->fetch()['count'];
    echo "<p>ຈຳນວນປີການສຶກສາ: $count ແຖວ</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM majors");
    $count = $stmt->fetch()['count'];
    echo "<p>ຈຳນວນສາຂາວິຊາ: $count ແຖວ</p>";
    
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM students");
    $count = $stmt->fetch()['count'];
    echo "<p>ຈຳນວນນັກຮຽນ: $count ແຖວ</p>";

    echo "<h3 style='color: green;'>🎉 ສ້າງຖານຂໍ້ມູນສຳເລັດແລ້ວ!</h3>";
    echo "<p><a href='index.php'>ກັບໄປໜ້າຫຼັກ</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ ຜິດພາດ: " . $e->getMessage() . "</p>";
    echo "<h3>ວິທີແກ້ໄຂ:</h3>";
    echo "<ol>";
    echo "<li>ກວດເບິ່ງວ່າ XAMPP ເປີດໃຊ້ MySQL ແລ້ວຫຼືບໍ່</li>";
    echo "<li>ກວດເບິ່ງການຕັ້ງຄ່າໃນ config/database.php</li>";
    echo "<li>ລອງເປີດ phpMyAdmin ເພື່ອກວດເບິ່ງການເຊື່ອມຕໍ່</li>";
    echo "</ol>";
}
?>