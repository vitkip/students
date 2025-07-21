<?php
// filepath: c:\xampp\htdocs\register-learning\src\classes\Major.php

/**
 * Major class - จัดการข้อมูลสาขาวิชา
 */
class Major {
    private $conn;
    private $table_name = "majors";
    
    // คุณสมบัติของวัตถุ
    public $id;
    public $name;
    public $description;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * อ่านข้อมูลสาขาทั้งหมด
     * 
     * @return array รายการสาขาทั้งหมด
     */
    public function readAll() {
        try {
            $query = "SELECT id, name, description FROM " . $this->table_name . " ORDER BY name";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Database error in Major::readAll(): " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * อ่านข้อมูลสาขาทีละรายการ
     * 
     * @param int $id ID ของสาขา
     * @return array|false ข้อมูลสาขา
     */
    public function readOne($id) {
        try {
            $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($row) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->description = $row['description'];
                return $row;
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Database error in Major::readOne(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ดึงจำนวนสาขาทั้งหมด
     */
    public function getTotalCount() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['total'] ?? 0;
        } catch (PDOException $e) {
            error_log("Database error in Major::getTotalCount(): " . $e->getMessage());
            return 0;
        }
    }
}
?>