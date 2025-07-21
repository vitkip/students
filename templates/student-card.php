<?php
// filepath: c:\xampp\htdocs\register-learning\templates\student-card.php

// ตรวจสอบว่ามีการเรียกใช้ผ่าน index.php หรือไม่
if (!defined('BASE_PATH')) {
    header('Location: ../public/index.php?page=students');
    exit('Access denied. Please use proper navigation.');
}

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!isset($db) || !$db) {
    die('Database connection not available');
}

// ตรวจสอบว่ามีข้อมูลนักศึกษาหรือไม่
if (!isset($student_data) || !$student_data) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-md mx-auto mt-10">';
    echo '<h3 class="font-bold text-lg mb-2">ຂໍ້ຜິດພາດ!</h3>';
    echo '<p>ບໍ່ສາມາດດຶງຂໍ້ມູນນັກສຶກສາໄດ້</p>';
    echo '<div class="mt-4">';
    echo '<a href="' . BASE_URL . 'index.php?page=students" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">ກັບຄືນຫາລາຍຊື່</a>';
    echo '</div>';
    echo '</div>';
    exit;
}

// โหลด TCPDF
require_once BASE_PATH . '/vendor/autoload.php';

try {
    // สร้างโฟลเดอร์ temp ถ้าไม่มี
    $tempDir = BASE_PATH . '/temp';
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0755, true);
    }

    // กำหนดฟอนต์สำหรับ TCPDF (ใช้ฟอนต์ Lao + Emoji Support)
    $laoFontPath = BASE_PATH . '/public/fonts/PhetsarathOT.ttf';
    $laoFontBoldPath = BASE_PATH . '/public/fonts/PhetsarathOT_Bold.ttf';
    $fontname = 'dejavusans'; // ฟอนต์ default ถ้าไม่มี PhetsarathOT
    $fontnameBold = 'dejavusansb'; // ฟอนต์ Bold default
    $emojiFontname = 'dejavusans'; // ฟอนต์สำหรับ emoji
    
    // ตรวจสอบและเพิ่ม PhetsarathOT font ถ้ามี
    if (file_exists($laoFontPath)) {
        try {
            $fontname = TCPDF_FONTS::addTTFfont($laoFontPath, 'TrueTypeUnicode', '', 96);
        } catch (Exception $e) {
            error_log("Font loading error: " . $e->getMessage());
            $fontname = 'dejavusans'; // fallback
        }
    }
    
    // ตรวจสอบและเพิ่ม PhetsarathOT Bold font ถ้ามี
    if (file_exists($laoFontBoldPath)) {
        try {
            $fontnameBold = TCPDF_FONTS::addTTFfont($laoFontBoldPath, 'TrueTypeUnicode', '', 96);
        } catch (Exception $e) {
            error_log("Bold Font loading error: " . $e->getMessage());
            $fontnameBold = 'dejavusansb'; // fallback
        }
    }
    
    // เพิ่ม NotoColorEmoji font สำหรับ emoji (ถ้ามี)
    $emojiFontPath = BASE_PATH . '/public/fonts/NotoColorEmoji.ttf';
    if (file_exists($emojiFontPath)) {
        try {
            $emojiFontname = TCPDF_FONTS::addTTFfont($emojiFontPath, 'TrueTypeUnicode', '', 96);
        } catch (Exception $e) {
            error_log("Emoji Font loading error: " . $e->getMessage());
            $emojiFontname = 'dejavusans'; // fallback
        }
    }

    // ฟังก์ชันสร้างบัตรนักศึกษา (แบบ TCPDF)
    function generateStudentCard($student_data) {
        global $fontname, $fontnameBold;
        
        // สร้าง PDF พร้อมกำหนดขนาดที่แน่นอน
        $pdf = new TCPDF('L', 'mm', array(85.6, 54), true, 'UTF-8');
        $pdf->SetCreator('ລະບົບອອກບັດນັກສຶກສາ');
        $pdf->SetAuthor('ວິທະຍາໄລຄູສົງ ອົງຕື້');
        $pdf->SetTitle('ບັດນັກສຶກສາ');

        // ลบส่วนหัวและส่วนท้าย
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        // พื้นหลังแบบไล่สี (น้ำเงิน-ม่วง)
        $pdf->Rect(0, 0, 85.6, 14, 'F', array(), array(30, 58, 138)); // สีน้ำเงิน
        $pdf->Rect(85.6, 0, -85.6, 14, 'F', array(), array(67, 56, 202)); // สีม่วง
        
        // พื้นหลังสีขาวสำหรับส่วนเนื้อหา
        $pdf->Rect(0, 14, 85.6, 40, 'F', array(), array(255, 255, 255));

        // ขอบบัตร
        $pdf->SetLineWidth(0.5);
        $pdf->RoundedRect(2, 2, 81.6, 50, 3.5, '1111', 'D', array('color' => array(255, 255, 255)));

        // โลโก้สถาบัน (ถ้ามี)
        $logoPath = BASE_PATH . '/public/assets/img/college-logo.png';
        if (file_exists($logoPath)) {
            $pdf->Image($logoPath, 72.6, 3, 10, 10, '', '', '', false, 300);
        }

        // หัวข้อบัตร
        $pdf->SetFont($fontnameBold, '', 12);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(10, 4);
        $pdf->Cell(60, 6, 'ວິທະຍາໄລຄູສົງ ອົງຕື້', 0, 1, 'C');
        
        $pdf->SetFont($fontname, '', 8);
        $pdf->SetXY(10, 10);
        $pdf->Cell(60, 4, 'ບັດນັກສຶກສາ Student card', 0, 1, 'C');

        // กรอบรูปภาพ (เพิ่มกรอบสีส้มเหลือง)
        $photoPath = '';
        if (!empty($student_data['photo'])) {
            $photoPath = BASE_PATH . '/public/uploads/photos/' . $student_data['photo'];
        }

        // ตั้งค่าสีกรอบเป็นสีส้มเหลือง
        $pdf->SetDrawColor(255, 165, 0); // สีส้ม (Orange)
        $pdf->SetLineWidth(0.3); // ความหนาของเส้นกรอบ (ลดลงจาก 0.8)

        if (!empty($student_data['photo']) && file_exists($photoPath)) {
            // พื้นหลังสีขาวสำหรับกรอบ
            $pdf->SetFillColor(255, 255, 255);
            $pdf->RoundedRect(5, 16, 22, 26, 2, '1111', 'DF'); // 'DF' คือทั้งสีพื้นและกรอบ
            
            // วางรูปภาพ
            $pdf->Image(
            $photoPath, 
            6,    // x
            17,   // y
            20,   // ความกว้าง
            24,   // ความสูง
            '',   // รูปแบบ
            '',   // ลิงก์
            'T',  // การจัดตำแหน่ง
            false, // ปรับขนาด
            300,   // ความละเอียด
            '',    // การจัดตำแหน่งหน้า
            false, // เป็นมาส์กหรือไม่
            false, // มาส์กรูปภาพ
            0,     // เส้นขอบ
            false  // พอดีกรอบ
            );
        } else {
            // กรอบว่างสำหรับรูปภาพด้วยสีส้มเหลือง
            $pdf->SetFillColor(240, 240, 240);
            $pdf->RoundedRect(5, 16, 22, 26, 2, '1111', 'DF');
            $pdf->SetFont($fontname, '', 8);
            $pdf->SetTextColor(128, 128, 128);
            $pdf->SetXY(5, 28);
            $pdf->Cell(22, 4, 'ບໍ່ມີຮູບ', 0, 0, 'C');
        }

        // ข้อมูลนักศึกษา
        $pdf->SetTextColor(50, 50, 50);
        $x = 30; // ตำแหน่ง x เริ่มต้น
        $y = 18; // ตำแหน่ง y เริ่มต้น
        $lh = 4; // ความสูงบรรทัด

        // สร้าง student_id ถ้าไม่มี
        $displayStudentId = $student_data['student_id'] ?? ('STU' . str_pad($student_data['id'], 6, '0', STR_PAD_LEFT));

        // ชื่อ-นามสกุล
        $pdf->SetFont($fontnameBold, '', 10);
        $pdf->SetXY($x, $y);
        $fullName = $student_data['first_name'] . ' ' . $student_data['last_name'];
        
        // ตรวจสอบความยาวของชื่อ
        $nameWidth = $pdf->GetStringWidth($fullName);
        if ($nameWidth > 50) {
            $pdf->SetFont($fontnameBold, '', 8);
        }
        $pdf->Cell(50, $lh, $fullName, 0, 1);

        // ข้อมูลรายละเอียด (ลดขนาดลง)
        $y += $lh;
        $pdf->SetFont($fontnameBold, '', 7); // ลดขนาดตัวอักษรลง
        addStudentInfo($pdf, $fontnameBold, $x, $y, 'ສາຂາ:', $student_data['major_name'] ?? 'ບໍ່ລະບຸ', '📚');

        $y += $lh - 0.5; // ลดระยะห่างระหว่างบรรทัด
        addStudentInfo($pdf, $fontnameBold, $x, $y, 'ຊັ້ນປີ:', $student_data['academic_year_name'] ?? 'ບໍ່ລະບຸ', '🎓');

        $y += $lh - 0.5; // ลดระยะห่างระหว่างบรรทัด
        addStudentInfo($pdf, $fontnameBold, $x, $y, 'ລະຫັດ:', $displayStudentId, '🆔');

        
        // QR Code
        $qrData = json_encode([
            'id' => $student_data['id'],
            'student_id' => $displayStudentId,
            'name' => $fullName,
            'url' => BASE_URL . 'index.php?page=student-detail&id=' . $student_data['id']
        ]);

        $style = array(
            'border' => false,
            'padding' => 1,
            'fgcolor' => array(0, 0, 128),
            'bgcolor' => array(255, 255, 255),
            'module_width' => 1,
            'module_height' => 1
        );

        // วาง QR code
        $pdf->write2DBarcode($qrData, 'QRCODE,L', 64, 32, 18, 18, $style);

        // วันที่ออกบัตร
        $pdf->SetFont($fontname, '', 7);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->SetXY(5, 47);
        $pdf->Cell(75.6, 4, 'ອອກໃຫ້ ວັນທີ: ' . date('d/m/Y'), 0, 0, 'C');

        return $pdf;
    }

    // ฟังก์ชันช่วยเหลือสำหรับแสดงข้อมูลนักศึกษา (รองรับ emoji)
    function addStudentInfo($pdf, $fontname, $x, $y, $label, $value, $icon) {
        global $fontnameBold, $emojiFontname;
        
        // วิธีที่ 1: ใช้ Unicode symbols แทน emoji (แนะนำ)
        $iconMapping = [
            '📚' => '♦',
            '🎓' => '◊', 
            '🆔' => '#',
            '👤' => '●'
        ];
        
        // แปลง emoji เป็น Unicode symbol ถ้ามี
        $displayIcon = $iconMapping[$icon] ?? $icon;
        
        // วิธีที่ 2: ใช้ emoji font (ถ้ามี NotoColorEmoji)
        if (isset($emojiFontname) && $emojiFontname !== 'dejavusans') {
            $pdf->SetFont($emojiFontname, '', 8);
            $pdf->SetXY($x, $y);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->Cell(4, 4, $icon, 0, 0); // ใช้ emoji ตัวจริง
        } else {
            // Fallback: ใช้ Unicode symbol
            $pdf->SetFont($fontname, '', 8);
            $pdf->SetXY($x, $y);
            $pdf->SetTextColor(100, 100, 100);
            $pdf->Cell(4, 4, $displayIcon, 0, 0);
        }
        
        $pdf->SetTextColor(50, 50, 50);
        $pdf->SetFont($fontname, '', 8);
        $pdf->Cell(12, 4, $label, 0, 0); // ป้ายกำกับ
        
        $pdf->SetFont($fontnameBold, '', 8);
        $pdf->SetTextColor(0, 0, 0);
        $valueWidth = $pdf->GetStringWidth($value);
        if ($valueWidth > 35) {
            $pdf->SetFont($fontnameBold, '', 7);
        }
        $pdf->Cell(35, 4, $value, 0, 1);
    }

    // ฟังก์ชันสร้าง HTML สำหรับด้านหลัง (บัตรนักศึกษามาตรฐาน)
    function generateBackCardHTML($student_data) {
        global $fontname, $fontnameBold;
        
        // สร้าง PDF สำหรับด้านหลัง
        $pdf = new TCPDF('L', 'mm', array(85.6, 54), true, 'UTF-8');
        $pdf->SetCreator('ລະບົບອອກບັດນັກສຶກສາ');
        $pdf->SetAuthor('ວິທະຍາໄລຄູສົງ ອົງຕື້');
        $pdf->SetTitle('ບັດນັກສຶກສາ - ດ້ານຫລັງ');

        // ลบส่วนหัวและส่วนท้าย
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(0, 0, 0);
        $pdf->SetAutoPageBreak(false);
        $pdf->AddPage();

        // พื้นหลังด้านหลัง (สีเทา-ดำ)
        $pdf->Rect(0, 0, 85.6, 54, 'F', array(), array(55, 65, 81));
        
        // ขอบบัตร
        $pdf->SetLineWidth(0.5);
        $pdf->RoundedRect(2, 2, 81.6, 50, 3.5, '1111', 'D', array('color' => array(255, 255, 255)));

        // หัวข้อด้านหลัง
        $pdf->SetFont($fontname, 'B', 10);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(5, 6);
        $pdf->Cell(75.6, 6, 'ວິທະຍາໄລຄູສົງ ອົງຕື້', 0, 1, 'C');

        $pdf->SetFont($fontname, '', 7);
        $pdf->SetXY(5, 11);
        $pdf->Cell(75.6, 4, 'ຂໍ້ມູນນັກສຶກສາ ', 0, 1, 'C');

        // เส้นแบ่ง
        $pdf->SetLineWidth(0.2);
        $pdf->Line(10, 18, 75.6, 18);

        // ข้อมูลรายละเอียด
        $pdf->SetTextColor(255, 255, 255);
        $x1 = 8; $x2 = 45; // คอลัมน์ซ้าย-ขวา
        $y = 22;
        $lh = 5;

        // สร้าง student_id ถ้าไม่มี
        $displayStudentId = $student_data['student_id'] ?? ('STU' . str_pad($student_data['id'], 6, '0', STR_PAD_LEFT));

        // จัดการวันที่เกิด
        $dobFormatted = 'ບໍ່ລະບຸ';
        if (!empty($student_data['dob'])) {
            try {
                $date = new DateTime($student_data['dob']);
                $dobFormatted = $date->format('d/m/Y');
            } catch (Exception $e) {
                $dobFormatted = 'ບໍ່ລະບຸ';
            }
        }

        // แปลงเพศ
        $genderText = '';
        switch ($student_data['gender'] ?? '') {
            case 'male': $genderText = 'ຊາຍ'; break;
            case 'female': $genderText = 'ຍິງ'; break;
            case 'ຊາຍ': $genderText = 'ຊາຍ'; break;
            case 'ຍິງ': $genderText = 'ຍິງ'; break;
            case 'ພຣະ': $genderText = 'ພຣະ'; break;
            case 'ສາມະເນນ': $genderText = 'ສາມະເນນ'; break;
            default: $genderText = 'ບໍ່ລະບຸ'; break;
        }

        // คอลัมน์ซ้าย
        addBackInfo($pdf, $fontname, $x1, $y, 'ລະຫັດນັກສຶກສາ:', $displayStudentId);
        $y += $lh;
        addBackInfo($pdf, $fontname, $x1, $y, 'ເພດ:', $genderText);
        $y += $lh;
        addBackInfo($pdf, $fontname, $x1, $y, 'ວັນເກີດ:', $dobFormatted);

        // คอลัมน์ขวา
        $y = 22;
        addBackInfo($pdf, $fontname, $x2, $y, 'ເບີໂທ:', $student_data['phone'] ?? 'ບໍ່ລະບຸ');
        $y += $lh;
        addBackInfo($pdf, $fontname, $x2, $y, 'ທີ່ພັກ:', $student_data['accommodation_type'] ?? 'ບໍ່ລະບຸ');
        $y += $lh;
        addBackInfo($pdf, $fontname, $x2, $y, 'ອີເມວ:', $student_data['email'] ?? 'ບໍ່ລະບຸ');

        // เส้นแบ่งล่าง
        $pdf->SetLineWidth(0.2);
        $pdf->Line(10, 40, 75.6, 40);

        // ส่วนลายเซ็น
        $pdf->SetFont($fontname, '', 6);
        $pdf->SetXY(8, 42);
        $pdf->Cell(30, 4, 'ລາຍເຊັນເຈົ້າຂອງບັດ', 0, 0, 'C');
        
        $pdf->SetXY(45, 42);
        $pdf->Cell(30, 4, 'ລາຍເຊັນຜູ້ອຳນວຍການ', 0, 0, 'C');

        // เส้นลายเซ็น
        $pdf->SetLineWidth(0.1);
        $pdf->Line(8, 48, 35, 48);   // ลายเซ็นซ้าย
        $pdf->Line(45, 48, 72, 48);  // ลายเซ็นขวา

        // ข้อมูลติดต่อและคำเตือน (รองรับ emoji)
        $pdf->SetTextColor(200, 200, 200);
        renderTextWithEmoji($pdf, $fontname, 5, 50, 75.6, 2, '📞 +856-20-9121-3388 | 🌐 www.ongtue-ttc.edu.la');
        
        $pdf->SetFont($fontname, '', 5);
        $pdf->SetXY(5, 52);
        $pdf->Cell(75.6, 2, 'ບັດນີ້ເປັນຂອງວິທະຍາໄລຄູສົງ ອົງຕື້ ຫ້າມໃຫ້ຜູ້ອື່ນໃຊ້ ໂດຍຂັດຕໍ່ລະບຽບກົດໝາຍ', 0, 1, 'C');

        return $pdf;
    }

    // ฟังก์ชันสำหรับแสดงข้อความที่มี emoji
    function renderTextWithEmoji($pdf, $fontname, $x, $y, $width, $height, $text, $align = 'C') {
        global $emojiFontname;
        
        // แยก text และ emoji
        $parts = preg_split('/([📞🌐📚🎓🆔👤])/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
        
        if (isset($emojiFontname) && $emojiFontname !== 'dejavusans') {
            // ถ้ามี emoji font ให้ใช้
            $currentX = $x;
            foreach ($parts as $part) {
                if (preg_match('/[📞🌐📚🎓🆔👤]/', $part)) {
                    // ใช้ emoji font
                    $pdf->SetFont($emojiFontname, '', 5);
                    $partWidth = $pdf->GetStringWidth($part);
                    $pdf->SetXY($currentX, $y);
                    $pdf->Cell($partWidth, $height, $part, 0, 0, 'L');
                    $currentX += $partWidth;
                } else {
                    // ใช้ font ปกติ
                    $pdf->SetFont($fontname, '', 5);
                    $partWidth = $pdf->GetStringWidth($part);
                    $pdf->SetXY($currentX, $y);
                    $pdf->Cell($partWidth, $height, $part, 0, 0, 'L');
                    $currentX += $partWidth;
                }
            }
        } else {
            // Fallback: แทนที่ emoji ด้วยข้อความ
            $replacements = [
                '📞' => 'Tel:',
                '🌐' => 'Web:'
            ];
            $displayText = str_replace(array_keys($replacements), array_values($replacements), $text);
            $pdf->SetFont($fontname, '', 5);
            $pdf->SetXY($x, $y);
            $pdf->Cell($width, $height, $displayText, 0, 0, $align);
        }
    }

    // ฟังก์ชันช่วยเหลือสำหรับข้อมูลด้านหลัง
    function addBackInfo($pdf, $fontname, $x, $y, $label, $value) {
        global $fontnameBold;
        
        $pdf->SetFont($fontname, '', 6);
        $pdf->SetXY($x, $y);
        $pdf->Cell(15, 4, $label, 0, 0);
        
        $pdf->SetFont($fontnameBold, '', 6);
        $pdf->Cell(20, 4, $value, 0, 1);
    }

    // ตรวจสอบว่าต้องการด้านไหน
    $side = $_GET['side'] ?? 'front'; // front, back, both
    $output = $_GET['output'] ?? 'I'; // D=Download, I=Inline

    if ($side === 'both') {
        // สร้างทั้งสองด้าน - ใช้ฟังก์ชันที่มีอยู่แล้ว
        $pdf = generateStudentCard($student_data);
        
        // เพิ่มหน้าใหม่สำหรับด้านหลัง โดยใช้ฟังก์ชัน generateBackCardHTML
        $backPdf = generateBackCardHTML($student_data);
        
        // Copy หน้าด้านหลังไปยัง PDF หลัก
        $pdf->AddPage();
        
        // วาดเนื้อหาด้านหลัง (คัดลอกจาก generateBackCardHTML)
        // พื้นหลังด้านหลัง (สีเทา-ดำ)
        $pdf->Rect(0, 0, 85.6, 54, 'F', array(), array(55, 65, 81));
        
        // ขอบบัตร
        $pdf->SetLineWidth(0.5);
        $pdf->RoundedRect(2, 2, 81.6, 50, 3.5, '1111', 'D', array('color' => array(255, 255, 255)));

        // หัวข้อด้านหลัง
        $pdf->SetFont($fontnameBold, '', 10);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetXY(5, 6);
        $pdf->Cell(75.6, 6, 'ວິທະຍາໄລຄູສົງ ອົງຕື້', 0, 1, 'C');

        $pdf->SetFont($fontname, '', 7);
        $pdf->SetXY(5, 11);
        $pdf->Cell(75.6, 4, 'ຂໍ້ມູນນັກສຶກສາ ', 0, 1, 'C');

        // เส้นแบ่ง
        $pdf->SetLineWidth(0.2);
        $pdf->Line(10, 18, 75.6, 18);

        // ข้อมูลรายละเอียด
        $pdf->SetTextColor(255, 255, 255);
        $x1 = 8; $x2 = 45; // คอลัมน์ซ้าย-ขวา
        $y = 22;
        $lh = 5;

        // สร้าง student_id ถ้าไม่มี
        $displayStudentId = $student_data['student_id'] ?? ('STU' . str_pad($student_data['id'], 6, '0', STR_PAD_LEFT));

        // จัดการวันที่เกิด
        $dobFormatted = 'ບໍ່ລະບຸ';
        if (!empty($student_data['dob'])) {
            try {
                $date = new DateTime($student_data['dob']);
                $dobFormatted = $date->format('d/m/Y');
            } catch (Exception $e) {
                $dobFormatted = 'ບໍ່ລະບຸ';
            }
        }

        // แปลงเพศ
        $genderText = '';
        switch ($student_data['gender'] ?? '') {
            case 'male': $genderText = 'ຊາຍ'; break;
            case 'female': $genderText = 'ຍິງ'; break;
            case 'ຊາຍ': $genderText = 'ຊາຍ'; break;
            case 'ຍິງ': $genderText = 'ຍິງ'; break;
            case 'ພຣະ': $genderText = 'ພຣະ'; break;
            case 'ສາມະເນນ': $genderText = 'ສາມະເນນ'; break;
            default: $genderText = 'ບໍ່ລະບຸ'; break;
        }

        // คอลัมน์ซ้าย
        addBackInfo($pdf, $fontname, $x1, $y, 'ລະຫັດນັກສຶກສາ:', $displayStudentId);
        $y += $lh;
        addBackInfo($pdf, $fontname, $x1, $y, 'ເພດ:', $genderText);
        $y += $lh;
        addBackInfo($pdf, $fontname, $x1, $y, 'ວັນເກີດ:', $dobFormatted);

        // คอลัมน์ขวา
        $y = 22;
        addBackInfo($pdf, $fontname, $x2, $y, 'ເບີໂທ:', $student_data['phone'] ?? 'ບໍ່ລະບຸ');
        $y += $lh;
        addBackInfo($pdf, $fontname, $x2, $y, 'ທີ່ພັກ:', $student_data['accommodation_type'] ?? 'ບໍ່ລະບຸ');
        $y += $lh;
        addBackInfo($pdf, $fontname, $x2, $y, 'ອີເມວ:', $student_data['email'] ?? 'ບໍ່ລະບຸ');

        // เส้นแบ่งล่าง
        $pdf->SetLineWidth(0.2);
        $pdf->Line(10, 40, 75.6, 40);

        // ส่วนลายเซ็น
        $pdf->SetFont($fontname, '', 6);
        $pdf->SetXY(8, 42);
        $pdf->Cell(30, 4, 'ລາຍເຊັນເຈົ້າຂອງບັດ', 0, 0, 'C');
        
        $pdf->SetXY(45, 42);
        $pdf->Cell(30, 4, 'ລາຍເຊັນຜູ້ອຳນວຍການ', 0, 0, 'C');

        // เส้นลายเซ็น
        $pdf->SetLineWidth(0.1);
        $pdf->Line(8, 48, 35, 48);   // ลายเซ็นซ้าย
        $pdf->Line(45, 48, 72, 48);  // ลายเซ็นขวา

        // ข้อมูลติดต่อและคำเตือน (รองรับ emoji)
        $pdf->SetTextColor(200, 200, 200);
        renderTextWithEmoji($pdf, $fontname, 5, 50, 75.6, 2, '📞 +856-20-9121-3388 | 🌐 www.ongtue-ttc.edu.la');
        
        $pdf->SetFont($fontname, '', 5);
        $pdf->SetXY(5, 52);
        $pdf->Cell(75.6, 2, 'ບັດນີ້ເປັນຂອງວິທະຍາໄລຄູສົງ ອົງຕື້ ຫ້າມໃຫ້ຜູ້ອື່ນໃຊ້ ໂດຍຂັດຕໍ່ລະບຽບກົດໝາຍ', 0, 1, 'C');
        
    } elseif ($side === 'back') {
        // สร้างเฉพาะด้านหลัง
        $pdf = generateBackCardHTML($student_data);
        
    } else {
        // สร้างเฉพาะด้านหน้า (default)
        $pdf = generateStudentCard($student_data);
    }

    // กำหนดชื่อไฟล์
    $studentName = preg_replace('/[^a-zA-Z0-9_]/', '', $student_data['first_name'] . '_' . $student_data['last_name']);
    $displayStudentId = $student_data['student_id'] ?? $student_data['id'];
    
    $filename = 'student_card_' . 
                $displayStudentId . '_' . 
                $studentName . '_' .
                $side . '_' . 
                date('Ymd_His') . '.pdf';

    // ส่งออก PDF
    if ($output === 'D') {
        // ดาวน์โหลด
        $pdf->Output($filename, 'D');
    } elseif ($output === 'F') {
        // บันทึกไฟล์
        $downloadDir = BASE_PATH . '/public/downloads/';
        if (!is_dir($downloadDir)) {
            mkdir($downloadDir, 0755, true);
        }
        $pdf->Output($downloadDir . $filename, 'F');
        
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'success' => true,
            'message' => 'สร้าง PDF สำเร็จ',
            'filename' => $filename,
            'path' => $downloadDir . $filename
        ]);
    } else {
        // แสดงในเบราว์เซอร์ (default)
        $pdf->Output($filename, 'I');
    }

} catch (Exception $e) {
    error_log("PDF Generation Error: " . $e->getMessage());
    
    if (isset($_GET['output']) && $_GET['output'] === 'F') {
        // ส่งคืน JSON error
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ]);
    } else {
        // แสดง error page
        echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded max-w-md mx-auto mt-10">';
        echo '<h3 class="font-bold text-lg mb-2">ເກີດຂໍ້ຜິດພາດ!</h3>';
        echo '<p>ບໍ່ສາມາດສ້າງ PDF ໄດ້: ' . htmlspecialchars($e->getMessage()) . '</p>';
        echo '<div class="mt-4">';
        echo '<a href="' . BASE_URL . 'index.php?page=students" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">ກັບຄືນຫາລາຍຊື່</a>';
        echo '</div>';
        echo '</div>';
    }
}
?>