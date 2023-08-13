<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat App</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .message-row {
            margin-bottom: 10px; /* Add some vertical spacing between messages */
            display: flex; /* Use Flexbox to align messages */
        }

        .sender-message {
            background-color: #cce5ff; /* Customize the background color for sender messages */
            padding: 5px;
            border-radius: 10px; /* Rounded corners for sender messages */
            margin-left: auto; /* Move sender messages to the right */
        }

        .receiver-message {
            background-color: #f0f0f0; /* Customize the background color for receiver messages */
            padding: 5px;
            border-radius: 10px; /* Rounded corners for receiver messages */
            margin-right: auto; /* Move receiver messages to the left */
        }

        .message-content {
            clear: both; /* Clear the float to ensure each message content is displayed correctly */
        }

        .message-content .card-body {
            padding: 0; /* Remove extra padding inside the message card body */
        }
    </style>



</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Chat &emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;&emsp;
                    <a href="{{ route('signout') }}" class="btn btn-primary">Signout</a>
                </div>
                    <div class="card-body">
                         <!-- Place the select dropdown here -->
                         <div class="row searchBox">
                            <div class="col-sm-12 searchBox-inner">
                                <div class="form-group has-feedback">
                                    <select id="recipientSelect" class="form-control" name="recipient">
                                        <option value="">Select a recipient</option>
                                        @foreach ($users as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="chatMessages">
                            <!-- Display chat messages here -->
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="input-group">
                            <input type="text" id="messageInput" class="form-control" placeholder="Type a message...">
                            <div class="input-group-append">
                                <button class="btn btn-primary" id="sendMessageButton">Send</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function(){
        // Function to send a new message
        function sendMessage(message) {
            var recipientId = $('#recipientSelect').val();

            if (recipientId.trim() === '') {
                alert('Please select a recipient before sending the message.');
                return;
            }
            $.ajax({
            url: "{{ route('sendMessage') }}",
            type: "POST",
            data: {
                incoming_msg_id: recipientId,
                text_message: message
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                // Handle success (if needed)
                if (response.success) {
                // Add the sent message to the chat screen
                addMessageToChat({
                    text_message: message,
                });
            }
            },
            error: function(xhr, status, error) {
                // Handle error (if needed)
            }
        });
        }

      function displayMessages(messages) {
        console.log(messages); // Check the content of the messages array
        let chatMessagesDiv = $('#chatMessages');
        chatMessagesDiv.empty();

        // Loop through the messages and display them
        messages.forEach(function (message) {
            let messageText = message.text_message;
            let senderId = message.sender_id;
            let createdAt = message.created_at;

            // Get the current user ID or any identifier to differentiate sender from receiver (You can implement it based on your application's logic)
            let currentUserId = '{{ Auth::user()->id }}'; // Replace this with the actual way to get the current user's ID

            // Determine if the message is from the sender or receiver
            let isSender = senderId === currentUserId;
            let messageClass = isSender ? 'sender-message' : 'receiver-message';

            // Format the timestamp (you can customize the format based on your preferences)
            let formattedTimestamp = new Date(createdAt).toLocaleTimeString();

            // Append the message to the chat screen
            let messageHTML = `
                <div class="message-row">
                    <div class="${isSender ? 'float-right' : 'float-left'} message-content">
                        <div class="card ${messageClass}">
                            <div class="card-body">
                                <p>${messageText}</p>
                                <small>${formattedTimestamp}</small>
                            </div>
                        </div>
                    </div>
                </div>
            `;

            chatMessagesDiv.append(messageHTML);
        });
    }



        // Function to get all messages and display them
        function getMessages() {
            $.ajax({
                url: "{{ route('getMessages') }}",
                type: "POST",
                headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
                success: function(response) {
                    if (response.success) {
                        displayMessages(response.messages);
                    }
                    // Call getMessages() again after a short delay (e.g., 1 second)
                    setTimeout(getMessages, 1000);
                },
                error: function(xhr, status, error) {
                    // Handle error (if needed)
                    // Call getMessages() again after a short delay (e.g., 1 second)
                    setTimeout(getMessages, 1000);
                }
            });
        }

        // Call getMessages() to load existing messages on page load
        getMessages();

        // Handle sending a new message when the 'Send' button is clicked
        $('#sendMessageButton').on('click', function() {
            let message = $('#messageInput').val();
            if (message.trim() !== '') {
                sendMessage(message);
                $('#messageInput').val(''); // Clear the input field after sending
            }
        });

            // Function to add a new message to the chat screen
        function addMessageToChat(message) {
            let chatMessagesDiv = $('#chatMessages');
            let messageText = message.text_message;
            // Customize the message display as needed
            chatMessagesDiv.append('<p>' + messageText + '</p>');
        }

        // Periodically refresh messages (customize the interval as needed)
        setInterval(getMessages, 5000); // Refresh every 5 seconds (for example)
    });
    </script>
</body>
</html>
