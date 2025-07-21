<?php
// filepath: c:\xampp\htdocs\register-learning\src\classes\QrCodeGenerator.php

class QrCodeGenerator {
    
    /**
     * ສ້າງ QR Code ສຳລັບນັກສຶກສາ (ໃຊ້ API)
     */
    public static function generateStudentQrCode(array $studentData): array {
        try {
            // สร้าง URL ที่ชี้ไปที่หน้ารายละเอียดนักศึกษา
            $baseUrl = self::getBaseUrl();
            $studentId = $studentData['id'];
            
            // สร้าง URL แบบสมบูรณ์
            $url = "{$baseUrl}index.php?page=student-detail&id={$studentId}";
            
            // สร้าง QR Code ด้วย API
            $qrCodeUrl = self::generateQRCodeUrl($url, 300);
            
            // ตรวจสอบว่า QR Code สร้างได้หรือไม่
            if (self::isUrlAccessible($qrCodeUrl)) {
                return [
                    'success' => true,
                    'data_url' => $qrCodeUrl,
                    'mime_type' => 'image/png',
                    'url' => $url,
                    'student_id' => $studentData['student_id'] ?? 'N/A',
                    'qr_code_url' => $qrCodeUrl
                ];
            } else {
                throw new Exception('QR Code API not accessible');
            }
            
        } catch (Exception $e) {
            error_log("QR Code generation error: " . $e->getMessage());
            
            // Fallback: สร้าง QR Code ด้วย Google Charts API
            try {
                $fallbackUrl = self::generateQRCodeUrlGoogle($url, 300);
                return [
                    'success' => true,
                    'data_url' => $fallbackUrl,
                    'mime_type' => 'image/png',
                    'url' => $url,
                    'student_id' => $studentData['student_id'] ?? 'N/A',
                    'qr_code_url' => $fallbackUrl,
                    'fallback' => true
                ];
            } catch (Exception $fallbackError) {
                return [
                    'success' => false,
                    'error' => $e->getMessage(),
                    'fallback_error' => $fallbackError->getMessage()
                ];
            }
        }
    }
    
    /**
     * ສ້າງ QR Code URL ດ້ວຍ QR Server API
     */
    private static function generateQRCodeUrl(string $data, int $size = 300): string {
        $baseUrl = 'https://api.qrserver.com/v1/create-qr-code/';
        $params = [
            'size' => $size . 'x' . $size,
            'data' => $data,
            'format' => 'png',
            'margin' => 10,
            'color' => '000000',
            'bgcolor' => 'FFFFFF',
            'ecc' => 'H' // High error correction
        ];
        
        return $baseUrl . '?' . http_build_query($params);
    }
    
    /**
     * ສ້າງ QR Code URL ດ້ວຍ Google Charts API (Fallback)
     */
    private static function generateQRCodeUrlGoogle(string $data, int $size = 300): string {
        $baseUrl = 'https://chart.googleapis.com/chart';
        $params = [
            'chs' => $size . 'x' . $size,
            'cht' => 'qr',
            'chl' => $data,
            'choe' => 'UTF-8',
            'chld' => 'H|2' // High error correction, margin 2
        ];
        
        return $baseUrl . '?' . http_build_query($params);
    }
    
    /**
     * ຕວດສອບວ່າ URL ເຂົ້າເຖິງໄດ້ຫຼືບໍ່
     */
    private static function isUrlAccessible(string $url): bool {
        $headers = @get_headers($url);
        return $headers && strpos($headers[0], '200') !== false;
    }
    
    /**
     * ດຶງ Base URL ຂອງເວັບໄຊ
     */
    private static function getBaseUrl(): string {
        // ตรวจสอบว่าอยู่ใน CLI หรือ Web environment
        if (php_sapi_name() === 'cli') {
            return 'http://localhost:8080/';
        }
        
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8080';
        $path = dirname($_SERVER['PHP_SELF'] ?? '/public/index.php');
        
        // ลบ /public ออกจาก path ถ้ามี
        $path = str_replace('/public', '', $path);
        
        return $protocol . $host . $path . '/';
    }
    
    /**
     * ສ້າງ QR Code ສຳລັບຂໍ້ມູນທົ່ວໄປ
     */
    public static function generateQRCode(string $data, int $size = 300): array {
        try {
            $qrCodeUrl = self::generateQRCodeUrl($data, $size);
            
            return [
                'success' => true,
                'data_url' => $qrCodeUrl,
                'mime_type' => 'image/png',
                'qr_code_url' => $qrCodeUrl
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * ບັນທຶກ QR Code ລົງໄຟລ໌
     */
    public static function saveQRCodeToFile(string $data, string $filename, int $size = 300): array {
        try {
            $qrCodeUrl = self::generateQRCodeUrl($data, $size);
            
            // สร้างโฟลเดอร์ถ้ายังไม่มี
            $uploadDir = BASE_PATH . '/public/uploads/qrcodes/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }
            
            // ดาวน์โหลดและบันทึกไฟล์
            $imageData = file_get_contents($qrCodeUrl);
            if ($imageData === false) {
                throw new Exception('Cannot download QR Code image');
            }
            
            $filePath = $uploadDir . $filename;
            if (file_put_contents($filePath, $imageData) === false) {
                throw new Exception('Cannot save QR Code file');
            }
            
            // สร้าง URL สำหรับเข้าถึงไฟล์
            $fileUrl = BASE_URL . 'uploads/qrcodes/' . $filename;
            
            return [
                'success' => true,
                'file_path' => $filePath,
                'file_url' => $fileUrl,
                'filename' => $filename
            ];
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * ສ້າງ QR Code ສຳລັບ vCard (ຂໍ້ມູນຜູ້ຕິດຕໍ່)
     */
    public static function generateVCardQRCode(array $contactData): array {
        try {
            $vcard = "BEGIN:VCARD\n";
            $vcard .= "VERSION:3.0\n";
            $vcard .= "FN:" . ($contactData['full_name'] ?? '') . "\n";
            $vcard .= "N:" . ($contactData['last_name'] ?? '') . ";" . ($contactData['first_name'] ?? '') . "\n";
            $vcard .= "ORG:" . ($contactData['organization'] ?? 'ວິທະຍາໄລຄູ') . "\n";
            $vcard .= "TEL:" . ($contactData['phone'] ?? '') . "\n";
            $vcard .= "EMAIL:" . ($contactData['email'] ?? '') . "\n";
            $vcard .= "URL:" . ($contactData['url'] ?? '') . "\n";
            $vcard .= "END:VCARD";
            
            return self::generateQRCode($vcard);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
    
    /**
     * ສ້າງ QR Code ສຳລັບ WiFi
     */
    public static function generateWiFiQRCode(string $ssid, string $password, string $security = 'WPA'): array {
        try {
            $wifiString = "WIFI:T:{$security};S:{$ssid};P:{$password};;";
            return self::generateQRCode($wifiString);
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>