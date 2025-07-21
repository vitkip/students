<?php
// filepath: c:\xampp\htdocs\register-learning\src\classes\User.php
class User {
    private $conn;
    private $table_name = "users";
    
    public $id;
    public $username;
    public $password;
    public $role;
    public $full_name;
    public $email;
    public $created_at;
    public $last_login;
    public $is_active;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * ກວດສອບຂໍ້ມູນການເຂົ້າສູ່ລະບົບ
     * @param string $username
     * @param string $password
     * @return array|false
     */
    public function authenticate($username, $password) {
        try {
            $query = "SELECT id, username, password, role, full_name, email, is_active 
                     FROM " . $this->table_name . " 
                     WHERE username = :username AND is_active = 1 
                     LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $row = $stmt->fetch(PDO::FETCH_ASSOC);
                
                if (password_verify($password, $row['password'])) {
                    $this->updateLastLogin($row['id']);
                    unset($row['password']);
                    return $row;
                }
            }
            
            return false;
        } catch (PDOException $e) {
            error_log("Authentication error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ອັບເດດເວລາເຂົ້າສູ່ລະບົບຄັ້ງລ່າສຸດ
     */
    private function updateLastLogin($user_id) {
        try {
            $query = "UPDATE " . $this->table_name . " 
                     SET last_login = CURRENT_TIMESTAMP 
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Update last login error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ສ້າງຜູ້ໃຊ້ໃໝ່
     */
    public function create($username, $password, $role = 'user', $full_name = '', $email = '') {
        try {
            $query = "INSERT INTO " . $this->table_name . " 
                     (username, password, role, full_name, email) 
                     VALUES (:username, :password, :role, :full_name, :email)";
            
            $stmt = $this->conn->prepare($query);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':password', $hashed_password);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':full_name', $full_name);
            $stmt->bindParam(':email', $email);
            
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Create user error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ດຶງຂໍ້ມູນຜູ້ໃຊ້ຕາມ ID
     */
    public function readOne($id) {
        try {
            $query = "SELECT id, username, role, full_name, email, created_at, last_login, is_active 
                     FROM " . $this->table_name . " 
                     WHERE id = :id LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->rowCount() > 0 ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        } catch (PDOException $e) {
            error_log("Read user error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ກວດສອບວ່າຊື່ຜູ້ໃຊ້ມີໃນລະບົບແລ້ວຫຼືບໍ່
     * @param string $username
     * @return bool
     */
    public function usernameExists($username) {
        try {
            $query = "SELECT id FROM " . $this->table_name . " WHERE username = :username LIMIT 1";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Check username exists error: " . $e->getMessage());
            return false;
        }
    }
}
?>