$(document).ready(function() {
    $('.nav-link').on('click', function() {
        // ลบคลาส 'active' จากลิงก์ทั้งหมด
        $('.nav-link').removeClass('active');
        // ซ่อนเนื้อหาของแท็บทั้งหมด
        $('.tab-pane').removeClass('show').hide();

        // เพิ่มคลาส 'active' ให้กับลิงก์ที่ถูกคลิก
        $(this).addClass('active');
        // แสดงเนื้อหาที่ตรงกับลิงก์ที่ถูกคลิก
        const target = $(this).data('target');
        $(target).addClass('show').fadeIn(); // ใช้ fadeIn เพื่อให้เนื้อหาแสดงอย่างนุ่มนวล
    });
});
