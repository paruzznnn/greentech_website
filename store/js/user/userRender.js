import { redirectGet, showNotification } from '../formHandler.js';

const MAX_FILE_SIZE = 2 * 1024 * 1024; // 2 MB

export function setupProfileImageUpload() {
    const profileImage = document.getElementById('profileImage');
    const editImageIcon = document.getElementById('editImageIcon');
    const imageUpload = document.getElementById('imageUpload');

    if (!profileImage || !editImageIcon || !imageUpload) return;

    editImageIcon.addEventListener('click', () => {
        imageUpload.click();
    });

    imageUpload.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            if (file.size > MAX_FILE_SIZE) {
                showNotification('ขนาดไฟล์รูปภาพใหญ่เกินไป กรุณาเลือกไฟล์ที่มีขนาดไม่เกิน 2 MB', 'error');
                event.target.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = (e) => {
                profileImage.src = e.target.result;
                showNotification('อัปโหลดรูปภาพสำเร็จ!', 'success');
            };
            reader.readAsDataURL(file);
        }
    });
}