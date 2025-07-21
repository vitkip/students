/**
 * SweetAlert Handler for Student Management System
 */

// ฟังก์ชันสำหรับแสดง toast notification
function showToast(type, message, timer = 3000) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: timer,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer)
            toast.addEventListener('mouseleave', Swal.resumeTimer)
        }
    });

    Toast.fire({
        icon: type,
        title: message
    });
}

// ฟังก์ชันสำหรับ confirmation dialog
function confirmAction(title, text, confirmText = 'ຢືນຢັນ', cancelText = 'ຍົກເລີກ') {
    return Swal.fire({
        title: title,
        text: text,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        reverseButtons: true
    });
}

// ฟังก์ชันสำหรับแสดง loading
function showLoading(title = 'ກຳລັງດຳເນີນການ...', text = 'ກະລຸນາລໍຖ້າສັກຄູ່') {
    Swal.fire({
        title: title,
        text: text,
        icon: 'info',
        allowOutsideClick: false,
        allowEscapeKey: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
}

// ฟังก์ชันปิด loading
function hideLoading() {
    Swal.close();
}