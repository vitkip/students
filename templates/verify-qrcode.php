<?php
// filepath: c:\xampp\htdocs\register-learning\templates\verify-qrcode.php

// ລວມເອົາຂໍ້ຄວາມແຈ້ງເຕືອນ
include_once __DIR__ . '/includes/messages.php';

// ດຶງຂໍ້ມູນການກວດສອບ (ຖ້າມີ)
$verificationResult = isset($_SESSION['verification_result']) ? $_SESSION['verification_result'] : null;
unset($_SESSION['verification_result']);
?>

<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <h1 class="text-2xl font-bold mb-6 text-center">ກວດສອບ QR Code ນັກສຶກສາ</h1>
    
    <div class="mb-8">
        <p class="text-gray-600 mb-4">
            ທ່ານສາມາດກວດສອບການລົງທະບຽນຂອງນັກສຶກສາດ້ວຍການອັບໂຫລດໄຟລ QR Code 
            ຫຼື ປ້ອນລະຫັດນັກສຶກສາເພື່ອກວດສອບ.
        </p>
    </div>
    
    <div class="grid md:grid-cols-2 gap-8">
        <!-- ອັບໂຫລດໄຟລ QR Code -->
        <div class="bg-blue-50 p-6 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">ອັບໂຫລດ QR Code</h2>
            <form action="index.php?page=verify-qrcode&action=upload" method="POST" enctype="multipart/form-data">
                <div class="mb-4">
                    <input type="file" name="qrcode_image" accept="image/*" required
                          class="block w-full text-sm text-gray-500
                          file:mr-4 file:py-2 file:px-4
                          file:rounded-full file:border-0
                          file:text-sm file:font-semibold
                          file:bg-blue-50 file:text-blue-700
                          hover:file:bg-blue-100">
                </div>
                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white py-2 px-4 rounded transition-colors">
                    ກວດສອບ QR Code
                </button>
            </form>
        </div>
        
        <!-- ປ້ອນລະຫັດນັກສຶກສາ -->
        <div class="bg-green-50 p-6 rounded-lg">
            <h2 class="text-lg font-semibold mb-4">ປ້ອນລະຫັດນັກສຶກສາ</h2>
            <form action="index.php?page=verify-qrcode&action=id" method="POST">
                <div class="mb-4">
                    <label for="student_id" class="block text-sm font-medium text-gray-700 mb-1">ລະຫັດນັກສຶກສາ</label>
                    <input type="number" name="student_id" id="student_id" required placeholder="ປ້ອນລະຫັດນັກສຶກສາ"
                          class="w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring focus:ring-blue-500 focus:ring-opacity-50">
                </div>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-2 px-4 rounded transition-colors">
                    ກວດສອບຂໍ້ມູນນັກສຶກສາ
                </button>
            </form>
        </div>
    </div>
    
    <!-- ຜົນການກວດສອບ -->
    <?php if ($verificationResult): ?>
        <div class="mt-8 p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold mb-4">ຜົນການກວດສອບ</h2>
            
            <?php if ($verificationResult['valid']): ?>
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                    <p class="font-bold">ຢືນຢັນສຳເລັດ!</p>
                    <p>ຂໍ້ມູນນັກສຶກສາຖືກພົບໃນລະບົບ.</p>
                </div>
                
                <div class="grid md:grid-cols-2 gap-4">
                    <!-- ຂໍ້ມູນນັກສຶກສາ -->
                    <div>
                        <p><span class="font-semibold">ຊື່-ນາມສະກຸນ:</span> <?= htmlspecialchars($verificationResult['student']['first_name'] . ' ' . $verificationResult['student']['last_name']) ?></p>
                        <p><span class="font-semibold">ລະຫັດນັກສຶກສາ:</span> <?= htmlspecialchars($verificationResult['student']['id']) ?></p>
                        <p><span class="font-semibold">ເພດ:</span> <?= htmlspecialchars($verificationResult['student']['gender']) ?></p>
                        <p><span class="font-semibold">ສາຂາ:</span> <?= htmlspecialchars($verificationResult['student']['major_name']) ?></p>
                        <p><span class="font-semibold">ປີການສຶກສາ:</span> <?= htmlspecialchars($verificationResult['student']['academic_year']) ?></p>
                    </div>
                    
                    <!-- ສະຖານະ -->
                    <div class="bg-gray-50 p-3 rounded">
                        <p><span class="font-semibold">ວັນທີລົງທະບຽນ:</span> <?= htmlspecialchars(date('d/m/Y H:i', strtotime($verificationResult['student']['registered_at']))) ?></p>
                        <p><span class="font-semibold">ສະຖານະ:</span> <span class="text-green-600 font-semibold">ລົງທະບຽນແລ້ວ</span></p>
                    </div>
                </div>
            <?php else: ?>
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4">
                    <p class="font-bold">ບໍ່ສາມາດຢືນຢັນໄດ້!</p>
                    <p><?= htmlspecialchars($verificationResult['message']) ?></p>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>