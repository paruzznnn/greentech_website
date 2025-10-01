<?php include '../check_permission.php' ?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title></title>

    <link rel="icon" type="image/x-icon" href="https://www.trandar.com//public/news_img/%E0%B8%94%E0%B8%B5%E0%B9%84%E0%B8%8B%E0%B8%99%E0%B9%8C%E0%B8%97%E0%B8%B5%E0%B9%88%E0%B8%A2%E0%B8%B1%E0%B8%87%E0%B9%84%E0%B8%A1%E0%B9%88%E0%B9%84%E0%B8%94%E0%B9%89%E0%B8%95%E0%B8%B1%E0%B9%89%E0%B8%87%E0%B8%8A%E0%B8%B7%E0%B9%88%E0%B8%AD_5.png">

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
            height: 100vh;
            overflow: hidden;
            flex-direction: row;
        }

        .chat-list {
            width: 250px;
            padding: 20px;
            background: #f1f1f1;
            border-right: 2px solid #ccc;
            overflow-y: auto;
            flex-shrink: 0;
        }

        .chat-box {
            flex: 1;
            display: flex;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }

        .message-group {
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: #fff;
            overflow-y: auto;
            margin-bottom: 20px;
            flex-grow: 1;
        }

        .message-container {
            display: none;
            position: relative;
            padding: 10px;
            background: #fff;
            flex-grow: 1;
            margin-bottom: 20px;
            overflow-y: auto;
        }

        .input-group {
            display: flex;
            align-items: center;
            margin-top: 10px;
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
            font-size: 14px;
        }

        .input-group button {
            padding: 10px 15px;
            border-radius: 5px;
            border: none;
            background: #4CAF50;
            color: white;
            cursor: pointer;
            font-size: 14px;
        }

        .input-group button:hover {
            background: #45a049;
        }

        #online-users {
            list-style: none;
            padding: 0;
        }

        #online-users li {
            padding: 5px 0;
            cursor: pointer;
        }

        #online-users li:hover {
            background: #f1f1f1;
        }

        .message {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            font-size: 14px;
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


        @media (max-width: 768px) {
            .chat-container {
                flex-direction: column;
            }

            .chat-list {
                width: 100%;
                border-right: none;
                border-bottom: 2px solid #ccc;
            }

            .chat-box {
                width: 100%;
            }

            .message-group {
                max-height: 400px;
            }

            .input-group input[type="text"] {
                font-size: 12px;
            }

            .input-group button {
                font-size: 12px;
            }

            .message {
                font-size: 12px;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .chat-container {
                flex-direction: column;
            }

            .chat-list {
                width: 50%;
            }

            .chat-box {
                width: 50%;
            }

            .message-group {
                max-height: 450px;
            }

            .input-group input[type="text"] {
                font-size: 13px;
            }

            .input-group button {
                font-size: 13px;
            }

            .message {
                font-size: 13px;
            }
        }

        @media (min-width: 1025px) {
            .chat-container {
                flex-direction: row;
            }

            .chat-list {
                width: 250px;
            }

            .chat-box {
                width: calc(100% - 250px);
            }

            .message-group {
                max-height: 500px;
            }

            .input-group input[type="text"] {
                font-size: 14px;
            }

            .input-group button {
                font-size: 14px;
            }

            .message {
                font-size: 14px;
            }
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

                        <div class="chat-list">
                            <ul id="online-users">
                            </ul>
                        </div>

                        <div class="chat-box">
                            <div id="message-container-placeholder"></div>
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