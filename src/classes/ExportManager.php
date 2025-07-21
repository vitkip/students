<?php
// เพิ่ม PhpSpreadsheet เข้ามาใช้งาน
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportManager {
    private $conn;
    private $spreadsheet;
    
    public function __construct($db) {
        $this->conn = $db;
        $this->spreadsheet = new Spreadsheet();
    }
    
    /**
     * ສົ່ງອອກຂໍ້ມູນນັກສຶກສາທັງໝົດ
     */
    public function exportAllStudents() {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle('ລາຍຊື່ນັກສຶກສາທັງໝົດ');
        
        // ຕັ້ງຫົວຂໍ້ເອກະສານ
        $sheet->setCellValue('A1', 'ລາຍງານຂໍ້ມູນນັກສຶກສາທັງໝົດ');
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // ກຳນົດຫົວຕາຕະລາງ
        $headers = [
            'A2' => 'ລຳດັບ',
            'B2' => 'ເພດ',
            'C2' => 'ຊື',
            'D2' => 'ນາມສະກຸນ',
            'E2' => 'ວັນເກີດ',
            'F2' => 'ເບີໂທ',
            'G2' => 'ສາຂາຮຽນ',
            'H2' => 'ປີການສຶກສາ',
            'I2' => 'ທີ່ພັກອາໄສ'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // ຕົກແຕ່ງຫົວຕາຕະລາງ
        $sheet->getStyle('A2:I2')->getFont()->setBold(true);
        $sheet->getStyle('A2:I2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
        $sheet->getStyle('A2:I2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:I2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // ດຶງຂໍ້ມູນນັກສຶກສາ
        $query = "SELECT s.*, m.name as major_name, a.year as academic_year 
                 FROM students s 
                 LEFT JOIN majors m ON s.major_id = m.id 
                 LEFT JOIN academic_years a ON s.academic_year_id = a.id 
                 ORDER BY s.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // ເພີ່ມຂໍ້ມູນລົງໃນ Excel
        $row = 3;
        foreach ($students as $index => $student) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $student['gender']);
            $sheet->setCellValue('C' . $row, $student['first_name']);
            $sheet->setCellValue('D' . $row, $student['last_name']);
            $sheet->setCellValue('E' . $row, $student['dob']);
            $sheet->setCellValue('F' . $row, $student['phone']);
            $sheet->setCellValue('G' . $row, $student['major_name']);
            $sheet->setCellValue('H' . $row, $student['academic_year']);
            $sheet->setCellValue('I' . $row, $student['accommodation_type']);
            
            // ຕົກແຕ່ງຂອບຕາຕະລາງ
            $sheet->getStyle('A' . $row . ':I' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            $row++;
        }
        
        // ປັບຂະໜາດຄໍລຳໃຫ້ພໍດີກັບເນື້ອຫາ
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // ສົ່ງອອກໄຟລ
        $this->outputExcel('ລາຍງານນັກສຶກສາທັງໝົດ.xlsx');
    }
    
    /**
     * ສົ່ງອອກຂໍ້ມູນນັກສຶກສາຕາມສາຂາ
     * @param int $majorId ລະຫັດສາຂາ
     */
    public function exportStudentsByMajor($majorId) {
        // ດຶງຂໍ້ມູນສາຂາ
        $majorQuery = "SELECT * FROM majors WHERE id = :id";
        $majorStmt = $this->conn->prepare($majorQuery);
        $majorStmt->bindParam(':id', $majorId);
        $majorStmt->execute();
        $major = $majorStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$major) {
            throw new Exception("ບໍ່ພົບຂໍ້ມູນສາຂາ");
        }
        
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle('ລາຍຊື່ນັກສຶກສາ ' . $major['name']);
        
        // ຕັ້ງຫົວຂໍ້ເອກະສານ
        $sheet->setCellValue('A1', 'ລາຍງານຂໍ້ມູນນັກສຶກສາສາຂາ ' . $major['name']);
        $sheet->mergeCells('A1:I1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // ກຳນົດຫົວຕາຕະລາງ
        $headers = [
            'A2' => 'ລຳດັບ',
            'B2' => 'ເພັດ',
            'C2' => 'ຊື່',
            'D2' => 'ນາມສະກຸນ',
            'E2' => 'ວັນເກີດ',
            'F2' => 'ເບີໂທ',
            'G2' => 'ປີການສຶກສາ',
            'H2' => 'ທີ່ພັກອາໄສ'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // ຕົກແຕ່ງຫົວຕາຕະລາງ
        $sheet->getStyle('A2:H2')->getFont()->setBold(true);
        $sheet->getStyle('A2:H2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
        $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:H2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // ດຶງຂໍ້ມູນນັກສຶກສາ
        $query = "SELECT s.*, a.year as academic_year 
                 FROM students s 
                 LEFT JOIN academic_years a ON s.academic_year_id = a.id 
                 WHERE s.major_id = :major_id
                 ORDER BY s.id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':major_id', $majorId);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // ເພີ່ມຂໍ້ມູນລົງໃນ Excel
        $row = 3;
        foreach ($students as $index => $student) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $student['gender']);
            $sheet->setCellValue('C' . $row, $student['first_name']);
            $sheet->setCellValue('D' . $row, $student['last_name']);
            $sheet->setCellValue('E' . $row, $student['dob']);
            $sheet->setCellValue('F' . $row, $student['phone']);
            $sheet->setCellValue('G' . $row, $student['academic_year']);
            $sheet->setCellValue('H' . $row, $student['accommodation_type']);
            
            // ຕົກແຕ່ງຂອບຕາຕະລາງ
            $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            $row++;
        }
        
        // ປັບຂະໜາດຄໍລຳໃຫ້ພໍດີກັບເນື້ອຫາ
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // ສົ່ງອອກໄຟລ
        $this->outputExcel('ລາຍງານນັກສຶກສາສາຂາ_' . $major['name'] . '.xlsx');
    }
    
    /**
     * ສົ່ງອອກຂໍ້ມູນນັກສຶກສາຕາມປີການສຶກສາ
     * @param int $yearId ລະຫັດປີການສຶກສາ
     */
    public function exportStudentsByYear($yearId) {
        // ດຶງຂໍ້ມູນປີການສຶກສາ
        $yearQuery = "SELECT * FROM academic_years WHERE id = :id";
        $yearStmt = $this->conn->prepare($yearQuery);
        $yearStmt->bindParam(':id', $yearId);
        $yearStmt->execute();
        $year = $yearStmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$year) {
            throw new Exception("ບໍ່ພົບຂໍ້ມູນປີການສຶກສາ");
        }
        
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle('ລາຍຊື່ນັກສຶກສາ ' . $year['year']);
        
        // ຕັ້ງຫົວຂໍ້ເອກະສານ
        $sheet->setCellValue('A1', 'ລາຍງານຂໍ້ມູນນັກສຶກສາປີການສຶກສາ ' . $year['year']);
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // ກຳນົດຫົວຕາຕະລາງ
        $headers = [
            'A2' => 'ລຳດັບ',
            'B2' => 'ເພດ',
            'C2' => 'ຊື່',
            'D2' => 'ນາມສະກຸນ',
            'E2' => 'ເບີໂທ',
            'F2' => 'ສາຂາຮຽນ',
            'G2' => 'ທີ່ພັກອາໄສ',
            'H2' => 'ວັນທີລົງທະບຽນ'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // ຕົກແຕ່ງຫົວຕາຕະລາງ
        $sheet->getStyle('A2:H2')->getFont()->setBold(true);
        $sheet->getStyle('A2:H2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
        $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:H2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // ດຶງຂໍ້ມູນນັກສຶກສາ
        $query = "SELECT s.*, m.name as major_name 
                 FROM students s 
                 LEFT JOIN majors m ON s.major_id = m.id 
                 WHERE s.academic_year_id = :year_id
                 ORDER BY s.id";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':year_id', $yearId);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // ເພີ່ມຂໍ້ມູນລົງໃນ Excel
        $row = 3;
        foreach ($students as $index => $student) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $student['gender']); 
            $sheet->setCellValue('C' . $row, $student['first_name']);
            $sheet->setCellValue('D' . $row, $student['last_name']);
            $sheet->setCellValue('E' . $row, $student['phone'] ?? '-');
            $sheet->setCellValue('F' . $row, $student['major_name']);
            $sheet->setCellValue('G' . $row, $student['accommodation_type']);
            $sheet->setCellValue('H' . $row, $student['registered_at'] ?? '-');
            
            // ຕົກແຕ່ງຂອບຕາຕະລາງ
            $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            $row++;
        }
        
        // ປັບຂະໜາດຄໍລຳໃຫ້ພໍດີກັບເນື້ອຫາ
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // ສົ່ງອອກໄຟລ
        $this->outputExcel('ລາຍງານນັກສຶກສາປີ_' . $year['year'] . '.xlsx');
    }
    
    /**
     * ສົ່ງອອກຂໍ້ມູນນັກສຶກສາຕາມທີ່ພັກອາໄສ
     * @param string $accommodationType ປະເພດທີ່ພັກອາໄສ
     */
    public function exportStudentsByAccommodation($accommodationType) {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setTitle('ນັກສຶກສາ ' . $accommodationType);
        
        // ຕັ້ງຫົວຂໍ້ເອກະສານ
        $sheet->setCellValue('A1', 'ລາຍງານຂໍ້ມູນນັກສຶກສາປະເພດທີ່ພັກ ' . $accommodationType);
        $sheet->mergeCells('A1:H1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        
        // ກຳນົດຫົວຕາຕະລາງ
        $headers = [
            'A2' => 'ລຳດັບ',
            'B2' => 'ເພດ',
            'C2' => 'ຊື່',
            'D2' => 'ນາມສະກຸນ',
            'E2' => 'ເບີໂທ',
            'F2' => 'ສາຂາຮຽນ',
            'G2' => 'ປີການສຶກສາ',
            'H2' => 'ວັນທີລົງທະບຽນ'
        ];
        
        foreach ($headers as $cell => $value) {
            $sheet->setCellValue($cell, $value);
        }
        
        // ຕົກແຕ່ງຫົວຕາຕະລາງ
        $sheet->getStyle('A2:H2')->getFont()->setBold(true);
        $sheet->getStyle('A2:H2')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('D9EAD3');
        $sheet->getStyle('A2:H2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2:H2')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        
        // ດຶງຂໍ້ມູນນັກສຶກສາ
        $query = "SELECT s.*, m.name as major_name, a.year as academic_year 
                 FROM students s 
                 LEFT JOIN majors m ON s.major_id = m.id 
                 LEFT JOIN academic_years a ON s.academic_year_id = a.id 
                 WHERE s.accommodation_type = :accommodation_type
                 ORDER BY s.id";
    
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':accommodation_type', $accommodationType);
        $stmt->execute();
        $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // ເພີ່ມຂໍ້ມູນລົງໃນ Excel
        $row = 3;
        foreach ($students as $index => $student) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $student['gender']);
            $sheet->setCellValue('C' . $row, $student['first_name']);
            $sheet->setCellValue('D' . $row, $student['last_name']);
            $sheet->setCellValue('E' . $row, $student['phone'] ?? '-');
            $sheet->setCellValue('F' . $row, $student['major_name']);
            $sheet->setCellValue('G' . $row, $student['academic_year']);
            $sheet->setCellValue('H' . $row, $student['registered_at'] ?? '-');
            
            // ຕົກແຕ່ງຂອບຕາຕະລາງ
            $sheet->getStyle('A' . $row . ':H' . $row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            
            $row++;
        }
        
        // ປັບຂະໜາດຄໍລຳໃຫ້ພໍດີກັບເນື້ອຫາ
        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // ສົ່ງອອກໄຟລ
        $this->outputExcel('ລາຍງານນັກສຶກສາທີ່ພັກ_' . str_replace(' ', '_', $accommodationType) . '.xlsx');
    }
    
    /**
     * ສົ່ງອອກໄຟລ Excel ໄປຍັງຜູ້ໃຊ້
     * @param string $filename ຊື່ໄຟລ
     */
    private function outputExcel($filename) {
        // เพิ่มการเคลียร์ buffer เพื่อป้องกันการส่งข้อมูลอื่นไปยังไฟล์
        if (ob_get_length()) {
            ob_end_clean();
        }
        
        // ກຳນົດ header ເພື່ອດາວໂຫລດ
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
        header('Cache-Control: cache, must-revalidate');
        header('Pragma: public');
        
        // ຂຽນໄຟລ Excel
        $writer = new Xlsx($this->spreadsheet);
        $writer->save('php://output');
        exit;
    }
}