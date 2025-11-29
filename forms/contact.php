<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get inputs
    $name         = htmlspecialchars($_POST['name']);
    $mobile       = htmlspecialchars($_POST['mobile']);
    $email        = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $country      = htmlspecialchars($_POST['country']);
    $looking_for  = htmlspecialchars($_POST['looking_for']);
    $experience   = htmlspecialchars($_POST['experience']);
    $participants = intval($_POST['participants']);
    $date         = htmlspecialchars($_POST['date']);
    $note         = htmlspecialchars($_POST['note']);

    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@divingmirissa.com';
        $mail->Password = 'divingMirissa@123';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;     
        
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // Email sender and recipients
        $mail->setFrom('info@divingmirissa.com', 'Diving Mirissa');
        $mail->addAddress('Thasheenkavindra@gmail.com');
        $mail->addAddress('info@divingmirissa.com');
        $mail->addReplyTo($email, $name);

        // Email subject
        $mail->Subject = "New Inquiry From Customer $name";

        // Load email template
        $emailBody = file_get_contents('email_template.html');

        // Replace placeholders with actual form data
        $emailBody = str_replace('{{name}}', $name, $emailBody);
        $emailBody = str_replace('{{email}}', $email, $emailBody);
        $emailBody = str_replace('{{phone}}', $mobile, $emailBody);
        $emailBody = str_replace('{{country}}', $country, $emailBody);
        $emailBody = str_replace('{{looking_for}}', $looking_for, $emailBody);
        $emailBody = str_replace('{{experience}}', $experience, $emailBody);
        $emailBody = str_replace('{{participants}}', $participants, $emailBody);
        $emailBody = str_replace('{{date}}', $date, $emailBody);
        $emailBody = str_replace('{{note}}', nl2br($note), $emailBody);

        $mail->isHTML(true);
        $mail->Body = $emailBody;

        if ($mail->send()) {
            http_response_code(200);
            echo 'Message sent successfully';
        } else {
            http_response_code(500); 
            echo 'Message could not be sent.';
        }
            
    } catch (Exception $e) {
        http_response_code(500); 
        echo 'Mailer Error: ' . $mail->ErrorInfo;
    }
    
} else {
    http_response_code(500); 
}
?>
