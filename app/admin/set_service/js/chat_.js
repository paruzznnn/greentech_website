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

    $('#online-users li').each(function() {
        var userName = $(this).data('user');

        var messageContainer = $('<div>', {
            id: 'message-container-' + userName,
            class: 'message-container',
            style: 'display: none;'
        });

        var sampleMessages = [
            { sender: 'To: ' + userName, text: 'ทดสอบ for ' + userName, timestamp: new Date().toISOString() },
            { sender: 'To: ' + userName, text: 'สวัสดี from ' + userName, timestamp: new Date().toISOString() }
        ];

        $.each(sampleMessages, function(index, message) {
            var messageElement = $('<div>', { class: 'message sent' })
                .append($('<div>', { class: 'sender', text: message.sender }))
                .append($('<div>', { class: 'text', text: message.text }))
                .append($('<div>', { class: 'timestamp', text: message.timestamp }));
            messageContainer.append(messageElement);
        });

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
        let $container = $('#message-container-' + recipient);
        if ($container.length === 0) {
            $container = $('<div>', {
                id: 'message-container-' + recipient,
                class: 'message-container'
            }).appendTo('#message-container-placeholder');
        }
    
        var timestamp = new Date().toISOString();
    
        const $messageElement = $('<div>', {
            class: 'message ' + (isSent ? 'sent' : 'received')
        });
    
        const $senderElement = $('<div>', {
            class: 'sender',
            text: isSent ? 'To: ' + recipient : 'From: ' + sender
        });
    
        const $textElement = $('<div>', {
            class: 'text',
            text: message
        });
    
        const $timestampElement = $('<div>', {
            class: 'timestamp',
            text: timestamp
        });
    
        $messageElement.append($senderElement).append($textElement).append($timestampElement);
        
        // Append the new message above the input field
        $container.find('.input-group').before($messageElement);

        // Ensure that the container scrolls to the bottom (if needed)
        $container.scrollTop($container[0].scrollHeight);
    }

    $('#message-container-placeholder').on('click', 'button', function() {
        const userName = $(this).attr('id').split('-')[2];
        const message = $('#message-input-' + userName).val().trim();

        if (message && currentRecipient) {
            const chatMessage = {
                message: message,
                sender: 'Me',
                recipient: currentRecipient,
                isSent: true
            };

            addMessageToChat(message, 'Me', currentRecipient, true);
            conn.send(JSON.stringify(chatMessage));
            $('#message-input-' + userName).val('');
        }
    });

    $('#online-users li').on('click', function() {
        currentRecipient = $(this).data('user');

        $('.message-container').hide();
        $('#message-container-' + currentRecipient).show();

        $('#message-input-' + currentRecipient).focus();
    });
});
