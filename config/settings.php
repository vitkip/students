<?php
// filepath: c:\xampp\htdocs\register-learning\config\settings.php

/**
 * ไฟล์การตั้งค่าระบบ
 */

class Settings {
    private $conn;
    private $table_name = "settings";
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * ดึงค่าการตั้งค่า
     */
    public function get($key, $default = null) {
        try {
            $query = "SELECT setting_value FROM " . $this->table_name . " WHERE setting_key = :key LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':key', $key);
            $stmt->execute();
            
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $row['setting_value'] : $default;
        } catch (PDOException $e) {
            error_log("Database error in Settings::get(): " . $e->getMessage());
            return $default;
        }
    }
    
    /**
     * บันทึกค่าการตั้งค่า
     */
    public function set($key, $value, $description = null) {
        try {
            $query = "INSERT INTO " . $this->table_name . " (setting_key, setting_value, description) 
                      VALUES (:key, :value, :description)
                      ON DUPLICATE KEY UPDATE 
                      setting_value = :value2, description = :description2";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':key', $key);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':description', $description);
            $stmt->bindParam(':value2', $value);
            $stmt->bindParam(':description2', $description);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Database error in Settings::set(): " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ดึงการตั้งค่าทั้งหมด
     */
    public function getAll() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY setting_key";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            $settings = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $settings[$row['setting_key']] = $row['setting_value'];
            }
            
            return $settings;
        } catch (PDOException $e) {
            error_log("Database error in Settings::getAll(): " . $e->getMessage());
            return [];
        }
    }
}
?>