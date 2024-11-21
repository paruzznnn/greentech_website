<?php
require __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require  __DIR__ . '/../vendor/phpmailer/PHPMailer/src/Exception.php';
require  __DIR__ . '/../vendor/phpmailer/PHPMailer/src/PHPMailer.php';
require  __DIR__ . '/../vendor/phpmailer/PHPMailer/src/SMTP.php';

require_once(__DIR__ . '/../lib/base_directory.php');


function sendEmail($to, $type_mes, $id, $otp)
{

    $mail = new PHPMailer(true);

    try {
        //Server settings
        // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      
        $mail->isSMTP();
        $mail->SMTPAuth   = true;

        $mail->Host       = 'smtp.gmail.com';
        $mail->Username   = 'allhiapp.info@gmail.com';
        $mail->Password   = 'neynwxnyhmnchvbk';

        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            
        // $mail->Port       = 587;    

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;      // Enable SSL encryption
        $mail->Port       = 465;                              // TCP port to connect to

        //Recipients
        $mail->setFrom('allhiapp.info@gmail.com', 'Allable');
        $mail->addAddress($to);

        //Content
        $mail->isHTML(true);
        $mail->Subject = mssageSubject($type_mes);
        $mail->Body    = messageBody($type_mes, $id, $otp);

        $mail->send();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

function mssageSubject($subject)
{

    $HTMLsj = '';

    switch ($subject) {
        case 'register':
            $HTMLsj = 'Sign Up Allable';
            break;
        case 'forgot':
            $HTMLsj = 'Reset your password';
            break;
        case 'new_password':
            $HTMLsj = 'New password';
            break;
        default:

            break;
    }

    return $HTMLsj;
}

function messageBody($body, $id, $otp)
{
    global $base_path;
    // global $base_path_admin;

    // Generate a random string for the 'gen' parameter
    $random_string = generateUrl(8);
    $type_tmp = '';
    $url = '';

    if ($body == 'register') {
        $type_tmp = 'register';
        $url = $base_path . 'app/otp_confirm.php?otpID=' . urlencode($id) . '&' . urlencode($random_string) . '&' . urlencode('register');
    } else if ($body == 'forgot') {
        $type_tmp = 'forgot';
        $url = $base_path . 'app/otp_confirm.php?otpID=' . urlencode($id) . '&' . urlencode($random_string) . '&' . urlencode('forgot');
    } else if ($body == 'new_password') {
        $type_tmp = 'new_password';
    }

    $HTMLbd = templateMail($url, $type_tmp, $otp);
    return $HTMLbd;
}


function generateUrl($length)
{
    $characters = '!@#$%^&*()_+1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}


function templateMail($url, $type_tmp, $otp)
{

    switch ($type_tmp) {
        case 'register':
            $mesMail = '<html>
                    <head>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f9f9f9;
                                color: #333;
                                padding: 20px;
                            }
                            .email-container {
                                background-color: #fff;
                                border: 1px solid #ddd;
                                padding: 20px;
                                border-radius: 5px;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                            }
                            .email-container p {
                                font-size: 16px;
                                line-height: 1.5;
                            }
                            .email-container a {
                                display: inline-block;
                                margin-top: 10px;
                                padding: 10px 20px;
                                background-color: #007bff;
                                color: #fff;
                                text-decoration: none;
                                border-radius: 5px;
                            }
                            .email-container a:hover {
                                background-color: #0056b3;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="email-container">
                            <p>Your verification code is</p>
                            <h1>' . $otp . '</h1>
                            <a href="' . $url . '" target="_blank">OTP</a>
                            <p>Best regards,<br/>The Allable Team</p>
                        </div>
                    </body>
                </html>';
            return $mesMail;
            break;
        case 'forgot':

            $mesMail = '<html>
                    <head>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f9f9f9;
                                color: #333;
                                padding: 20px;
                            }
                            .email-container {
                                background-color: #fff;
                                border: 1px solid #ddd;
                                padding: 20px;
                                border-radius: 5px;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                            }
                            .email-container p {
                                font-size: 16px;
                                line-height: 1.5;
                            }
                            .email-container a {
                                display: inline-block;
                                margin-top: 10px;
                                padding: 10px 20px;
                                background-color: #007bff;
                                color: #fff;
                                text-decoration: none;
                                border-radius: 5px;
                            }
                            .email-container a:hover {
                                background-color: #0056b3;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="email-container">
                            <p>Your verification code is</p>
                            <h1>' . $otp . '</h1>
                            <a href="' . $url . '" target="_blank">Reset Password</a>
                            <p>Best regards,<br/>The Allable Team</p>
                        </div>
                    </body>
                </html>';
            return $mesMail;

            break;
        case 'new_password':

            $mesMail = '<html>
                    <head>
                        <style>
                            body {
                                font-family: Arial, sans-serif;
                                background-color: #f9f9f9;
                                color: #333;
                                padding: 20px;
                            }
                            .email-container {
                                background-color: #fff;
                                border: 1px solid #ddd;
                                padding: 20px;
                                border-radius: 5px;
                                box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                            }
                            .email-container p {
                                font-size: 16px;
                                line-height: 1.5;
                            }
                            .email-container a {
                                display: inline-block;
                                margin-top: 10px;
                                padding: 10px 20px;
                                background-color: #007bff;
                                color: #fff;
                                text-decoration: none;
                                border-radius: 5px;
                            }
                            .email-container a:hover {
                                background-color: #0056b3;
                            }
                        </style>
                    </head>
                    <body>
                        <div class="email-container">
                            <p>Your new password</p>
                            <h1>' . $otp . '</h1>
                            <p>Best regards,<br/>The Allable Team</p>
                        </div>
                    </body>
                </html>';
            return $mesMail;

            break;
        default:
            $mesMail = '';
            break;
    }
}




// data: 'action=change_category_project&comp_id='+comp_id+'&type='+type+'&project_id='+project_id,
// $mail->addAddress('ellen@example.com');    
// $mail->addReplyTo('info@example.com', 'Information');
// $mail->addCC('cc@example.com');
// $mail->addBCC('bcc@example.com');

//Attachments
// $mail->addAttachment('/var/tmp/file.tar.gz');       
// $mail->addAttachment('/tmp/image.jpg', 'new.jpg'); 
// $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';
