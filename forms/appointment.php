<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';  // Correct path (adjust if needed)

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Get inputs from form
    $name        = htmlspecialchars($_POST['name']);
    $email       = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $phone       = htmlspecialchars($_POST['phone']);
    $tour        = htmlspecialchars($_POST['department']);
    $date        = htmlspecialchars($_POST['date']);
    $guests      = intval($_POST['guests']);
    $messageText = htmlspecialchars($_POST['message']);

    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host = 'smtp.hostinger.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'info@mirissariverkayaktours.com';
        $mail->Password = 'MirissaKayakTours@123';
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->SMTPOptions = [
            'ssl' => [
                'verify_peer'       => false,
                'verify_peer_name'  => false,
                'allow_self_signed' => true
            ]
        ];

        // Email details
        $mail->setFrom('info@mirissariverkayaktours.com', 'Kayak Tours Mirissa');
        $mail->addAddress('visualvibegraphicslk@gmail.com');
        $mail->addAddress('info@mirissariverkayaktours.com');
        $mail->addReplyTo($email, $name);

        $mail->Subject = "New Inquiry From $name – Kayak Tours Mirissa";

        // Load template
        $emailBody = file_get_contents('../email_template.html');

        // Replace placeholders
        $search = [
            '{{name}}', '{{email}}', '{{phone}}',
            '{{tour}}', '{{date}}', '{{guests}}', '{{message}}'
        ];

        $replace = [
            $name, $email, $phone, $tour, $date, $guests, nl2br($messageText)
        ];

        $emailBody = str_replace($search, $replace, $emailBody);

        $mail->isHTML(true);
        $mail->Body = $emailBody;

        if ($mail->send()) {
            http_response_code(200);
            echo "Message sent successfully.";
        } else {
            http_response_code(500);
            echo "Email sending failed.";
        }

    } catch (Exception $e) {
        http_response_code(500);
        echo "Mailer Error: " . $mail->ErrorInfo;
    }

} else {
    http_response_code(405);
    echo "Invalid request.";
}
?>
