const basePath = 'http://localhost:3000/tdi_store/app/admin/';
// Custom setIn function to show an element after a delay
function setInLoading(element, delay) {
    setTimeout(function() {
    $(element).fadeIn();
    }, delay);
}

// Custom setOut function to hide an element after a delay
function setOutLoading(element, delay) {
    setTimeout(function() {
    $(element).fadeOut(); 
    }, delay);
}

$(document).ready(function() {

    setInLoading('#loading-overlay', 200);
    setOutLoading('#loading-overlay', 200);

    buildTabSidebar();

    var $sidebar = $('#showTabSidebar');
    $sidebar.hide();
    
    $("#toggleIcon").on("click", function() {
    
        $sidebar.toggle();
    
        var isVisible = $sidebar.is(":visible");
        $(this).toggleClass("fa-bars", !isVisible);
        $(this).toggleClass("fa-times", isVisible);
    });

});

function setupModal(modalId, btnId, closeClass) {
    var modal = document.getElementById(modalId);
    var btn = document.getElementById(btnId);
    var span = document.getElementsByClassName(closeClass)[0];

    if (modal && btn && span) { // ตรวจสอบว่าทุกตัวแปรถูกกำหนดค่า
        btn.onclick = function() {
            modal.style.display = "block";
        }

        span.onclick = function() {
            modal.style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    } else {
        console.error('One of the modal variables, btn or span, cannot be found in the DOM.');
    }
}


const buildTabSidebar = () => {

    let sidebarItems = [];

    // Fetch sidebar data via AJAX
    $.ajax({
        url: basePath + 'actions/check_sidebar.php',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            
            sidebarItems = response;

            // Check if sidebarItems is valid
            if (!Array.isArray(sidebarItems) || sidebarItems.length === 0) {
                $('#showTabSidebar').html('<p>No sidebar items found.</p>');
                return;
            }

            // Generate and display the sidebar content
            let sidebarContent = '<div class="sidebar">';


            sidebarContent += `
            <div>
                <span style="margin: 0 10px;">
                    <label class="toggleSwitch nolabel">
                        <input type="checkbox" id="theme-toggle"/>
                        <span>
                            <span><i class="fas fa-sun"></i></span>
                            <span><i class="fas fa-moon"></i></span>
                        </span>
                        <a></a>
                    </label>
                </span>
            </div>
            `;


            sidebarItems.sort((a, b) => a.order - b.order).forEach(item => {
                const itemLink = item.link || '#';
                const itemToggleClass = `toggle-${item.id}`;

                sidebarContent += `<a href="${itemLink}" class="sidebar-link ${itemToggleClass}" data-href="${itemLink}">${item.icon} ${item.label}</a>`;

                if (item.subItems && item.subItems.length > 0) {
                    sidebarContent += `<div class="sub-sidebar ${itemToggleClass}" style="display:none;">`;

                    item.subItems.sort((a, b) => a.order - b.order).forEach(subItem => {
                        const subItemLink = subItem.link || '#';
                        sidebarContent += `<a href="${subItemLink}" class="sub-sidebar-link" data-parent="${subItem.parentId}">${subItem.icon} ${subItem.label}</a>`;
                    });

                    sidebarContent += '</div>';
                }
            });

            sidebarContent += '</div>';

            $('#showTabSidebar').html(sidebarContent);

            // Event delegation for handling click events
            $('#showTabSidebar').on('click', '.sidebar-link', function(event) {
                const itemLink = $(this).data('href'); // ดึงค่า itemLink จาก data attribute
                const targetClass = $(this).attr('class').split(' ').find(cls => cls.startsWith('toggle-'));
                const isActive = $(this).hasClass('active');
                const $subSidebar = $(`.${targetClass}`);

                if (itemLink === '#') {
                    // ถ้า itemLink ไม่มีค่า (เป็น '#') ให้เปิด/ปิด sub-sidebar
                    event.preventDefault(); // ป้องกันการนำทาง

                    // ปิด sub-sidebar อื่น ๆ และลบคลาส active
                    $('.sub-sidebar').slideUp(300);
                    $('.sidebar-link').removeClass('active');

                    // เปิด sub-sidebar ที่ถูกคลิก
                    if (!isActive) {
                        $subSidebar.slideDown(300);
                        $(this).addClass('active');
                    }
                } else {
                    // ถ้ามี itemLink ให้ดำเนินการลิงค์ตามปกติ
                    window.location.href = itemLink;
                }
            });

            const $toggleSwitch = $('#theme-toggle');
            const isNightMode = localStorage.getItem('night-mode') === 'true';
        
            if (isNightMode) {
                $('body').addClass('night-mode');
                $toggleSwitch.prop('checked', true);
            }
        
            $toggleSwitch.change(function() {
                if ($(this).is(':checked')) {
                    $('body').addClass('night-mode');
                    localStorage.setItem('night-mode', 'true');
                } else {
                    $('body').removeClass('night-mode');
                    localStorage.setItem('night-mode', 'false');
                }
            });


        },
        error: function(xhr, status, error) {
            console.error('เกิดข้อผิดพลาด:', error);
        }
    });
};










