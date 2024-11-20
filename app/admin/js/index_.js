
/****nationLanguages**** */

function nationLanguages() {
    $.getJSON(window.base_path + 'api/languages/nation.json' + '?' + new Date().getTime(), function (data) {
        let nationalities = data.nationalities;
        let $select = $('#language-select');
        $select.empty();

        $.each(nationalities, function (index, entry) {
            let option = $('<option></option>')
                .attr('value', entry.abbreviation)
                .attr('data-flag', entry.flag)
                .text(entry.name);

            $select.append(option);
        });

        if (nationalities.length > 0) {
            $select.val(nationalities[0].abbreviation);
            updateSelectedLanguageFlag();
        }
    });
}


function updateSelectedLanguageFlag() {
    let selectedOption = $('#language-select option:selected');
    let flagUrl = selectedOption.data('flag');

    if (flagUrl) {
        $('#language-select').css({
            'background-image': 'url(' + flagUrl + ')',
            'background-repeat': 'no-repeat',
            'background-position': 'left 8px center',
            'background-size': '20px 15px',
            'padding-left': '30px'
        });
    }
}

function changeLanguage(lang) {
    fetch(window.base_path + 'api/languages/' + lang + '.json')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            document.querySelectorAll("[data-translate][lang]").forEach(el => {
                const key = el.getAttribute("data-translate");

                el.textContent = data[key] || el.textContent;
                // Update the lang attribute to the selected language
                el.setAttribute('lang', lang);

            });
        })
        .catch(error => console.error('Error loading language file:', error));
}

/****nationLanguages**** */

// sidebarContent += `
// <div>
//     <span style="margin: 0 10px;">
//         <label class="toggleSwitch nolabel">
//             <input type="checkbox" id="theme-toggle"/>
//             <span>
//                 <span><i class="fas fa-sun"></i></span>
//                 <span><i class="fas fa-moon"></i></span>
//             </span>
//             <a></a>
//         </label>
//     </span>
// </div>
// `;

const buildTabSidebar = () => {

    let sidebarItems = [];

    $.ajax({
        url: window.base_path_admin + 'actions/check_sidebar.php',
        type: 'POST',
        dataType: 'json',
        success: function (response) {

            sidebarItems = response;

            if (!Array.isArray(sidebarItems) || sidebarItems.length === 0) {
                $('#showTabSidebar').html('<p>No sidebar items found.</p>');
                return;
            }
            
            let sidebarContent = '<div class="sidebar">';
            let appsAdded = false; // ตัวแปรสถานะ
            
            sidebarItems.sort((a, b) => a.order - b.order).forEach(item => {
                const itemLink = item.link || '#';
                const itemToggleClass = `toggle-${item.id}`;
                const level = item.level || 0;
            
                if (level == 1) {
                    // เพิ่ม APPS เฉพาะครั้งแรกที่เจอ level == 1
                    if (!appsAdded) {
                        sidebarContent += `
                        <div style="padding: 10px 15px; font-size: 12px; color: #5555;">
                            <i class="fas fa-rocket"></i> APPS
                        </div>`;
                        appsAdded = true; // ตั้งค่าสถานะว่าเพิ่มแล้ว
                    }
            
                    sidebarContent += `<a href="${itemLink}" class="sidebar-link ${itemToggleClass}" data-href="${itemLink}">
                    <span style="font-size: 14px;">${item.icon}</span>
                    ${item.label}
                    </a>`;
            
                } else {
                    sidebarContent += `<a href="${itemLink}" class="sidebar-link ${itemToggleClass}" data-href="${itemLink}">
                    <span style="font-size: 14px;">${item.icon}</span> 
                    ${item.label}
                    </a>`;
                }
            
                if (item.subItems && item.subItems.length > 0) {
                    sidebarContent += `<div class="sub-sidebar ${itemToggleClass}" style="display:none;">`;
            
                    item.subItems.sort((a, b) => a.order - b.order).forEach(subItem => {
                        const subItemLink = subItem.link || '#';
                        sidebarContent += `<a href="${subItemLink}" class="sub-sidebar-link" data-parent="${subItem.parentId}">
                        <span style="font-size: 12px;">${subItem.icon}</span> 
                        ${subItem.label}
                        </a>`;
                    });
            
                    sidebarContent += '</div>';
                }
            });
            
            sidebarContent += `
            <a href="${window.base_path_admin}logout.php" class="sidebar-link" data-href="">
                <i class="fas fa-sign-out-alt"></i>
                <span>log out</span>
            </a>`;
            
            sidebarContent += '</div>';
            
            $('#showTabSidebar').html(sidebarContent);
            

            $('#showTabSidebar').on('click', '.sidebar-link', function (event) {
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

            $toggleSwitch.change(function () {
                if ($(this).is(':checked')) {
                    $('body').addClass('night-mode');
                    localStorage.setItem('night-mode', 'true');
                } else {
                    $('body').removeClass('night-mode');
                    localStorage.setItem('night-mode', 'false');
                }
            });


        },
        error: function (xhr, status, error) {
            console.error('เกิดข้อผิดพลาด:', error);
        }
    });

};

// function cssResponsiveTable(tableId, headers) {

//     const style = document.createElement('style');
//     style.type = 'text/css';
//     let cssContent = `
//         @media only screen and (max-width: 760px), (min-device-width: 768px) and (max-device-width: 1024px) {
//             #${tableId}, #${tableId} thead, #${tableId} tbody, #${tableId} th, #${tableId} td, #${tableId} tr {
//                 display: block;
//             }

//             #${tableId} thead tr {
//                 position: absolute;
//                 top: -9999px;
//                 left: -9999px;
//             }

//             #${tableId} tr {
//                 margin: 0 0 1rem 0;
//             }

//             #${tableId} td {
//                 border: none;
//                 border-bottom: 1px solid #eee;
//                 position: relative;
//                 padding-left: 50%;
//             }

//             #${tableId} td:before {
//                 position: absolute;
//                 top: 0;
//                 left: 6px;
//                 width: 45%;
//                 padding-right: 10px;
//                 white-space: nowrap;
//             }
//     `;

//     headers.forEach((header, index) => {
//         cssContent += `
//             #${tableId} td:nth-of-type(${index + 1}):before { content: "${header}"; font-weight: 700; }
//         `;
//     });

//     cssContent += ` }`;

//     style.innerHTML = cssContent;
//     document.head.appendChild(style);

// }



$(document).ready(function () {

    $('#loading-overlay').fadeIn();
    $('#loading-overlay').fadeOut();

    buildTabSidebar();

    var $sidebar = $('#showTabSidebar');
    $sidebar.hide();

    $(".toggle-button").on("click", function () {

        $sidebar.toggle();

        var iconSidebar = $("#toggleIcon");

        var isVisible = $sidebar.is(":visible");
        iconSidebar.toggleClass("fa-bars", !isVisible);
        iconSidebar.toggleClass("fa-times", isVisible);
    });

    $(document).on('click', function (event) {
        if (!$(event.target).closest('#showTabSidebar').length && !$(event.target).closest('.toggle-button').length) {
            $sidebar.hide();
            $('#toggleIcon').removeClass('fa-times').addClass('fa-bars');
        }
    });


    nationLanguages();
    const selectedLanguage = localStorage.getItem('language') || 'th';
    changeLanguage(selectedLanguage);

    $('#language-select').on('change', function () {
        const selectedLang = $(this).val().toLowerCase();
        changeLanguage(selectedLang);
        updateSelectedLanguageFlag();
    });


    $('.dropdown-btn').on('click', function (event) {
        event.stopPropagation();
        $('.dropdown-content').toggle();

        const icon = $(this).find('i');
        if ($('.dropdown-content').is(':visible')) {
            icon.removeClass('fa-caret-up').addClass('fa-caret-down');
        } else {
            icon.removeClass('fa-caret-down').addClass('fa-caret-up');
        }
    });

    $(document).on('click', function (event) {
        if (!$(event.target).closest('.dropdown-content').length && !$(event.target).is('.dropdown-btn')) {
            $('.dropdown-content').hide();

            $('.dropdown-btn').find('i').removeClass('fa-caret-down').addClass('fa-caret-up');
        }
    });



});




