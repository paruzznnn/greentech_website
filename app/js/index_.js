// function toggleDropdown() {
//     const dropdownContent = document.getElementById("dropdownContent");
//     dropdownContent.classList.toggle("show");
// }

// // Close dropdown if clicked outside
// window.onclick = function(event) {
//     if (!event.target.matches('.dropbtn') && !event.target.closest('.dropdown')) {
//         const dropdowns = document.getElementsByClassName("dropdown-content");
//         for (let i = 0; i < dropdowns.length; i++) {
//             const openDropdown = dropdowns[i];
//             if (openDropdown.classList.contains('show')) {
//                 openDropdown.classList.remove('show');
//             }
//         }
//     }
// };

$(document).ready(function(){


});



function toggleDropdown(id) {
    closeAllDropdowns(); // Close any open dropdowns first
    const dropdown = document.getElementById(id);
    dropdown.style.display = 'block'; // Show the selected dropdown
    
    // Check if any dropdown is open to toggle the class
    if (dropdown.style.display === 'block') {
        $('#background-blur').addClass('tab-open');
        // $('#navbar-menu').addClass('tab-open');
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

window.addEventListener('scroll', function() {
let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

if (scrollTop > lastScrollTop) {
    headerTop.style.top = "-100px";
} else {
    headerTop.style.top = "0";
}

// lastScrollTop = scrollTop;
});



$('#navbar-menu a').on('click', function() {
    $('#navbar-menu a').removeClass('active');
    $(this).addClass('active');
});
