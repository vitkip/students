<?php
// filepath: /register-learning/register-learning/src/classes/AcademicYear.php

/**
 * ຄລາສຈັດການຂໍ້ມູນປີການສຶກສາ
 */
class AcademicYear {
    private $conn;
    private $table_name = "academic_years";
    
    // ຄຸນສົມບັດຂອງວັດຖຸ
    public $id;
    public $year;
    
    /**
     * Constructor - ເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
     * @param PDO $db ການເຊື່ອມຕໍ່ຖານຂໍ້ມູນ
     */
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * ດຶງຂໍ້ມູນປີການສຶກສາທັງໝົດ
     * @return array ລາຍການປີການສຶກສາ
     */
    public function readAll() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY year DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Database error in AcademicYear::readAll(): " . $e->getMessage());
            return [];
        }
    }
    
    
    /**
     * ດຶງຂໍ້ມູນປີການສຶກສາຕາມ ID
     * @param int $id ລະຫັດປີການສຶກສາ
     * @return array ຂໍ້ມູນປີການສຶກສາ
     */
    public function readOne($id) {
        $query = "SELECT * FROM academic_years WHERE id = :id LIMIT 0,1";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $row ? $row : [];
    }
    
    /**
     * ດຶງຂໍ້ມູນປີການສຶກສາຕາມ ID (ສໍາລັບການເອົາໃຊ້ໃນລະບົບ)
     * @param int $id ລະຫັດປີການສຶກສາ
     * @return array ຂໍ້ມູນປີການສຶກສາ
     */
    public function readById($id) {
        // เรียกใช้เมธอด readOne ที่มีอยู่แล้ว
        return $this->readOne($id);
    }
    
    /**
     * ดึงจำนวนปีการศึกษาทั้งหมด
     */
    public function getTotalCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Database error in AcademicYear::getTotalCount(): " . $e->getMessage());
            return 0;
        }
    }
}
?>