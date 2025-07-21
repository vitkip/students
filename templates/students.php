<!-- filepath: c:\xampp\htdocs\register-learning\templates\students.php -->
<div class="mb-6">
    <div class="bg-white p-4 rounded-lg shadow-md">
        <form action="index.php" method="GET" class="flex flex-wrap items-end gap-4">
            <input type="hidden" name="page" value="students">
            
            <div class="flex-1 min-w-[200px]">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">ຄົ້ນຫາ</label>
                <input type="text" id="search" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                    placeholder="ຄົ້ນຫາຕາມຊື່, ນາມສະກຸນ ຫຼື ລະຫັດນັກສຶກສາ..." 
                    class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
            </div>
            
            <div>
                <label for="major" class="block text-sm font-medium text-gray-700 mb-1">ສາຂາວິຊາ</label>
                <select id="major" name="major_id" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="">-- ທັງໝົດ --</option>
                    <?php foreach($majors as $major): ?>
                        <option value="<?= $major['id'] ?>" <?= (isset($_GET['major_id']) && $_GET['major_id'] == $major['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($major['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div>
                <label for="academic_year" class="block text-sm font-medium text-gray-700 mb-1">ປີການສຶກສາ</label>
                <select id="academic_year" name="academic_year_id" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                    <option value="">-- ທັງໝົດ --</option>
                    <?php foreach($academicYears as $year): ?>
                        <option value="<?= $year['id'] ?>" <?= (isset($_GET['academic_year_id']) && $_GET['academic_year_id'] == $year['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($year['year']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="flex gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded-md flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    ຄົ້ນຫາ
                </button>
                
                <a href="index.php?page=students" class="bg-gray-500 hover:bg-gray-600 text-white py-2 px-4 rounded-md flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    ລ້າງຕົວກອງ
                </a>
            </div>
        </form>
    </div>
</div>

<!-- เพิ่มต่อท้ายตารางหรือรายการนักศึกษา -->
<div class="mt-6">
    <?php if ($result['total'] > 0): ?>
        <div class="flex justify-between items-center text-sm text-gray-600 mb-4">
            <div>
                กำลังแสดง <?= ($page-1)*$perPage + 1 ?> - <?= min($page*$perPage, $result['total']) ?> จาก <?= $result['total'] ?> รายการ
            </div>
            
            <div>
                หน้า <?= $page ?> จาก <?= $totalPages ?>
            </div>
        </div>
        
        <?php if ($totalPages > 1): ?>
            <div class="flex justify-center">
                <nav class="inline-flex rounded-md shadow">
                    <?php 
                    // สร้าง URL สำหรับลิงก์เปลี่ยนหน้า คงค่าการค้นหาเดิม
                    $queryParams = $_GET;
                    
                    // ปุ่มหน้าแรก
                    if ($page > 1):
                        $queryParams['p'] = 1;
                        $firstPageUrl = 'index.php?' . http_build_query($queryParams);
                    ?>
                        <a href="<?= $firstPageUrl ?>" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-l-md hover:bg-gray-50">
                            ໜ້າທຳອິດ
                        </a>
                    <?php endif; ?>
                    
                    <?php 
                    // ปุ่มหน้าก่อนหน้า
                    if ($page > 1):
                        $queryParams['p'] = $page - 1;
                        $prevPageUrl = 'index.php?' . http_build_query($queryParams);
                    ?>
                        <a href="<?= $prevPageUrl ?>" class="px-4 py-2 text-sm bg-white border-t border-b border-gray-300 hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                        </a>
                    <?php endif; ?>
                    
                    <?php
                    // แสดงตัวเลขหน้า
                    $range = 2; // จำนวนหน้าที่แสดงก่อนและหลังหน้าปัจจุบัน
                    for ($i = max(1, $page - $range); $i <= min($totalPages, $page + $range); $i++):
                        $queryParams['p'] = $i;
                        $pageUrl = 'index.php?' . http_build_query($queryParams);
                        $isActive = $i == $page;
                    ?>
                        <a href="<?= $pageUrl ?>" 
                           class="px-4 py-2 text-sm <?= $isActive ? 'bg-blue-600 text-white border border-blue-600' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                    
                    <?php 
                    // ปุ่มหน้าถัดไป
                    if ($page < $totalPages):
                        $queryParams['p'] = $page + 1;
                        $nextPageUrl = 'index.php?' . http_build_query($queryParams);
                    ?>
                        <a href="<?= $nextPageUrl ?>" class="px-4 py-2 text-sm bg-white border-t border-b border-gray-300 hover:bg-gray-50">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    <?php endif; ?>
                    
                    <?php 
                    // ปุ่มหน้าสุดท้าย
                    if ($page < $totalPages):
                        $queryParams['p'] = $totalPages;
                        $lastPageUrl = 'index.php?' . http_build_query($queryParams);
                    ?>
                        <a href="<?= $lastPageUrl ?>" class="px-4 py-2 text-sm bg-white border border-gray-300 rounded-r-md hover:bg-gray-50">
                            ໜ້າສຸດທ້າຍ
                        </a>
                    <?php endif; ?>
                </nav>
            </div>
        <?php endif; ?>
    <?php else: ?>
        <div class="text-center py-6 bg-gray-50 rounded-lg">
            <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="mt-2 text-gray-500">ບໍ່ພົບຂໍ້ມູນນັກສຶກສາ</p>
        </div>
    <?php endif; ?>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php if (count($students) > 0): ?>
        <?php foreach ($students as $student): ?>
            <div class="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow border border-gray-200 overflow-hidden hover-rise">
                <div class="h-2 bg-primary-500"></div>
                <div class="p-5">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="text-lg font-semibold">
                            <?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?>
                        </h3>
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 text-xs font-medium rounded-full">
                            <?= htmlspecialchars($student['academic_year']) ?>
                        </span>
                    </div>
                    
                    <p class="text-gray-600 mb-2">
                        <span class="font-medium">ລະຫັດນັກສຶກສາ:</span> <?= htmlspecialchars($student['id']) ?>
                    </p>
                    <p class="text-gray-600 mb-4">
                        <span class="font-medium">ສາຂາວິຊາ:</span> <?= htmlspecialchars($student['major_name']) ?>
                    </p>
                    
                    <div class="flex justify-end">
                        <a href="index.php?page=student-detail&id=<?= $student['id'] ?>" 
                           class="text-primary-600 hover:text-primary-700 text-sm font-medium">
                            ເບິ່ງລາຍລະອຽດ →
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="col-span-3 text-center py-10 bg-gray-50 rounded-lg">
            <svg class="w-16 h-16 mx-auto text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="mt-4 text-xl text-gray-500">ບໍ່ພົບຂໍ້ມູນນັກສຶກສາທີ່ຄົ້ນຫາ</p>
            <p class="mt-2 text-gray-400">ລອງປ່ຽນເງື່ອນໄຂການຄົ້ນຫາແລ້ວລອງໃໝ່ອີກຄັ້ງ</p>
        </div>
    <?php endif; ?>
</div>