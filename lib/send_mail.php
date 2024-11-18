<?php
// เรียกใช้ autoload.php ของ Composer
require 'vendor/autoload.php';

// นำเข้า namespace ของ PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// สร้าง instance ของ PHPMailer
$mail = new PHPMailer(true);

try {
    
    $mail->isSMTP();                                
    $mail->Host = 'smtp.example.com';                     
    $mail->SMTPAuth = true;                              
    $mail->Username = 'your_email@example.com';           
    $mail->Password = 'your_password';                    
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;   
    $mail->Port = 587;                                    

    // ตั้งค่าผู้ส่งและผู้รับ
    $mail->setFrom('your_email@example.com', 'Your Name');
    $mail->addAddress('recipient_email@example.com', 'Recipient Name'); // ผู้รับ

    // ตั้งค่าเนื้อหาอีเมล
    $mail->isHTML(true);                                  // ตั้งค่าอีเมลให้ส่งในรูปแบบ HTML
    $mail->Subject = 'This is a test email';
    $mail->Body    = '<b>This is the HTML message body</b>';
    $mail->AltBody = 'This is the plain text version of the email content';

    // ส่งอีเมล
    $mail->send();
    echo 'Message has been sent successfully';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
