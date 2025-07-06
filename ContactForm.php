<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $input = json_decode(file_get_contents('php://input'), true);

  $name    = $input['name']    ?? '';
  $email   = $input['email']   ?? '';
  $subject = $input['subject'] ?? '';
  $message = $input['message'] ?? '';

  if (empty($name) || empty($email) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit;
  }

  $mail = new PHPMailer(true);

  try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'tugbamohammed.cct@gmail.com';
    $mail->Password   = 'pdpiscokznfezppk'; // Use your correct App Password here
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    $mail->setFrom('tugbamohammed.cct@gmail.com', 'Portfolio Contact');
    $mail->addAddress('tugbamohammed.cct@gmail.com');

    $mail->isHTML(true);
    $mail->Subject = "New Contact Message: " . $subject;

    $htmlTemplate = file_get_contents('ContactFormMailing.html');
    if ($htmlTemplate === false) {
      throw new Exception("Could not load HTML template.");
    }

    $htmlBody = str_replace(
      ['{{from_name}}', '{{from_email}}', '{{project_type}}', '{{message}}', '{{current_time}}'],
      [$name, $email, $subject, nl2br($message), date('Y-m-d H:i:s')],
      $htmlTemplate
    );

    error_log("Email Body: " . $htmlBody);
    $mail->Body = $htmlBody;

    $mail->send();
    echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);

  } catch (Exception $e) {
    error_log("Mailer Error: " . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => 'Mailer Error: ' . $mail->ErrorInfo]);
  }
} else {
  echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>

<?php
// Alternative using PHPMailer (if you prefer)
// composer require phpmailer/phpmailer

/*
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendEmailWithPHPMailer($name, $email, $subject, $message) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'your-email@gmail.com';
        $mail->Password   = 'your-app-password';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('your-email@gmail.com', 'Portfolio Contact');
        $mail->addAddress('your-email@gmail.com');
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Portfolio Contact: ' . $subject;
        $mail->Body    = "
        <h2>New Portfolio Contact</h2>
        <p><strong>Name:</strong> $name</p>
        <p><strong>Email:</strong> $email</p>
        <p><strong>Subject:</strong> $subject</p>
        <p><strong>Message:</strong></p>
        <p>" . nl2br($message) . "</p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
*/
?>
