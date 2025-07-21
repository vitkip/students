<?php
// filepath: c:\xampp\htdocs\register-learning\src\classes\Student.php

class Student {
    private $conn;
    private $table_name = "students";
    
    // Properties
    public $id;
    public $student_id;
    public $first_name;
    public $last_name;
    public $gender;
    public $date_of_birth;
    public $place_of_birth;
    public $phone;
    public $email;
    public $address;
    public $major_id;
    public $academic_year_id;
    public $photo;
    public $guardian_name;
    public $guardian_phone;
    public $guardian_relationship;
    public $created_at;
    public $updated_at;
    
    public function __construct($db) {
        $this->conn = $db;
    }
    
    /**
     * ອ່ານຂໍ້ມູນນັກສຶກສາທັງໝົດພ້ອມຕົວກອງ
     */
    public function readAllWithFilter($search = '', $major_id = 0, $year_id = 0, $limit = 10, $offset = 0) {
        try {
            $query = "SELECT s.*, 
                            m.name as major_name,
                            ay.year as academic_year
                     FROM " . $this->table_name . " s
                     LEFT JOIN majors m ON s.major_id = m.id
                     LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                     WHERE 1=1";
            
            $params = [];
            
            // Add search condition
            if (!empty($search)) {
                $query .= " AND (s.first_name LIKE :search 
                               OR s.last_name LIKE :search 
                               OR s.student_id LIKE :search
                               OR CONCAT(s.first_name, ' ', s.last_name) LIKE :search)";
                $params[':search'] = '%' . $search . '%';
            }
            
            // Add major filter
            if ($major_id > 0) {
                $query .= " AND s.major_id = :major_id";
                $params[':major_id'] = $major_id;
            }
            
            // Add academic year filter
            if ($year_id > 0) {
                $query .= " AND s.academic_year_id = :year_id";
                $params[':year_id'] = $year_id;
            }
            
            $query .= " ORDER BY s.created_at DESC, s.first_name ASC";
            
            // Add pagination
            if ($limit > 0) {
                $query .= " LIMIT :limit OFFSET :offset";
                $params[':limit'] = $limit;
                $params[':offset'] = $offset;
            }
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                if ($key === ':limit' || $key === ':offset' || $key === ':major_id' || $key === ':year_id') {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value, PDO::PARAM_STR);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Read students with filter error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ນັບຈຳນວນນັກສຶກສາທັງໝົດພ້ອມຕົວກອງ
     */
    public function countWithFilter($search = '', $major_id = 0, $year_id = 0) {
        try {
            $query = "SELECT COUNT(*) as total 
                     FROM " . $this->table_name . " s
                     WHERE 1=1";
            
            $params = [];
            
            // Add search condition
            if (!empty($search)) {
                $query .= " AND (s.first_name LIKE :search 
                               OR s.last_name LIKE :search 
                               OR s.student_id LIKE :search
                               OR CONCAT(s.first_name, ' ', s.last_name) LIKE :search)";
                $params[':search'] = '%' . $search . '%';
            }
            
            // Add major filter
            if ($major_id > 0) {
                $query .= " AND s.major_id = :major_id";
                $params[':major_id'] = $major_id;
            }
            
            // Add academic year filter
            if ($year_id > 0) {
                $query .= " AND s.academic_year_id = :year_id";
                $params[':year_id'] = $year_id;
            }
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            foreach ($params as $key => $value) {
                if ($key === ':major_id' || $key === ':year_id') {
                    $stmt->bindValue($key, $value, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value, PDO::PARAM_STR);
                }
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
            
        } catch (PDOException $e) {
            error_log("Count students with filter error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * ອ່ານຂໍ້ມູນນັກສຶກສາທັງໝົດ (ແບບງ່າຍ)
     */
    public function readAll() {
        try {
            $query = "SELECT s.*, 
                            m.name as major_name,
                            ay.year as academic_year
                     FROM " . $this->table_name . " s
                     LEFT JOIN majors m ON s.major_id = m.id
                     LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                     ORDER BY s.created_at DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Read all students error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ອ່ານຂໍ້ມູນນັກສຶກສາຄົນດຽວ
     */
    public function read($id) {
        try {
            $query = "SELECT s.*, 
                            m.name as major_name,
                            ay.year as academic_year
                     FROM " . $this->table_name . " s
                     LEFT JOIN majors m ON s.major_id = m.id
                     LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                     WHERE s.id = :id
                     LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Read student error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ສ້າງນັກສຶກສາໃໝ່
     */
    public function create() {
        try {
            $query = "INSERT INTO " . $this->table_name . "
                     SET student_id = :student_id,
                         first_name = :first_name,
                         last_name = :last_name,
                         gender = :gender,
                         date_of_birth = :date_of_birth,
                         place_of_birth = :place_of_birth,
                         phone = :phone,
                         email = :email,
                         address = :address,
                         major_id = :major_id,
                         academic_year_id = :academic_year_id,
                         photo = :photo,
                         guardian_name = :guardian_name,
                         guardian_phone = :guardian_phone,
                         guardian_relationship = :guardian_relationship,
                         created_at = NOW()";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':student_id', $this->student_id);
            $stmt->bindParam(':first_name', $this->first_name);
            $stmt->bindParam(':last_name', $this->last_name);
            $stmt->bindParam(':gender', $this->gender);
            $stmt->bindParam(':date_of_birth', $this->date_of_birth);
            $stmt->bindParam(':place_of_birth', $this->place_of_birth);
            $stmt->bindParam(':phone', $this->phone);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':address', $this->address);
            $stmt->bindParam(':major_id', $this->major_id);
            $stmt->bindParam(':academic_year_id', $this->academic_year_id);
            $stmt->bindParam(':photo', $this->photo);
            $stmt->bindParam(':guardian_name', $this->guardian_name);
            $stmt->bindParam(':guardian_phone', $this->guardian_phone);
            $stmt->bindParam(':guardian_relationship', $this->guardian_relationship);
            
            if ($stmt->execute()) {
                $this->id = $this->conn->lastInsertId();
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Create student error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ແກ້ໄຂຂໍ້ມູນນັກສຶກສາ
     */
    public function update() {
        try {
            $query = "UPDATE " . $this->table_name . "
                     SET student_id = :student_id,
                         first_name = :first_name,
                         last_name = :last_name,
                         gender = :gender,
                         date_of_birth = :date_of_birth,
                         place_of_birth = :place_of_birth,
                         phone = :phone,
                         email = :email,
                         address = :address,
                         major_id = :major_id,
                         academic_year_id = :academic_year_id,
                         photo = :photo,
                         guardian_name = :guardian_name,
                         guardian_phone = :guardian_phone,
                         guardian_relationship = :guardian_relationship,
                         updated_at = NOW()
                     WHERE id = :id";
            
            $stmt = $this->conn->prepare($query);
            
            // Bind parameters
            $stmt->bindParam(':id', $this->id);
            $stmt->bindParam(':student_id', $this->student_id);
            $stmt->bindParam(':first_name', $this->first_name);
            $stmt->bindParam(':last_name', $this->last_name);
            $stmt->bindParam(':gender', $this->gender);
            $stmt->bindParam(':date_of_birth', $this->date_of_birth);
            $stmt->bindParam(':place_of_birth', $this->place_of_birth);
            $stmt->bindParam(':phone', $this->phone);
            $stmt->bindParam(':email', $this->email);
            $stmt->bindParam(':address', $this->address);
            $stmt->bindParam(':major_id', $this->major_id);
            $stmt->bindParam(':academic_year_id', $this->academic_year_id);
            $stmt->bindParam(':photo', $this->photo);
            $stmt->bindParam(':guardian_name', $this->guardian_name);
            $stmt->bindParam(':guardian_phone', $this->guardian_phone);
            $stmt->bindParam(':guardian_relationship', $this->guardian_relationship);
            
            return $stmt->execute();
            
        } catch (PDOException $e) {
            error_log("Update student error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ລຶບນັກສຶກສາ
     */
    public function delete($id) {
        try {
            // Get student data first to delete photo
            $student_data = $this->read($id);
            
            $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                // Delete photo file if exists
                if ($student_data && !empty($student_data['photo'])) {
                    $photo_path = BASE_PATH . '/public/uploads/photos/' . $student_data['photo'];
                    if (file_exists($photo_path)) {
                        unlink($photo_path);
                    }
                }
                return true;
            }
            
            return false;
            
        } catch (PDOException $e) {
            error_log("Delete student error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ນັບຈຳນວນນັກສຶກສາທັງໝົດ
     */
    public function count() {
        try {
            $query = "SELECT COUNT(*) as total FROM " . $this->table_name;
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)$result['total'];
        } catch (PDOException $e) {
            error_log("Count students error: " . $e->getMessage());
            return 0;
        }
    }
    
    /**
     * ຕໍ່ອັດຕະໂນມັດສ້າງລະຫັດນັກສຶກສາ
     */
    public function generateStudentId($major_id, $year) {
        try {
            // Get major code
            $major_query = "SELECT code FROM majors WHERE id = :major_id";
            $major_stmt = $this->conn->prepare($major_query);
            $major_stmt->bindParam(':major_id', $major_id);
            $major_stmt->execute();
            $major_data = $major_stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$major_data) {
                return false;
            }
            
            $major_code = $major_data['code'];
            $year_suffix = substr($year, -2); // Last 2 digits of year
            
            // Find the next sequential number
            $count_query = "SELECT COUNT(*) as count 
                           FROM " . $this->table_name . " 
                           WHERE student_id LIKE :pattern";
            $pattern = $major_code . $year_suffix . '%';
            $count_stmt = $this->conn->prepare($count_query);
            $count_stmt->bindParam(':pattern', $pattern);
            $count_stmt->execute();
            $count_data = $count_stmt->fetch(PDO::FETCH_ASSOC);
            
            $next_number = str_pad($count_data['count'] + 1, 3, '0', STR_PAD_LEFT);
            
            return $major_code . $year_suffix . $next_number;
            
        } catch (PDOException $e) {
            error_log("Generate student ID error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ກວດສອບວ່າມີລະຫັດນັກສຶກສານີ້ແລ້ວຫຼືບໍ່
     */
    public function studentIdExists($student_id, $exclude_id = null) {
        try {
            $query = "SELECT id FROM " . $this->table_name . " WHERE student_id = :student_id";
            
            if ($exclude_id) {
                $query .= " AND id != :exclude_id";
            }
            
            $query .= " LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            
            if ($exclude_id) {
                $stmt->bindParam(':exclude_id', $exclude_id);
            }
            
            $stmt->execute();
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Check student ID exists error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ຄົ້ນຫານັກສຶກສາດ້ວຍລະຫັດນັກສຶກສາ
     */
    public function readByStudentId($student_id) {
        try {
            $query = "SELECT s.*, 
                            m.name as major_name,
                            ay.year as academic_year
                     FROM " . $this->table_name . " s
                     LEFT JOIN majors m ON s.major_id = m.id
                     LEFT JOIN academic_years ay ON s.academic_year_id = ay.id
                     WHERE s.student_id = :student_id
                     LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':student_id', $student_id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            error_log("Read student by ID error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ກວດສອບວ່ามີອີເມວນີ້ແລ້ວຫຼືບໍ່
     */
    public function emailExists($email, $exclude_id = null) {
        try {
            if (empty($email)) return false;
            
            $query = "SELECT id FROM " . $this->table_name . " WHERE email = :email";
            
            if ($exclude_id) {
                $query .= " AND id != :exclude_id";
            }
            
            $query .= " LIMIT 1";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':email', $email);
            
            if ($exclude_id) {
                $stmt->bindParam(':exclude_id', $exclude_id);
            }
            
            $stmt->execute();
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            error_log("Check email exists error: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * ສ້າງ backup ຂໍ້ມູນ
     */
    public function backup() {
        try {
            $query = "SELECT * FROM " . $this->table_name . " ORDER BY id";
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Backup students error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ສະຖິຕິນັກສຶກສາຕາມສາຂາ
     */
    public function getStatsByMajor() {
        try {
            $query = "SELECT m.name as major_name, COUNT(s.id) as student_count
                     FROM majors m
                     LEFT JOIN " . $this->table_name . " s ON m.id = s.major_id
                     GROUP BY m.id, m.name
                     ORDER BY student_count DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get stats by major error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ສະຖິຕິນັກສຶກສາຕາມປີການສຶກສາ
     */
    public function getStatsByYear() {
        try {
            $query = "SELECT ay.year as academic_year, COUNT(s.id) as student_count
                     FROM academic_years ay
                     LEFT JOIN " . $this->table_name . " s ON ay.id = s.academic_year_id
                     GROUP BY ay.id, ay.year
                     ORDER BY ay.year DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get stats by year error: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * ສະຖິຕິນັກສຶກສາຕາມເພດ
     */
    public function getStatsByGender() {
        try {
            $query = "SELECT gender, COUNT(*) as count
                     FROM " . $this->table_name . "
                     GROUP BY gender
                     ORDER BY count DESC";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get stats by gender error: " . $e->getMessage());
            return [];
        }
    }
}
?>