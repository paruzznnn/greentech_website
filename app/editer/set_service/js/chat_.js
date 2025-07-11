const usersArray = {
    "1": "John",
    "2": "Jane",
    "3": "Mike",
    "4": "Anna",
    "5": "Bob"
};

$(document).ready(function() {
    var conn = new WebSocket('ws://localhost:8080');
    var currentRecipient = null;

    conn.onopen = function (e) {
        console.log("Connection established!");
    };

    conn.onmessage = function (e) {
        const data = JSON.parse(e.data);

        if (data.recipient === currentRecipient) {
            addMessageToChat(data.message, data.sender, data.recipient, false);
        }
    };

    $.each(usersArray, function(key, value) {
        $('#online-users').append('<li data-user="' + key + '">' + value + '</li>');
    });

    $('#online-users li').each(function() {
        var userName = $(this).data('user');
    
        var messageContainer = $('<div>', {
            id: 'message-container-' + userName,
            class: 'message-container',
            style: 'display: none;'
        });
    
        var messageGroup = $('<div>', { class: 'message-group' });
    
        var sampleMessages = [
            { sender: 'To: ' + userName, text: 'ทดสอบ for ' + userName, timestamp: new Date().toISOString() },
            { sender: 'To: ' + userName, text: 'สวัสดี from ' + userName, timestamp: new Date().toISOString() }
        ];
    
        $.each(sampleMessages, function(index, message) {
            var messageElement = $('<div>', { class: 'message sent' })
                .append($('<div>', { class: 'sender', text: message.sender }))
                .append($('<div>', { class: 'text', text: message.text }))
                .append($('<div>', { class: 'timestamp', text: message.timestamp }));
            messageGroup.append(messageElement);
        });
    
        messageContainer.append(messageGroup);
    
        var inputGroup = $('<div>', { class: 'input-group' })
            .append($('<input>', {
                type: 'text',
                id: 'message-input-' + userName,
                placeholder: 'Type your message here...'
            }))
            .append($('<button>', {
                id: 'send-button-' + userName,
                text: 'Send'
            }));
    
        messageContainer.append(inputGroup);
    
        $('#message-container-placeholder').append(messageContainer);
    });
    
    function addMessageToChat(message, sender, recipient, isSent) {
        // ค้นหา message-container สำหรับ recipient
        let $container = $('#message-container-' + recipient);
        if ($container.length === 0) {
            // ถ้ายังไม่มี message-container สร้างใหม่
            $container = $('<div>', {
                id: 'message-container-' + recipient,
                class: 'message-container',
                style: 'display: block;' // สามารถปรับ style ให้แสดงได้ทันที
            }).appendTo('#message-container-placeholder');
            
            // สร้าง message-group สำหรับข้อความ
            var $messageGroup = $('<div>', { class: 'message-group' });
            $container.append($messageGroup);
    
            // สร้าง input-group สำหรับกล่องข้อความใหม่
            var $inputGroup = $('<div>', { class: 'input-group' })
                .append($('<input>', {
                    type: 'text',
                    id: 'message-input-' + recipient,
                    placeholder: 'Type your message here...'
                }))
                .append($('<button>', {
                    id: 'send-button-' + recipient,
                    text: 'Send'
                }));
    
            // เพิ่ม input-group ลงไปใน container
            $container.append($inputGroup);
        }
    
        // เวลาของข้อความที่ส่ง
        var timestamp = new Date().toISOString();
    
        // สร้างข้อความใหม่
        const $messageElement = $('<div>', {
            class: 'message ' + (isSent ? 'sent' : 'received')
        });
    
        // สร้างส่วนของ sender
        const $senderElement = $('<div>', {
            class: 'sender',
            text: isSent ? 'To: ' + recipient : 'From: ' + sender
        });
    
        // สร้างส่วนของข้อความ
        const $textElement = $('<div>', {
            class: 'text',
            text: message
        });
    
        // สร้างส่วนของ timestamp
        const $timestampElement = $('<div>', {
            class: 'timestamp',
            text: timestamp
        });
    
        // เพิ่ม sender, text และ timestamp ลงใน messageElement
        $messageElement.append($senderElement).append($textElement).append($timestampElement);
    
        // ค้นหา message-group และเพิ่มข้อความใหม่เข้าไป
        $container.find('.message-group').append($messageElement);
    
        // ทำให้ scroll ไปที่ข้อความล่าสุด
        $container.scrollTop($container[0].scrollHeight);
    }
    

    // function addMessageToChat(message, sender, recipient, isSent) {
    //     let $container = $('#message-container-' + recipient);
    //     if ($container.length === 0) {
    //         $container = $('<div>', {
    //             id: 'message-container-' + recipient,
    //             class: 'message-container'
    //         }).appendTo('#message-container-placeholder');
    //     }
    
    //     var timestamp = new Date().toISOString();
    
    //     const $messageElement = $('<div>', {
    //         class: 'message ' + (isSent ? 'sent' : 'received')
    //     });
    
    //     const $senderElement = $('<div>', {
    //         class: 'sender',
    //         text: isSent ? 'To: ' + recipient : 'From: ' + sender
    //     });
    
    //     const $textElement = $('<div>', {
    //         class: 'text',
    //         text: message
    //     });
    
    //     const $timestampElement = $('<div>', {
    //         class: 'timestamp',
    //         text: timestamp
    //     });
    
    //     $messageElement.append($senderElement).append($textElement).append($timestampElement);
        
    //     // Append the new message above the input field
    //     $container.find('.input-group').before($messageElement);

    //     // Ensure that the container scrolls to the bottom (if needed)
    //     $container.scrollTop($container[0].scrollHeight);
    // }

    $('#message-container-placeholder').on('click', 'button', function() {
        const userName = $(this).attr('id').split('-')[2];
        const message = $('#message-input-' + userName).val().trim();

        if (message && currentRecipient) {

            const chatMessage = {
                message: message,
                sender: userName,
                recipient: currentRecipient,
                isSent: true
            };

            addMessageToChat(message, 'Me', currentRecipient, true);
            conn.send(JSON.stringify(chatMessage));
            $('#message-input-' + userName).val('');

            // $.ajax({
            //     url: 'actions/process_service.php',
            //     type: 'POST',
            //     data: {
            //         action: 'getSender'
            //     },
            //     dataType: 'json',
            //     success: function (response) {

            //         if (response.status == 'success') {

            //             const chatMessage = {
            //                 message: message,
            //                 sender: response.data.user_id,
            //                 recipient: currentRecipient,
            //                 isSent: true
            //             };

            //             addMessageToChat(message, 'Me', currentRecipient, true);
            //             conn.send(JSON.stringify(chatMessage));
            //             $('#message-input-' + userName).val('');
                    
            //         }

            //     },
            //     error: function (xhr, status, error) {
            //         console.error('Error:', error);
            //     }
            // });

        }
    });

    $('#online-users li').on('click', function() {
        currentRecipient = $(this).data('user');

        $('.message-container').hide();
        $('#message-container-' + currentRecipient).show();

        $('#message-input-' + currentRecipient).focus();
    });
});

// var sendArray = null;
// var receiverArray = null;

// async function fetchDataChat() {

//     try {

//         const senderResponse = await fetchSender();
//         if (senderResponse.status === 'success') {
//             sendArray = senderResponse.data;
//         } else {
//             console.error('Error fetching menus:', senderResponse.message);
//             return;
//         }

//         const receiverResponse = await fetchReceiver();
//         if (receiverResponse.status === 'success') {
//             receiverArray = receiverResponse.data;
//         } else {
//             console.error('Error fetching roles:', receiverResponse.message);
//             return;
//         }
        

//     } catch (error) {
//         console.error('เกิดข้อผิดพลาดในการดึงข้อมูล:', error);
//     }
// }

// async function fetchSender() {
//     const response = await $.ajax({
//         url: 'actions/process_service.php',
//         type: 'POST',
//         data: { action: 'getSender' },
//         dataType: 'json'
//     });
//     return response;
// }

// async function fetchReceiver() {
//     const response = await $.ajax({
//         url: 'actions/process_service.php',
//         type: 'POST',
//         data: { action: 'getReceiver' },
//         dataType: 'json'
//     });
//     return response;
// }

// $(document).ready(function() {

//     fetchDataChat();

//     $.each(receiverArray, function(key, value) {
//         $('#online-users').append('<li data-user="' + key + '">' + value + '</li>');
//     });

//     console.log('senderResponse', sendArray);
//     console.log('receiverResponse', receiverArray);


// });