<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/vendor/autoload.php';  // FIXED PATH

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name    = htmlspecialchars($_POST['name']) ?? '';
    $email   = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone   = htmlspecialchars($_POST['phone']) ?? '';
    $tour    = htmlspecialchars($_POST['department']) ?? '';
    $date    = htmlspecialchars($_POST['date']) ?? '';
    $guests  = intval($_POST['guests']) ?? 0;
    $note = htmlspecialchars($_POST['message']) ?? '';

    // Configure PHPMailer
    $mail = new PHPMailer(true);

    try {
        // SMTP Settings for Hostinger
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@kayakmirissa.com'; 
        $mail->Password = 'KayakMirissa@123'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;  // Use constant instead of string
        $mail->Port = 587;

        // Remove SMTPOptions or use minimal settings
        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false,
                'allow_self_signed' => true
            ]
        ];

        // From & To
        $mail->setFrom('info@kayakmirissa.com', 'Mirissa Kayak Tours');
        $mail->addAddress('visualvibegraphicslk@gmail.com');
        $mail->addAddress('info@kayakmirissa.com');
        $mail->addReplyTo($email, $name);

        // Email subject
        $mail->Subject = "New Inquiry From Customer $name";

        // Load email template
        $emailBody = file_get_contents(__DIR__ . '/email_template.html');

        // Replace placeholders with actual form data
        $emailBody = str_replace('{{name}}', $name, $emailBody);
        $emailBody = str_replace('{{email}}', $email, $emailBody);
        $emailBody = str_replace('{{phone}}', $phone, $emailBody);
        $emailBody = str_replace('{{tour}}', $tour, $emailBody); 
        $emailBody = str_replace('{{guests}}', $guests, $emailBody);
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
        echo "Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    http_response_code(405);
    echo "Method not allowed";
}
