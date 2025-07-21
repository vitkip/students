// filepath: /register-learning/register-learning/public/assets/js/form-validation.js

// ຟັງຊັນສໍາລັບການກວດສອບຟອມລົງທະບຽນ
function validateForm() {
    // ດຶງຄໍາເຂົ້າຂອງຜູ້ໃຊ້
    const firstName = document.getElementById('first_name').value.trim();
    const lastName = document.getElementById('last_name').value.trim();
    const email = document.getElementById('email').value.trim();
    const phone = document.getElementById('phone').value.trim();
    const dob = document.getElementById('dob').value.trim();
    const major = document.getElementById('major').value;
    const academicYear = document.getElementById('academic_year').value;

    // ກວດສອບຄໍາເຂົ້າທີ່ຈະລົງທະບຽນ
    if (firstName === '' || lastName === '' || email === '' || phone === '' || dob === '' || major === '' || academicYear === '') {
        alert('ກະລຸນາໃສ່ຂໍ້ມູນທັງໝົດ!');
        return false;
    }

    // ກວດສອບອີເມວ
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        alert('ອີເມວບໍ່ຖືກຕ້ອງ!');
        return false;
    }

    // ກວດສອບເບີໂທ
    const phonePattern = /^[0-9]{10,15}$/;
    if (!phonePattern.test(phone)) {
        alert('ເບີໂທຈະຕ້ອງມີລະຫັດສູງສຸດ 10 ເຖິງ 15 ຕົວ!');
        return false;
    }

    // ຖ້າທຸກຢ່າງຖືກຕ້ອງ, ສົ່ງຟອມ
    return true;
}

// ກຳນົດໃຫ້ຟອມລົງທະບຽນໃຊ້ຟັງຊັນກວດສອບ
document.getElementById('registrationForm').onsubmit = function() {
    return validateForm();
};