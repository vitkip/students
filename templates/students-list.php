<?php
// filepath: c:\xampp\htdocs\register-learning\templates\students-list.php

// ตรวจสอบว่ามีการเรียกใช้ผ่าน index.php หรือไม่
if (!defined('BASE_PATH')) {
    header('Location: ../public/index.php?page=students');
    exit('Access denied. Please use proper navigation.');
}

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!isset($db) || !$db) {
    die('Database connection not available');
}

// ตรวจสอบว่ามีตัวแปร $students จาก index.php หรือไม่
if (!isset($students)) {
    die('Students data not available');
}

// กำหนดค่าเริ่มต้นสำหรับตัวแปรที่จำเป็น
$current_search = $current_search ?? '';
$current_major = $current_major ?? 0;
$current_year = $current_year ?? 0;
$current_page = $current_page ?? 1;
$total_students = $total_students ?? 0;
$total_pages = $total_pages ?? 1;
$students_per_page = $students_per_page ?? 10;
$majors = $majors ?? [];
$academicYears = $academicYears ?? [];

// นำเข้าไฟล์ helper functions
if (file_exists(BASE_PATH . '/src/helpers/functions.php')) {
    require_once BASE_PATH . '/src/helpers/functions.php';
}

// ฟังก์ชันสำหรับแสดงรูปภาพที่ปลอดภัย
function getSafePhotoUrl($photo, $fallback = null) {
    if (empty($photo)) {
        return $fallback;
    }
    
    $photo_path = BASE_PATH . '/public/uploads/photos/' . $photo;
    if (file_exists($photo_path)) {
        return BASE_URL . 'uploads/photos/' . htmlspecialchars($photo);
    }
    
    return $fallback;
}

// ฟังก์ชันสำหรับสร้าง URL pagination
function buildPaginationUrl($page, $params = []) {
    $url_params = ['page' => 'students', 'p' => $page];
    
    // เพิ่ม search parameters
    if (!empty($params['search'])) $url_params['search'] = $params['search'];
    if (!empty($params['major']) && $params['major'] > 0) $url_params['major'] = $params['major'];
    if (!empty($params['year']) && $params['year'] > 0) $url_params['year'] = $params['year'];
    if (!empty($params['students_per_page']) && $params['students_per_page'] != 10) $url_params['students_per_page'] = $params['students_per_page'];
    
    return BASE_URL . 'index.php?' . http_build_query($url_params);
}
?>

<!-- Loading Overlay -->
<div id="loading-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center" style="display: none;">
    <div class="w-16 h-16 border-4 border-t-amber-500 border-gray-200 rounded-full animate-spin"></div>
</div>

<div class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 animate-fade-in">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row md:justify-between md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-1">
                <i class="fas fa-users text-amber-500 mr-3"></i>
                ລາຍຊື່ນັກສຶກສາ
            </h1>
            <p class="text-gray-600">
                ທັງໝົດ <span class="font-semibold text-amber-600"><?= number_format($total_students) ?></span> ລາຍການ
                <?php if (!empty($current_search) || $current_major > 0 || $current_year > 0): ?>
                    <span class="text-sm text-gray-500 ml-2">(ຜົນການຄົ້ນຫາ)</span>
                <?php endif; ?>
            </p>
        </div>
        <div class="flex items-center gap-2">
            <a href="<?= BASE_URL ?>index.php?page=register" 
               class="inline-flex items-center justify-center px-4 py-2 bg-amber-500 text-white font-semibold rounded-lg shadow-md hover:bg-amber-600 transition-colors duration-300">
                <i class="fas fa-user-plus mr-2"></i> 
                ລົງທະບຽນໃໝ່
            </a>
        </div>
    </div>

    <!-- Search and Filter Section (Collapsible) -->
    <div class="bg-white rounded-xl shadow-lg mb-8">
        <details class="p-6" open>
            <summary class="font-bold text-lg text-gray-800 cursor-pointer flex justify-between items-center">
                <span><i class="fas fa-search mr-2 text-amber-500"></i>ຄົ້ນຫາ ແລະ ຕົວກອງ</span>
                <i class="fas fa-chevron-down transition-transform duration-300"></i>
            </summary>
            <form method="GET" action="<?= BASE_URL ?>index.php" class="mt-6 space-y-4" id="searchForm">
                <input type="hidden" name="page" value="students">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="lg:col-span-2">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">ຄົ້ນຫາ</label>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                            <input type="text" id="search" name="search" value="<?= htmlspecialchars($current_search) ?>"
                                   placeholder="ຊື່, ນາມສະກຸນ, ລະຫັດນັກສຶກສາ..."
                                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                        </div>
                    </div>
                    <div>
                        <label for="major" class="block text-sm font-medium text-gray-700 mb-1">ສາຂາວິຊາ</label>
                        <select id="major" name="major" class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                            <option value="0">ທຸກສາຂາ</option>
                            <?php foreach ($majors as $major): ?>
                                <option value="<?= $major['id'] ?>" <?= $current_major == $major['id'] ? 'selected' : '' ?>><?= htmlspecialchars($major['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label for="year" class="block text-sm font-medium text-gray-700 mb-1">ປີການສຶກສາ</label>
                        <select id="year" name="year" class="w-full py-2 px-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-amber-500 focus:border-amber-500 transition">
                            <option value="0">ທຸກປີການສຶກສາ</option>
                            <?php foreach ($academicYears as $year): ?>
                                <option value="<?= $year['id'] ?>" <?= $current_year == $year['id'] ? 'selected' : '' ?>><?= htmlspecialchars($year['year']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="flex items-center gap-3 pt-4 border-t border-gray-200">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-semibold rounded-lg shadow-md hover:bg-blue-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>ຄົ້ນຫາ
                    </button>
                    <a href="<?= BASE_URL ?>index.php?page=students" class="inline-flex items-center px-4 py-2 bg-gray-200 text-gray-700 font-semibold rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-undo mr-2"></i>ລ້າງຕົວກອງ
                    </a>
                </div>
            </form>
        </details>
    </div>

    <?php if (!empty($students)): ?>
        <!-- Students Table Card -->
        <div class="bg-white shadow-xl rounded-xl overflow-hidden">
     
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-100">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">ຮູບພາບ</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">ເພດ</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">ຊື່ ແລະ ລະຫັດນັກສຶກສາ</th>          
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">ສາຂາວິຊາ</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-gray-600 uppercase tracking-wider">ເບອຜູ້ຕິດຕໍ່</th>
                            <th scope="col" class="px-6 py-3 text-center text-xs font-bold text-gray-600 uppercase tracking-wider">ການກະທຳ</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($students as $student): ?>
                            <tr class="hover:bg-amber-50 transition-colors duration-200">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <?php $photo_url = getSafePhotoUrl($student['photo']); ?>
                                    <?php if ($photo_url): ?>
                                        <img src="<?= $photo_url ?>" alt="Student Photo" class="w-12 h-12 rounded-full object-cover border-2 border-amber-200 shadow-sm hover:scale-110 transition-transform">
                                    <?php else: ?>
                                        <div class="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500 border-2 border-gray-300">
                                            <i class="fas fa-user text-xl"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                 <td class="px-6 py-4 whitespace-nowrap">
                                    <?php
                                    $gender_class = 'bg-gray-100 text-gray-800';
                                    if (($student['gender'] ?? '') === 'ຊາຍ') $gender_class = 'bg-blue-100 text-blue-800';
                                    if (($student['gender'] ?? '') === 'ຍິງ') $gender_class = 'bg-pink-100 text-pink-800';
                                    ?>
                                    <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full <?= $gender_class ?>">
                                        <?= htmlspecialchars($student['gender'] ?? 'N/A') ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-semibold text-gray-900"><?= htmlspecialchars($student['first_name'] . ' ' . $student['last_name']) ?></div>
                                    <div class="text-xs text-gray-500"><?= htmlspecialchars($student['student_id'] ?? 'N/A') ?></div>
                                </td>
                               
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($student['major_name'] ?? 'N/A') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?= htmlspecialchars($student['phone'] ?? '-') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <a href="<?= BASE_URL ?>index.php?page=student-detail&id=<?= $student['id'] ?>" class="p-2 text-blue-600 hover:text-blue-900 hover:bg-blue-100 rounded-full transition" title="ເບິ່ງລາຍລະອຽດ"><i class="fas fa-eye fa-fw"></i></a>
                                        <a href="<?= BASE_URL ?>index.php?page=student-edit&id=<?= $student['id'] ?>" class="p-2 text-green-600 hover:text-green-900 hover:bg-green-100 rounded-full transition" title="ແກ້ໄຂຂໍ້ມູນ"><i class="fas fa-edit fa-fw"></i></a>
                                        <a href="<?= BASE_URL ?>index.php?page=student-card&id=<?= $student['id'] ?>" class="p-2 text-purple-600 hover:text-purple-900 hover:bg-purple-100 rounded-full transition" title="ພິມບັດນັກສຶກສາ"><i class="fas fa-id-card fa-fw"></i></a>
                                        <!-- ปุ่มลบ -->
<a href="#" onclick="confirmDelete(<?= $student['id'] ?>)" class="text-red-600 hover:text-red-800 transition-colors" title="ລຶບຂໍ້ມູນ">
    <i class="fas fa-trash-alt"></i>
</a>
<script>
function confirmDelete(id) {
    if (confirm('ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບຂໍ້ມູນນັກສຶກສາຄົນນີ້?')) {
        window.location.href = '<?= BASE_URL ?>?page=students&action=delete&id=' + id;
    }
}
</script>
                                        
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
                   <!-- Table Actions -->
            <div class="p-4 flex flex-col sm:flex-row justify-between items-center gap-3 bg-gray-50 border-b">
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">ສະແດງ:</span>
                    <?php $page_sizes = [10, 25, 50, 100]; ?>
                    <?php foreach ($page_sizes as $size): ?>
                        <?php
                        $url_params = ['page' => 'students', 'students_per_page' => $size, 'p' => 1];
                        if (!empty($current_search)) $url_params['search'] = $current_search;
                        if ($current_major > 0) $url_params['major'] = $current_major;
                        if ($current_year > 0) $url_params['year'] = $current_year;
                        $size_url = BASE_URL . "index.php?" . http_build_query($url_params);
                        ?>
                        <a href="<?= $size_url ?>" class="px-3 py-1 text-sm font-medium rounded-md <?= $students_per_page == $size ? 'bg-amber-500 text-white shadow' : 'bg-white text-gray-700 hover:bg-gray-100 border' ?>">
                            <?= $size ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <div class="text-sm text-gray-600">
                    <?php 
                    $start = ($current_page - 1) * $students_per_page + 1;
                    $end = min($start + count($students) - 1, $total_students);
                    ?>
                    ກຳລັງສະແດງ <?= $start ?> - <?= $end ?> ຈາກ <?= number_format($total_students) ?>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <div class="mt-8 flex items-center justify-center">
                <?php
                $pagination_params = [];
                if (!empty($current_search)) $pagination_params['search'] = $current_search;
                if ($current_major > 0) $pagination_params['major'] = $current_major;
                if ($current_year > 0) $pagination_params['year'] = $current_year;
                if ($students_per_page != 10) $pagination_params['students_per_page'] = $students_per_page;
                ?>
                <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <a href="<?= $current_page > 1 ? buildPaginationUrl($current_page - 1, $pagination_params) : '#' ?>"
                       class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?= $current_page <= 1 ? 'opacity-50 cursor-not-allowed' : '' ?>">
                        <span class="sr-only">Previous</span>
                        <i class="fas fa-chevron-left h-5 w-5"></i>
                    </a>
                    <?php
                    $num_links_to_show = 5;
                    $start_link = max(1, $current_page - floor($num_links_to_show / 2));
                    $end_link = min($total_pages, $start_link + $num_links_to_show - 1);
                    if ($end_link - $start_link + 1 < $num_links_to_show) {
                        $start_link = max(1, $end_link - $num_links_to_show + 1);
                    }
                    if ($start_link > 1) {
                        echo '<a href="' . buildPaginationUrl(1, $pagination_params) . '" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">1</a>';
                        if ($start_link > 2) echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                    }
                    for ($i = $start_link; $i <= $end_link; $i++): ?>
                        <a href="<?= buildPaginationUrl($i, $pagination_params) ?>"
                           aria-current="page"
                           class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?= $i == $current_page ? 'z-10 bg-amber-50 border-amber-500 text-amber-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor;
                    if ($end_link < $total_pages) {
                        if ($end_link < $total_pages - 1) echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                        echo '<a href="' . buildPaginationUrl($total_pages, $pagination_params) . '" class="bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium">' . $total_pages . '</a>';
                    }
                    ?>
                     <a href="<?= $current_page < $total_pages ? buildPaginationUrl($current_page + 1, $pagination_params) : '#' ?>"
                       class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?= $current_page >= $total_pages ? 'opacity-50 cursor-not-allowed' : '' ?>">
                        <span class="sr-only">Next</span>
                        <i class="fas fa-chevron-right h-5 w-5"></i>
                    </a>
                </nav>
            </div>
        <?php endif; ?>

    <?php else: ?>
        <!-- Empty State Card -->
        <div class="bg-white rounded-xl shadow-lg text-center p-8 md:p-16">
            <div class="w-24 h-24 mx-auto bg-yellow-100 rounded-full flex items-center justify-center mb-6">
                <i class="fas fa-search text-5xl text-yellow-500"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-800 mb-2">
                <?= !empty($current_search) || $current_major > 0 || $current_year > 0 ? 'ບໍ່ພົບຜົນການຄົ້ນຫາ' : 'ຍັງບໍ່ມີຂໍ້ມູນນັກສຶກສາ' ?>
            </h3>
            <p class="text-gray-600 mb-6">
                <?php if (!empty($current_search) || $current_major > 0 || $current_year > 0): ?>
                    ກະລຸນາລອງປ່ຽນເງື່ອນໄຂການຄົ້ນຫາ ຫຼື ລ້າງຕົວກອງເພື່ອສະແດງຂໍ້ມູນທັງໝົດ
                <?php else: ?>
                    ເລີ່ມຕົ້ນໂດຍການເພີ່ມຂໍ້ມູນນັກສຶກສາຄົນທຳອິດ
                <?php endif; ?>
            </p>
            <div class="flex justify-center gap-4">
                <a href="<?= BASE_URL ?>index.php?page=register" class="inline-flex items-center px-6 py-3 bg-amber-500 text-white font-bold rounded-lg shadow-md hover:bg-amber-600 transition-colors">
                    <i class="fas fa-user-plus mr-2"></i> ລົງທະບຽນນັກສຶກສາໃໝ່
                </a>
                <?php if (!empty($current_search) || $current_major > 0 || $current_year > 0): ?>
                    <a href="<?= BASE_URL ?>index.php?page=students" class="inline-flex items-center px-6 py-3 bg-gray-200 text-gray-700 font-bold rounded-lg hover:bg-gray-300 transition-colors">
                        <i class="fas fa-undo mr-2"></i> ລ້າງຕົວກອງ
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchForm = document.getElementById('searchForm');
    const loadingOverlay = document.getElementById('loading-overlay');

    // Show loading overlay on form submission
    searchForm.addEventListener('submit', function() {
        loadingOverlay.style.display = 'flex';
    });

    // Auto-submit form when changing filters
    document.querySelectorAll('#major, #year').forEach(function(select) {
        select.addEventListener('change', function() {
            loadingOverlay.style.display = 'flex';
            searchForm.submit();
        });
    });

    // Toggle chevron icon for collapsible search box
    const details = document.querySelector('details');
    details.addEventListener('toggle', function() {
        const icon = this.querySelector('summary i.fa-chevron-down');
        icon.classList.toggle('rotate-180');
    });

    // Show session messages with SweetAlert2
    <?php if (isset($_SESSION['message'])): ?>
        Swal.fire({
            title: '<?= $_SESSION['message_type'] === 'success' ? 'ສຳເລັດ!' : 'ເກີດຂໍ້ຜິດພາດ!' ?>',
            text: '<?= addslashes($_SESSION['message']) ?>',
            icon: '<?= $_SESSION['message_type'] ?>',
            confirmButtonText: 'ຕົກລົງ',
            confirmButtonColor: '#f59e0b'
        });
        <?php 
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        ?>
    <?php endif; ?>
});

// Confirm delete function
function confirmDelete(studentId, studentName) {
    Swal.fire({
        title: 'ຢືນຢັນການລຶບ',
        html: `ທ່ານແນ່ໃຈບໍ່ວ່າຕ້ອງການລຶບຂໍ້ມູນຂອງ <br><strong class="text-red-600">${studentName}</strong>? <br><small class="text-gray-600">ການກະທຳນີ້ບໍ່ສາມາດຍົກເລີກໄດ້</small>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '<i class="fas fa-trash-alt"></i> ແມ່ນ, ລຶບເລີຍ',
        cancelButtonText: '<i class="fas fa-times"></i> ຍົກເລີກ',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('loading-overlay').style.display = 'flex';
            
            const currentParams = new URLSearchParams(window.location.search);
            currentParams.set('action', 'delete');
            currentParams.set('id', studentId);
            
            window.location.href = `<?= BASE_URL ?>index.php?${currentParams.toString()}`;
        }
    });
}
</script>