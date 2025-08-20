$(document).ready(function() {

    setupModal("myModal-setup-language", "myBtn-setup-language", "modal-close-setup-language");
    getLanuage('US');

    $.getJSON("../../../api/languages/nation.json" + '?' + new Date().getTime(), function(data) {

        let nationalities = data.nationalities;
        let $select = $('#language_name');
        $select.empty();
    
        $.each(nationalities, function(key, entry) {
            // สร้าง <option> พร้อมรูปธงชาติ
            let option = $('<option></option>')
                .attr('value', entry.abbreviation)
                .attr('data-flag', entry.flag)  // เก็บ URL ธงชาติใน data-flag
                .text(entry.name);
            
            $select.append(option);
        });
    
        if (nationalities.length > 0) {
            $select.select2({
                templateResult: formatState,  // เรียกใช้ function สำหรับแสดงรูปธงชาติ
                templateSelection: formatState // เรียกใช้เมื่อเลือก option
            });
        } else {
            console.error("ไม่มีข้อมูลสำหรับสร้าง options");
        }
    });

    $.getJSON("../../../api/languages/nation.json" + '?' + new Date().getTime(), function(data) {

        let nationalities = data.nationalities;
        let $select = $('#filter_lang');
        $select.empty();
    
        $.each(nationalities, function(key, entry) {
            // สร้าง <option> พร้อมรูปธงชาติ
            let option = $('<option></option>')
                .attr('value', entry.abbreviation)
                .attr('data-flag', entry.flag)  // เก็บ URL ธงชาติใน data-flag
                .text(entry.name);
            
            $select.append(option);
        });
    
        if (nationalities.length > 0) {
            $select.select2({
                templateResult: formatState,  // เรียกใช้ function สำหรับแสดงรูปธงชาติ
                templateSelection: formatState // เรียกใช้เมื่อเลือก option
            });
        } else {
            console.error("ไม่มีข้อมูลสำหรับสร้าง options");
        }
    });
    
    // ฟังก์ชันสำหรับแสดงรูปธงชาติใน dropdown
    function formatState(state) {
        if (!state.id) {
            return state.text;
        }
    
        var flagUrl = $(state.element).data('flag'); // ดึงค่า data-flag
        var $state = $(
            '<span><img src="' + flagUrl + '" class="img-flag" style="width:20px; margin-right: 10px;" /> ' + state.text + '</span>'
        );
        return $state;
    }
    
    

});

    $('#filter_lang').on('change', function(e){
        var $islang = $(this).val();
        getLanuage($islang);
    });

    const getLanuage = ($islang) => {

        $.ajax({
            url: 'actions/get_language.php',
            type: 'GET',
            data:{
                isLang: $islang
            },
            dataType: 'json',
            success: function(data) {
                var lang = $islang || 'en';  // Use || instead of ??
                var tableBody = $('#dataTable tbody');
                tableBody.empty();
            
                if (data[lang]) {
                    $.each(data[lang], function(key, value) {
                        var countRow = tableBody.children().length + 1;
            
                        var row = `
                        <tr data-language-key="${key}" data-language-word="${value}" data-language-name="${lang}">
                            <td><input type="text" id="language_key_${countRow}" class="form-control" name="language_key_${countRow}" value="${key}" readonly></td>
                            <td><input type="text" id="language_word_${countRow}" class="form-control" name="language_word_${countRow}" value="${value}" readonly></td>
                            <td>
                                <input type="hidden" id="language_name_${countRow}" name="language_name_${countRow}" value="${lang}">
                                <div>
                                    <div>
                                        <button type="button" class="save-btn save-circle save-lang" style="display:none;"><i class="fas fa-save"></i></button>
                                    </div>
                                    <div>
                                        <button type="button" class="edit-btn edit-circle edit-lang"><i class="fas fa-pencil-alt"></i></button>
                                    </div>
                                    <div>
                                        <button type="button" class="remove-btn remove-circle del-lang"><i class="fas fa-trash-alt"></i></button>
                                    </div>
                                    <div>
                                        <button type="button" class="remove-btn remove-circle cancel-lang" style="display:none;"><i class="fas fa-times"></i></button>
                                    </div>
                                </div>
                            </td>
                        </tr>`;
                        tableBody.append(row);
                    });
            
                    // Bind edit button click event to show inputs and save/cancel buttons
                    $('.edit-lang').off('click').on('click', function() {
                        var row = $(this).closest('tr');
                        row.find('input').prop('readonly', false);  // Enable inputs
                        row.find('.save-lang').show();               // Show save button
                        row.find('.cancel-lang').show();             // Show cancel button
                        row.find('.edit-lang').hide();               // Hide edit button
                        row.find('.del-lang').hide();                // Hide delete button
                    });
            
                    // Bind save button click event to send data to the server
                    $('.save-lang').off('click').on('click', function() {
                        var row = $(this).closest('tr');
                        var languageKey = row.find('input[name^="language_key"]').val();
                        var languageWord = row.find('input[name^="language_word"]').val();
                        var languageName = row.data('language-name');

                        Swal.fire({
                            title: "Are you sure?",
                            text: "Do you want to save the data!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#4caf50",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes"
                        }).then((result) => {
                            if (result.isConfirmed) {

                              // Send data to the server
                                $.ajax({
                                    url: 'actions/process_setup_language.php',
                                    type: 'POST',
                                    data: {
                                        action: 'update',
                                        language_name: languageName,
                                        language_key: languageKey,
                                        language_word: languageWord
                                    },
                                    success: function(response) {

                                        const Toast = Swal.mixin({
                                            toast: true,
                                            position: "top-end",
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true,
                                            didOpen: (toast) => {
                                                toast.onmouseenter = Swal.stopTimer;
                                                toast.onmouseleave = Swal.resumeTimer;
                                            }
                                        });
                                        Toast.fire({
                                            icon: "success",
                                            title: "Save in successfully"
                                        });
                                        getLanuage(lang);

                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error: ' + error);
                                    }
                                });

                            }

                        });
            
                        // After saving, set inputs back to readonly and show/hide appropriate buttons
                        row.find('input').prop('readonly', true);
                        row.find('.save-lang').hide();
                        row.find('.cancel-lang').hide();
                        row.find('.edit-lang').show();
                        row.find('.del-lang').show();
                    });
            
                    // Bind cancel button click event to revert changes
                    $('.cancel-lang').off('click').on('click', function() {
                        var row = $(this).closest('tr');
                        var languageKey = row.data('language-key');
                        var languageWord = row.data('language-word');
            
                        // Revert changes
                        row.find('input[name^="language_key"]').val(languageKey).prop('readonly', true);
                        row.find('input[name^="language_word"]').val(languageWord).prop('readonly', true);
            
                        // Hide save and cancel buttons, show edit and delete buttons
                        row.find('.save-lang').hide();
                        row.find('.cancel-lang').hide();
                        row.find('.edit-lang').show();
                        row.find('.del-lang').show();
                    });
            
                    // Bind delete button click event to send data to the server
                    $('.del-lang').off('click').on('click', function() {
                        var row = $(this).closest('tr');
                        var languageKey = row.find('input[name^="language_key"]').val();
                        var languageWord = row.find('input[name^="language_word"]').val();
                        var languageName = row.data('language-name');

                        Swal.fire({
                            title: "Are you sure?",
                            text: "Do you want to delete the data!",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#4caf50",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Yes"
                        }).then((result) => {
                            if (result.isConfirmed) {
            
                                // Send data to the server
                                $.ajax({
                                    url: 'actions/process_setup_language.php',
                                    type: 'POST',
                                    data: {
                                        action: 'delete',
                                        language_name: languageName,
                                        language_key: languageKey,
                                        language_word: languageWord
                                    },
                                    success: function(response) {

                                        const Toast = Swal.mixin({
                                            toast: true,
                                            position: "top-end",
                                            showConfirmButton: false,
                                            timer: 2000,
                                            timerProgressBar: true,
                                            didOpen: (toast) => {
                                                toast.onmouseenter = Swal.stopTimer;
                                                toast.onmouseleave = Swal.resumeTimer;
                                            }
                                        });
                                        Toast.fire({
                                            icon: "success",
                                            title: "Delete  in successfully"
                                        });
                                        getLanuage(lang);
                                    },
                                    error: function(xhr, status, error) {
                                        console.error('Error: ' + error);
                                    }
                                });

                            }

                        });

                    });
            
                } else {
                    tableBody.html('<tr><td colspan="4">No data available</td></tr>');  // Adjusted colspan to 4
                }
            },            
            error: function() {
                $('#dataTable tbody').html('<tr><td colspan="2">Error loading data</td></tr>');
            }
        });

    };

    $('#formSetLanguage').submit(function(event) {
        event.preventDefault();

        var formData = $(this).serialize();

        $.ajax({
        url: 'actions/process_setup_language.php', // ไฟล์ PHP
        type: 'POST',
        data: formData,
        success: function(response) {

            $('#response').html(response); // แสดงผลลัพธ์
            location.reload();
        },
        error: function() {
            $('#response').html('Error saving data');
        }
        });
    });
