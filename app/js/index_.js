function toggleDropdown(id) {
    closeAllDropdowns(); // Close any open dropdowns first
    const dropdown = document.getElementById(id);
    
    if (dropdown) { // Check if dropdown exists
        dropdown.style.display = 'block'; // Show the selected dropdown
        
        // Check if any dropdown is open to toggle the class
        if (dropdown.style.display === 'block') {
            $('#background-blur').addClass('tab-open');
        }
    } else {
        // console.error(`Dropdown with id '${id}' does not exist.`);
    }
}

function closeAllDropdowns() {
    let anyOpen = false;
    const dropdowns = document.querySelectorAll('.dropdown-content');
    
    dropdowns.forEach(dropdown => {
        if (dropdown.style.display === 'block') {
            dropdown.style.display = 'none';
            anyOpen = true; // Set flag if any dropdown was open
        }
    });

    // Only remove the class if a dropdown was actually open
    if (anyOpen) {
        $('#background-blur').removeClass('tab-open');
    }
}




let lastScrollTop = 0;
const headerTop = document.querySelector('.header-top');
// const sections = document.querySelectorAll('.box-transform'); 

window.addEventListener('scroll', function() {
    let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

    // ซ่อน header เมื่อเลื่อนลง
    if (scrollTop > lastScrollTop) {
        headerTop.style.top = "-100px"; // ซ่อน header

        // วนลูปผ่านทุก .section
        // sections.forEach(section => {
        //     const img = section.querySelector('img'); 
        //     if (img) {
        //         img.style.transform = 'translateY(-20px)'; 
        //     }
        // });

    } else {
        headerTop.style.top = "0"; // แสดง header

        // วนลูปผ่านทุก .section
        // sections.forEach(section => {
        //     const img = section.querySelector('img'); // เลือกรูปภาพในแต่ละ section
        //     if (img) {
        //         img.style.transform = 'translateY(0)'; // ส่งภาพกลับไปที่ตำแหน่งเดิม
        //     }
        // });
    }

    lastScrollTop = scrollTop; // อัปเดต lastScrollTop
});




$('#navbar-menu a').on('click', function() {
    $('#navbar-menu a').removeClass('active');
    $(this).addClass('active');
});
