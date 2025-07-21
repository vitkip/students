<?php
// ເອົາຂໍ້ມູນສາຂາແລະປີການສຶກສາ
require_once __DIR__ . '/../src/classes/Major.php';
require_once __DIR__ . '/../src/classes/AcademicYear.php';

// ສ້າງການເຊື່ອມຕໍ່ກັບຖານຂໍ້ມູນ
$database = new Database();
$db = $database->getConnection();

// ດຶງຂໍ້ມູນສາຂາຮຽນ
$majorObj = new Major($db);
$majors = $majorObj->readAll();

// ດຶງຂໍ້ມູນປີການສຶກສາ
$yearObj = new AcademicYear($db);
$academicYears = $yearObj->readAll();

// ລວມເອົາຂໍ້ຄວາມແຈ້ງເຕືອນ
include_once __DIR__ . '/includes/messages.php';
?>

<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold mb-6">ສົ່ງອອກລາຍງານ Excel</h2>
    
    <div class="grid md:grid-cols-2 gap-6">
        <!-- ລາຍງານທັງໝົດ -->
        <div class="bg-blue-50 p-6 rounded-lg border border-blue-100">
            <h3 class="text-lg font-semibold mb-3">ລາຍງານນັກສຶກສາທັງໝົດ</h3>
            <p class="text-gray-600 mb-4">ສົ່ງອອກຂໍ້ມູນນັກສຶກສາທັງໝົດໃນລະບົບເປັນໄຟລ Excel</p>
            <a href="index.php?page=export-processor&type=all" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md">
                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"></path>
                </svg>
                ດາວໂຫລດ Excel
            </a>
        </div>
        
        <!-- ລາຍງານຕາມສາຂາ -->
        <div class="bg-green-50 p-6 rounded-lg border border-green-100">
            <h3 class="text-lg font-semibold mb-3">ລາຍງານຕາມສາຂາ</h3>
            <p class="text-gray-600 mb-4">ເລືອກສາຂາເພື່ອສົ່ງອອກຂໍ້ມູນນັກສຶກສາສະເພາະສາຂານັ້ນ</p>
            
            <form action="index.php?page=export-processor&type=by_major" method="GET" class="mt-3">
                <input type="hidden" name="page" value="export-processor">
                <input type="hidden" name="type" value="by_major">
                
                <div class="mb-3">
                    <select name="major_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">-- ເລືອກສາຂາ --</option>
                        <?php foreach ($majors as $major): ?>
                            <option value="<?= $major['id'] ?>"><?= htmlspecialchars($major['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="inline-flex items-center bg-green-600 hover:bg-green-700 text-white font-medium py-2 px-4 rounded-md">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"></path>
                    </svg>
                    ດາວໂຫລດ Excel
                </button>
            </form>
        </div>
        
        <!-- ລາຍງານຕາມປີການສຶກສາ -->
        <div class="bg-purple-50 p-6 rounded-lg border border-purple-100">
            <h3 class="text-lg font-semibold mb-3">ລາຍງານຕາມປີການສຶກສາ</h3>
            <p class="text-gray-600 mb-4">ເລືອກປີການສຶກສາເພື່ອສົ່ງອອກຂໍ້ມູນນັກສຶກສາໃນປີນັ້ນ</p>
            
            <form action="index.php?page=export-processor&type=by_year" method="GET" class="mt-3">
                <input type="hidden" name="page" value="export-processor">
                <input type="hidden" name="type" value="by_year">
                
                <div class="mb-3">
                    <select name="year_id" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">-- ເລືອກປີການສຶກສາ --</option>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year['id'] ?>"><?= htmlspecialchars($year['year']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <button type="submit" class="inline-flex items-center bg-purple-600 hover:bg-purple-700 text-white font-medium py-2 px-4 rounded-md">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"></path>
                    </svg>
                    ດາວໂຫລດ Excel
                </button>
            </form>
        </div>
        
        <!-- ລາຍງານຕາມທີ່ພັກອາໄສ -->
        <div class="bg-amber-50 p-6 rounded-lg border border-amber-100">
            <h3 class="text-lg font-semibold mb-3">ລາຍງານຕາມທີ່ພັກອາໄສ</h3>
            <p class="text-gray-600 mb-4">ເລືອກປະເພດທີ່ພັກອາໄສເພື່ອສົ່ງອອກຂໍ້ມູນນັກສຶກສາ</p>
            
            <form action="index.php?page=export-processor&type=by_accommodation" method="GET" class="mt-3">
                <input type="hidden" name="page" value="export-processor">
                <input type="hidden" name="type" value="by_accommodation">
                
                <div class="mb-3">
                    <select name="accommodation_type" required class="block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                        <option value="">-- ເລືອກປະເພດທີ່ພັກ --</option>
                        <option value="ຫາວັດໃຫ້">ຫາວັດໃຫ້</option>
                        <option value="ມີວັດຢູ່ແລ້ວ">ມີວັດຢູ່ແລ້ວ</option>
                    </select>
                </div>
                
                <button type="submit" class="inline-flex items-center bg-amber-600 hover:bg-amber-700 text-white font-medium py-2 px-4 rounded-md">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M13 8V2H7v6H2l8 8 8-8h-5zM0 18h20v2H0v-2z"></path>
                    </svg>
                    ດາວໂຫລດ Excel
                </button>
            </form>
        </div>
    </div>
</div>