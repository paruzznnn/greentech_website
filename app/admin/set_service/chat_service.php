<?php include '../check_permission.php' ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <link rel="icon" type="image/x-icon" href="../../../public/img/logo-ALLABLE-07.ico">

    <link href="../../../inc/jquery/css/jquery-ui.css" rel="stylesheet">

    <script src="../../../inc/jquery/js/jquery-3.6.0.min.js"></script>
    <script src="../../../inc/jquery/js/jquery-ui.min.js"></script>

    <link href="../../../inc/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <script src="../../../inc/bootstrap/js/bootstrap.min.js"></script>

    <link href="https://cdn.jsdelivr.net/npm/fontawesome5-fullcss@1.1.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.0/css/all.min.css" integrity="sha512-9xKTRVabjVeZmc+GUW8GgSmcREDunMM+Dt/GrzchfN8tkwHizc5RP4Ok/MXFFy5rIjJjzhndFScTceq5e6GvVQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />


    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&display=swap" rel="stylesheet">

    <link href="../../../inc/sweetalert2/css/sweetalert2.min.css" rel="stylesheet">
    <script src="../../../inc/sweetalert2/js/sweetalert2.all.min.js"></script>

    <link href="../../../inc/select2/css/select2.min.css" rel="stylesheet">
    <script src="../../../inc/select2/js/select2.min.js"></script>

    <link href="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.css" rel="stylesheet">
    <script src="https://cdn.datatables.net/v/dt/dt-2.1.4/datatables.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/css/bootstrap-iconpicker.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-iconpicker/1.10.0/js/bootstrap-iconpicker.bundle.min.js"></script>

    <link href='../css/index_.css?v=<?php echo time(); ?>' rel='stylesheet'>
    <style>

        .chat-container {
            display: flex;
            height: 100vh; /* Full viewport height */
        }

        .chat-list {
            width: 250px;
            padding: 20px;
            background: #f1f1f1;
            border-right: 2px solid #ccc;
            overflow-y: auto; /* Allow scrolling if the list is too long */
        }

        .chat-box {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%; /* Ensure it fills the remaining space */
        }

        .message-container {
            position: relative;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #fff;
            overflow-y: auto; /* Enable scrolling when content overflows */
            flex-grow: 1;
            margin-bottom: 20px;
            max-height: calc(100vh - 140px); /* Make it take up most of the space, adjust for input group and other content */
        }

        .input-group {
            display: flex;
            align-items: center;
            margin-top: 10px; /* Reduced space between messages and input */
            padding: 10px;
            background: #f9f9f9;
            border-top: 1px solid #ccc;
        }

        .input-group input[type="text"] {
            flex: 1;
            padding: 10px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .input-group button {
            padding: 10px 15px;
            border-radius: 5px;
            border: none;
            background: #4CAF50;
            color: white;
            cursor: pointer;
        }

        .input-group button:hover {
            background: #45a049;
        }

        #recipient-select {
            margin-bottom: 10px;
            padding: 10px;
            width: 100%;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }

        .sent {
            text-align: end;
            background: #d9fdd3;
            align-self: flex-end;
        }

        .received {
            text-align: start;
            background: #f0f0f0;
            align-self: flex-start;
        }

        .sender {
            font-weight: bold;
            font-size: 14px;
        }

        .text {
            margin-top: 5px;
        }

        .file-name {
            font-size: 12px;
            color: gray;
        }

        #online-status {
            margin-top: 20px;
        }

        #online-users {
            list-style: none;
            padding: 0;
        }

        #online-users li {
            padding: 5px 0;
            cursor: pointer;
        }



    </style>
</head>

<?php include '../template/header.php' ?>

<body>

    <div class="content-sticky" id="">
        <div class="container-fluid">
            <div class="box-content">
                <div class="row">

                    <div class="chat-container">
                        <!-- Left Sidebar with online users -->
                        <div class="chat-list">
                            <ul id="online-users">
                                <li data-user="John">John</li>
                                <li data-user="Jane">Jane</li>
                                <li data-user="Mike">Mike</li>
                            </ul>
                        </div>

                        <!-- Chat Box -->
                        <div class="chat-box">
                            <div id="message-container-placeholder"></div> <!-- This will be populated dynamically -->
                        </div>
                    </div>


                </div>
            </div>
        </div>
    </div>



    <script src='../js/index_.js?v=<?php echo time(); ?>'></script>
    <script src='js/chat_.js'></script>
</body>

</html>