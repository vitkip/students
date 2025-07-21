<?php
/**
 * QR Code Template for register-learning project
 * Template file to be included in the main routing system
 */

// Process request parameters first
$url = $_GET['url'] ?? '';
$size = (int)($_GET['size'] ?? 300);
$download = $_GET['download'] ?? '';

// Validate size
if ($size < 100 || $size > 1000) {
    $size = 300;
}

// Check if autoloader is available and load QR code classes
$autoloaderPaths = [
    BASE_PATH . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    __DIR__ . '/../../vendor/autoload.php'
];

$autoloaderLoaded = false;
foreach ($autoloaderPaths as $autoloaderPath) {
    if (file_exists($autoloaderPath)) {
        require_once $autoloaderPath;
        $autoloaderLoaded = true;
        break;
    }
}

// Check if we have the required classes available after autoloader
if (!$autoloaderLoaded || !class_exists('Endroid\QrCode\QrCode')) {
    echo '<div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4">';
    echo '<strong>Error:</strong> QR Code library not available. Please ensure Composer dependencies are installed.';
    if (!$autoloaderLoaded) {
        echo '<br><small>Autoloader paths checked:</small>';
        foreach ($autoloaderPaths as $path) {
            echo '<br>- ' . htmlspecialchars($path) . ' (exists: ' . (file_exists($path) ? 'yes' : 'no') . ')';
        }
    }
    echo '</div>';
    return;
}

use Endroid\QrCode\Color\Color;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

/**
 * Alternative QR Code generation using bacon/bacon-qr-code directly
 * This is a fallback method in case endroid/qr-code has issues
 */
function generateQRCodeBacon($url, $size = 300) {
    try {
        // Check if bacon qr-code is available
        if (!class_exists('BaconQrCode\Renderer\ImageRenderer')) {
            throw new Exception('Bacon QR Code library not available');
        }
        
        $urlHash = md5($url);
        $filename = "qrcode_bacon_{$urlHash}_{$size}.png";
        $filePath = BASE_PATH . "/public/qrcodes/{$filename}";
        $webPath = "qrcodes/{$filename}";
        
        // Check if cached
        if (file_exists($filePath)) {
            return [
                'success' => true,
                'file_path' => $filePath,
                'web_path' => $webPath,
                'url' => $url,
                'cached' => true,
                'size' => $size
            ];
        }
        
        $renderer = new \BaconQrCode\Renderer\ImageRenderer(
            new \BaconQrCode\Renderer\RendererStyle\RendererStyle($size),
            new \BaconQrCode\Renderer\Image\SvgImageBackEnd()
        );
        
        $writer = new \BaconQrCode\Writer($renderer);
        $qrCodeString = $writer->writeString($url);
        
        // Convert SVG to PNG using GD
        $svg = new \DOMDocument();
        $svg->loadXML($qrCodeString);
        
        // Simple fallback: save as SVG and let browser handle it
        $svgFile = str_replace('.png', '.svg', $filePath);
        file_put_contents($svgFile, $qrCodeString);
        
        return [
            'success' => true,
            'file_path' => $svgFile,
            'web_path' => str_replace('.png', '.svg', $webPath),
            'url' => $url,
            'cached' => false,
            'size' => $size
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Bacon QR fallback failed: ' . $e->getMessage()
        ];
    }
}

/**
 * Generate QR Code with caching support and enhanced error handling
 */
function generateQRCodeTemplate($url, $size = 300) {
    try {
        // Increase memory limit and execution time for QR generation
        $oldMemoryLimit = ini_get('memory_limit');
        $oldTimeLimit = ini_get('max_execution_time');
        
        @ini_set('memory_limit', '256M');
        @ini_set('max_execution_time', 60);
        
        // Check GD extension
        if (!extension_loaded('gd')) {
            throw new Exception('GD extension is not loaded. Please enable GD extension in php.ini');
        }
        
        $gd_info = gd_info();
        if (!$gd_info['PNG Support']) {
            throw new Exception('PNG support is not available in GD extension');
        }

        // Validate URL
        if (empty($url)) {
            throw new Exception('URL parameter is required');
        }

        // Clean and validate URL
        $url = trim($url);
        if (!filter_var($url, FILTER_VALIDATE_URL) && !preg_match('/^https?:\/\//', $url)) {
            // If not a full URL, assume it's a relative path
            $url = 'http://' . $url;
        }

        // Create filename based on URL hash (UTF-8 safe)
        $urlHash = md5($url);
        $filename = "qrcode_{$urlHash}_{$size}.png";
        $filePath = BASE_PATH . "/public/qrcodes/{$filename}";
        
        // Ensure qrcodes directory exists
        $qrcodesDir = BASE_PATH . "/public/qrcodes";
        if (!is_dir($qrcodesDir)) {
            if (!mkdir($qrcodesDir, 0755, true)) {
                throw new Exception('Unable to create qrcodes directory: ' . $qrcodesDir);
            }
        }
        
        if (!is_writable($qrcodesDir)) {
            throw new Exception('qrcodes directory is not writable: ' . $qrcodesDir);
        }
        
        // Use relative path for web access to avoid BASE_URL issues
        $webPath = "qrcodes/{$filename}";

        // Check if QR code already exists
        if (file_exists($filePath)) {
            return [
                'success' => true,
                'file_path' => $filePath,
                'web_path' => $webPath,
                'url' => $url,
                'cached' => true,
                'size' => $size
            ];
        }

        // Test basic image creation first
        $testImage = @imagecreate(10, 10);
        if (!$testImage) {
            throw new Exception('Unable to create test image with imagecreate(). GD may not be properly configured.');
        }
        imagedestroy($testImage);

        // Create QR code with error handling
        $qrCode = new QrCode(
            data: $url,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::Low,
            size: $size,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin,
            foregroundColor: new Color(0, 0, 0),
            backgroundColor: new Color(255, 255, 255)
        );

        // Create writer with error handling
        $writer = new PngWriter();

        // Generate QR code with detailed error reporting
        try {
            $result = $writer->write($qrCode);
        } catch (Exception $e) {
            throw new Exception('QR code generation failed: ' . $e->getMessage());
        }

        // Save to file with error handling
        try {
            $result->saveToFile($filePath);
        } catch (Exception $e) {
            throw new Exception('Failed to save QR code to file: ' . $e->getMessage());
        }
        
        // Verify file was created
        if (!file_exists($filePath)) {
            throw new Exception('QR code file was not created successfully');
        }
        
        if (filesize($filePath) === 0) {
            throw new Exception('QR code file is empty (0 bytes)');
        }

        // Restore original settings
        @ini_set('memory_limit', $oldMemoryLimit);
        @ini_set('max_execution_time', $oldTimeLimit);

        return [
            'success' => true,
            'file_path' => $filePath,
            'web_path' => $webPath,
            'url' => $url,
            'cached' => false,
            'size' => $size
        ];

    } catch (Exception $e) {
        error_log("QR Code generation error: " . $e->getMessage());
        
        // Try fallback method using bacon/bacon-qr-code
        $fallbackResult = generateQRCodeBacon($url, $size);
        if ($fallbackResult['success']) {
            error_log("QR Code generated using bacon fallback method");
            return $fallbackResult;
        }
        
        return [
            'success' => false,
            'error' => 'Primary method failed: ' . $e->getMessage() . '. Fallback also failed: ' . $fallbackResult['error']
        ];
    }
}

// Generate QR code if URL is provided
$qrResult = null;
if (!empty($url)) {
    $qrResult = generateQRCodeTemplate($url, $size);
}

// Note: Download handling is now managed in index.php before headers are sent



?>

<!DOCTYPE html>
<html lang="lo">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Generator - ເຄື່ອງມືສ້າງລະຫັດ QR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>assets/css/style.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Lao:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body, html {
            font-family: 'Noto Sans Lao', sans-serif;
        }
        h1, h2, h3, h4, h5, h6, p, span, div, button, a {
            font-family: 'Noto Sans Lao', sans-serif;
        }
        @keyframes bounce {
            0%, 20%, 53%, 80%, 100% { transform: translate3d(0,0,0); }
            40%, 43% { transform: translate3d(0, -30px, 0); }
            70% { transform: translate3d(0, -15px, 0); }
            90% { transform: translate3d(0, -4px, 0); }
        }
        .animate-bounce { animation: bounce 2s infinite; }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fadeIn 0.6s ease-out; }
        
        @keyframes pulse-glow {
            0%, 100% { box-shadow: 0 0 20px rgba(139, 92, 246, 0.4); }
            50% { box-shadow: 0 0 30px rgba(139, 92, 246, 0.6); }
        }
        .qr-glow { animation: pulse-glow 2s infinite; }
    </style>
</head>
<body class="bg-gradient-to-br from-blue-50 via-purple-50 to-green-50 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-5xl mx-auto">
            
            <!-- Header Section -->
            <div class="text-center mb-8 animate-fade-in">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-purple-100 rounded-full mb-6">
                    <i class="fas fa-qrcode text-4xl text-purple-600 animate-bounce"></i>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold text-gray-800 mb-4">
                    📱 QR Code Generator
                </h1>
                <p class="text-gray-600 text-lg md:text-xl mb-4">
                    ເຄື່ອງມືສ້າງລະຫັດ QR ສຳລັບເວັບໄຊ ແລະ ລິງກ໌ຕ່າງໆ
                </p>
                <div class="inline-flex items-center px-6 py-3 bg-purple-100 rounded-full">
                    <i class="fas fa-magic text-purple-600 mr-2"></i>
                    <span class="text-purple-800 font-semibold">ຮອງຮັບ UTF-8 ແລະ ພາສາລາວ</span>
                </div>
            </div>

            <!-- QR Code Form -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 mb-8 animate-fade-in"
                 style="background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);">
                <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-edit text-blue-500 mr-3"></i>
                    ສ້າງ QR Code ໃໝ່
                </h2>
                
                <form method="GET" action="" class="space-y-6">
                    <input type="hidden" name="page" value="qrcode">
                    
                    <div class="space-y-4">
                        <div>
                            <label for="url" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-link text-blue-500 mr-2"></i>
                                URL Address / ທີ່ຢູ່ເວັບໄຊ
                            </label>
                            <input 
                                type="text" 
                                id="url" 
                                name="url" 
                                value="<?= htmlspecialchars($url) ?>"
                                placeholder="https://example.com"
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 hover:border-purple-300"
                                required
                            >
                        </div>
                        
                        <div>
                            <label for="size" class="flex items-center text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-expand-arrows-alt text-green-500 mr-2"></i>
                                Size / ຂະໜາດ (pixels)
                            </label>
                            <select 
                                id="size" 
                                name="size" 
                                class="w-full px-4 py-3 border-2 border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-purple-500 transition-all duration-200 hover:border-purple-300"
                            >
                                <option value="200" <?= $size == 200 ? 'selected' : '' ?>>200x200 - ຂະໜາດນ້ອຍ</option>
                                <option value="300" <?= $size == 300 ? 'selected' : '' ?>>300x300 - ຂະໜາດກາງ</option>
                                <option value="400" <?= $size == 400 ? 'selected' : '' ?>>400x400 - ຂະໜາດໃຫຍ່</option>
                                <option value="500" <?= $size == 500 ? 'selected' : '' ?>>500x500 - ຂະໜາດໃຫຍ່ທີ່ສຸດ</option>
                            </select>
                        </div>
                    </div>
                    
                    <button 
                        type="submit" 
                        class="w-full bg-gradient-to-r from-purple-600 to-blue-600 text-white py-4 px-6 rounded-xl hover:from-purple-700 hover:to-blue-700 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 font-semibold text-lg"
                    >
                        <i class="fas fa-magic mr-2"></i>
                        ✨ Generate QR Code / ສ້າງລະຫັດ QR
                    </button>
                </form>
            </div>

            <!-- QR Code Result -->
            <?php if ($qrResult): ?>
                <div class="bg-white rounded-2xl shadow-2xl p-8 mb-8 animate-fade-in"
                     style="background: linear-gradient(135deg, #ffffff 0%, #f0f9ff 100%);">
                    <?php if ($qrResult['success']): ?>
                        <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                            <i class="fas fa-check-circle text-green-500 mr-3"></i>
                            ລະຫັດ QR ທີ່ສ້າງສຳເລັດແລ້ວ
                            <?php if ($qrResult['cached']): ?>
                                <span class="text-sm bg-green-100 text-green-600 px-3 py-1 rounded-full ml-2">
                                    <i class="fas fa-clock mr-1"></i>
                                    ໃຊ້ໄຟລ໌ທີ່ມີຢູ່ແລ້ວ
                                </span>
                            <?php endif; ?>
                        </h3>
                        
                        <div class="text-center">
                            <!-- QR Code Image -->
                            <div class="inline-block bg-gradient-to-br from-gray-50 to-gray-100 p-6 rounded-2xl shadow-inner mb-6">
                                <div class="bg-white p-4 rounded-xl qr-glow">
                                    
                                    <img 
                                        src="<?= htmlspecialchars($qrResult['web_path']) ?>" 
                                        alt="QR Code"
                                        class="max-w-full h-auto rounded-lg shadow-lg"
                                        style="width: <?= $qrResult['size'] ?>px; height: <?= $qrResult['size'] ?>px;"
                                        onerror="this.style.border='2px solid red'; this.alt='QR Code failed to load: <?= htmlspecialchars($qrResult['web_path']) ?>'; console.log('QR Code image failed to load:', '<?= htmlspecialchars($qrResult['web_path']) ?>');"
                                    >
                                </div>
                            </div>
                            
                            <!-- Fallback: Direct link if image doesn't load -->
                            <div class="mb-6">
                                <a href="<?= htmlspecialchars($qrResult['web_path']) ?>" target="_blank" 
                                   class="inline-flex items-center px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors duration-200">
                                    <i class="fas fa-external-link-alt mr-2"></i>
                                    🔗 Direct link to QR code image
                                </a>
                            </div>
                            
                            <!-- QR Code Information -->
                            <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 mb-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                                    <div class="flex items-start">
                                        <i class="fas fa-link text-blue-500 mr-3 mt-1"></i>
                                        <div>
                                            <strong class="text-gray-700">Target URL / ທີ່ຢູ່ເປົ້າໝາຍ:</strong>
                                            <p class="text-gray-600 break-all text-sm mt-1">
                                                <a href="<?= htmlspecialchars($qrResult['url']) ?>" target="_blank" 
                                                   class="text-blue-600 hover:underline">
                                                    <?= htmlspecialchars($qrResult['url']) ?>
                                                </a>
                                            </p>
                                        </div>
                                    </div>
                                    <div class="flex items-center">
                                        <i class="fas fa-expand-arrows-alt text-green-500 mr-3"></i>
                                        <div>
                                            <strong class="text-gray-700">Size / ຂະໜາດ:</strong>
                                            <p class="text-gray-600"><?= $qrResult['size'] ?>x<?= $qrResult['size'] ?> pixels</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                      
                            
                            <!-- Action Buttons -->
                            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                                <a 
                                    href="?page=qrcode&url=<?= urlencode($url) ?>&size=<?= $size ?>&download=1" 
                                    class="bg-gradient-to-r from-green-600 to-emerald-600 text-white px-8 py-4 rounded-xl hover:from-green-700 hover:to-emerald-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 inline-flex items-center justify-center font-semibold"
                                >
                                    <i class="fas fa-download mr-2"></i>
                                    💾 ດາວໂຫລດ
                                </a>
                                
                                <a 
                                    href="qrcode.php?url=<?= urlencode($url) ?>&size=<?= $size ?>" 
                                    target="_blank"
                                    class="bg-gradient-to-r from-blue-600 to-indigo-600 text-white px-8 py-4 rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 inline-flex items-center justify-center font-semibold"
                                >
                                    <i class="fas fa-search-plus mr-2"></i>
                                    🔍 ເບິ່ງຂະໜາດເຕັມ
                                </a>
                                
                                <button 
                                    onclick="copyToClipboard('<?= addslashes($qrResult['url']) ?>')"
                                    class="bg-gradient-to-r from-gray-600 to-slate-600 text-white px-8 py-4 rounded-xl hover:from-gray-700 hover:to-slate-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1 inline-flex items-center justify-center font-semibold"
                                >
                                    <i class="fas fa-copy mr-2"></i>
                                    📋 ຄັດລອກ URL
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-red-100 rounded-full mb-6">
                                <i class="fas fa-exclamation-triangle text-4xl text-red-500"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-red-600 mb-4">Error / ຂໍ້ຜິດພາດ</h3>
                            <div class="bg-red-50 border border-red-200 rounded-xl p-6">
                                <p class="text-red-700"><?= htmlspecialchars($qrResult['error']) ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- Usage Examples -->
            <div class="bg-white rounded-2xl shadow-2xl p-8 mb-8 animate-fade-in"
                 style="background: linear-gradient(135deg, #ffffff 0%, #faf5ff 100%);">
                <h3 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                    <i class="fas fa-lightbulb text-yellow-500 mr-3"></i>
                    Usage Examples / ຕົວຢ່າງການໃຊ້ງານ
                </h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-6">
                        <h4 class="font-semibold text-blue-800 mb-4 flex items-center">
                            <i class="fas fa-globe mr-2"></i>
                            Website URLs / ລິງກ໌ເວັບໄຊ
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                <i class="fas fa-school text-blue-500 mr-3"></i>
                                <div>
                                    <strong>School Website:</strong>
                                    <p class="text-gray-600">https://www.ongtue-ttc.edu.la</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                <i class="fas fa-language text-purple-500 mr-3"></i>
                                <div>
                                    <strong>Lao Text URL:</strong>
                                    <p class="text-gray-600">https://example.com/ຂໍ້ມູນນັກສຶກສາ</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-6">
                        <h4 class="font-semibold text-green-800 mb-4 flex items-center">
                            <i class="fas fa-user-graduate mr-2"></i>
                            Student System / ລະບົບນັກສຶກສາ
                        </h4>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                <i class="fas fa-id-card text-green-500 mr-3"></i>
                                <div>
                                    <strong>Student Profile:</strong>
                                    <p class="text-gray-600"><?= BASE_URL ?>?page=student-detail&id=123</p>
                                </div>
                            </div>
                            <div class="flex items-center p-3 bg-white rounded-lg shadow-sm">
                                <i class="fas fa-user-plus text-blue-500 mr-3"></i>
                                <div>
                                    <strong>Registration:</strong>
                                    <p class="text-gray-600"><?= BASE_URL ?>?page=register</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-6 p-6 bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl border border-amber-200">
                    <div class="flex items-start">
                        <i class="fas fa-info-circle text-amber-600 mr-3 mt-1"></i>
                        <div>
                            <h4 class="font-semibold text-amber-800 mb-2">Tips / ຄຳແນະນຳ</h4>
                            <ul class="text-sm text-amber-700 space-y-1">
                                <li>• ສຳລັບ URL ທີ່ຍາວ ແນະນຳໃຫ້ໃຊ້ຂະໜາດ 400x400 ຫຼື 500x500</li>
                                <li>• QR Code ຮອງຮັບພາສາລາວ ແລະ UTF-8 ຢ່າງສົມບູນ</li>
                                <li>• ໄຟລ໌ QR Code ຈະຖືກເກັບໄວ້ເພື່ອການໃຊ້ງານໃນອະນາຄົດ</li>
                                <li>• ສາມາດດາວໂຫລດໄຟລ໌ PNG ເພື່ອໃຊ້ງານອື່ນໆ</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
                
                <div class="mt-6 p-6 bg-gradient-to-r from-indigo-50 to-purple-50 rounded-xl border border-indigo-200">
                    <h4 class="font-semibold text-indigo-800 mb-4 flex items-center">
                        <i class="fas fa-rocket mr-2"></i>
                        📋 Quick Links / ລິງກ໌ດ່ວນ
                    </h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <a href="?page=qrcode&url=<?= urlencode('https://www.ongtue-ttc.edu.la') ?>" 
                           class="flex items-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-1 border border-blue-100 hover:border-blue-300">
                            <i class="fas fa-school text-blue-500 mr-3"></i>
                            <div>
                                <div class="font-semibold text-blue-800">🌐 College Website</div>
                                <div class="text-xs text-gray-600">ເວັບໄຊວິທະຍາໄລ</div>
                            </div>
                        </a>
                        <a href="?page=qrcode&url=<?= urlencode(BASE_URL . '?page=register') ?>" 
                           class="flex items-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-1 border border-green-100 hover:border-green-300">
                            <i class="fas fa-user-plus text-green-500 mr-3"></i>
                            <div>
                                <div class="font-semibold text-green-800">📝 Registration Form</div>
                                <div class="text-xs text-gray-600">ແບບຟອມລົງທະບຽນ</div>
                            </div>
                        </a>
                        <a href="?page=qrcode&url=<?= urlencode(BASE_URL . '?page=students') ?>" 
                           class="flex items-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-1 border border-purple-100 hover:border-purple-300">
                            <i class="fas fa-users text-purple-500 mr-3"></i>
                            <div>
                                <div class="font-semibold text-purple-800">👥 Student List</div>
                                <div class="text-xs text-gray-600">ລາຍຊື່ນັກສຶກສາ</div>
                            </div>
                        </a>
                        <a href="qr-examples.php" target="_blank"
                           class="flex items-center p-4 bg-white rounded-lg shadow-sm hover:shadow-md transition-all duration-200 transform hover:-translate-y-1 border border-orange-100 hover:border-orange-300">
                            <i class="fas fa-magic text-orange-500 mr-3"></i>
                            <div>
                                <div class="font-semibold text-orange-800">🎯 More Examples</div>
                                <div class="text-xs text-gray-600">ຕົວຢ່າງເພີ່ມເຕີມ</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(function() {
            Swal.fire({
                icon: 'success',
                title: 'ສຳເລັດ!',
                text: 'URL copied to clipboard! / ຄັດລອກ URL ແລ້ວ!',
                timer: 2000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end',
                background: '#d1fae5',
                color: '#065f46'
            });
        }, function(err) {
            console.error('Could not copy text: ', err);
            Swal.fire({
                icon: 'error',
                title: 'ຂໍ້ຜິດພາດ!',
                text: 'Failed to copy URL / ຄັດລອກ URL ບໍ່ສຳເລັດ',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        });
    }
    
    // Auto-focus on URL input with enhanced UX
    document.addEventListener('DOMContentLoaded', function() {
        const urlInput = document.getElementById('url');
        if (urlInput && !urlInput.value) {
            urlInput.focus();
        }
        
        // Add form validation and loading states
        const form = document.querySelector('form');
        if (form) {
            form.addEventListener('submit', function(e) {
                const url = document.getElementById('url').value.trim();
                if (!url) {
                    e.preventDefault();
                    Swal.fire({
                        icon: 'warning',
                        title: 'ກະລຸນາປ້ອນຂໍ້ມູນ',
                        text: 'Please enter a URL / ກະລຸນາປ້ອນ URL',
                        confirmButtonText: 'ຕົກລົງ',
                        confirmButtonColor: '#8b5cf6'
                    });
                    return false;
                }
                
                // Show loading animation
                const submitBtn = form.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>ກຳລັງສ້າງ...';
                submitBtn.disabled = true;
                
                // Restore button after a delay (in case of quick redirect)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 3000);
            });
        }
        
        // Add animation to success elements
        const successElements = document.querySelectorAll('.animate-fade-in');
        successElements.forEach((element, index) => {
            element.style.animationDelay = `${index * 0.1}s`;
        });
    });
    </script>
</body>
</html>
