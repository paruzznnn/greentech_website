
$(document).ready(function() {

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


const buildTabSidebar = () => {

    let sidebarItems = [];

    // Fetch sidebar data via AJAX
    $.ajax({
        url: window.base_path_admin +'actions/check_sidebar.php',
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
                const itemLink = $(this).data('href');
                const targetClass = $(this).attr('class').split(' ').find(cls => cls.startsWith('toggle-'));
                const isActive = $(this).hasClass('active');
                const $subSidebar = $(`.${targetClass}`);

                if (itemLink === '#') {
                    event.preventDefault();

                    $('.sub-sidebar').slideUp(300);
                    $('.sidebar-link').removeClass('active');

                    if (!isActive) {
                        $subSidebar.slideDown(300);
                        $(this).addClass('active');
                    }
                } else {
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

function cssResponsiveTable(tableId, headers) {

    const style = document.createElement('style');
    style.type = 'text/css';
    let cssContent = `
        @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
            #${tableId}, #${tableId} thead, #${tableId} tbody, #${tableId} th, #${tableId} td, #${tableId} tr {
                display: block;
            }

            #${tableId} thead tr {
                position: absolute;
                top: -9999px;
                left: -9999px;
            }

            #${tableId} tr {
                margin: 0 0 1rem 0;
            }

            #${tableId} td {
                border: none;
                border-bottom: 1px solid #eee;
                position: relative;
                padding-left: 50%;
            }

            #${tableId} td:before {
                position: absolute;
                top: 0;
                left: 6px;
                width: 45%;
                padding-right: 10px;
                white-space: nowrap;
            }
    `;

    headers.forEach((header, index) => {
        cssContent += `
            #${tableId} td:nth-of-type(${index + 1}):before { content: "${header}"; font-weight: 700; }
        `;
    });

    cssContent += ` }`;

    style.innerHTML = cssContent;
    document.head.appendChild(style);

}








