<?php
// Load .env file if exists
if (file_exists(__DIR__ . '/../../.env')) {
    $envFile = __DIR__ . '/../../.env';
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        if (!getenv($key)) {
            putenv("$key=$value");
        }
    }
}

// PHPMailer v5 style files exist in /smtp; include legacy classes safely
// Support both namespaced (v6) and legacy (v5) loaders for flexibility
if (file_exists(__DIR__ . '/../../smtp/class.phpmailer.php')) {
    require_once __DIR__ . '/../../smtp/class.phpmailer.php';
    require_once __DIR__ . '/../../smtp/class.smtp.php';
    require_once __DIR__ . '/../../smtp/class.pop3.php';
} elseif (file_exists(__DIR__ . '/../../smtp/PHPMailerAutoload.php')) {
    require_once __DIR__ . '/../../smtp/PHPMailerAutoload.php';
}

function sendMail($toEmail, $subject, $htmlBody, $toName = '') {
    $host = getenv('SMTP_HOST') ?: 'smtp.gmail.com';
    $username = getenv('SMTP_USERNAME') ?: '';
    $password = getenv('SMTP_PASSWORD') ?: '';
    $port = (int)(getenv('SMTP_PORT') ?: 587);
    $secure = getenv('SMTP_SECURE') ?: 'tls';
    $fromEmail = getenv('SMTP_FROM') ?: $username;
    $fromName = getenv('SMTP_FROM_NAME') ?: 'FIT TRACK HUB';

    if (!$username || !$password) {
        return [false, 'SMTP not configured'];
    }

    // Instantiate PHPMailer (v5 compatible)
    $mail = new PHPMailer();
    $mail->isSMTP();
    $mail->Host = $host;
    $mail->SMTPAuth = true;
    $mail->Username = $username;
    $mail->Password = $password;
    $mail->Port = $port;
    $mail->SMTPSecure = $secure;
    $mail->CharSet = 'UTF-8';
    $mail->setFrom($fromEmail, $fromName);
    $mail->addAddress($toEmail, $toName ?: $toEmail);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $htmlBody;

    try {
        if (!$mail->send()) {
            return [false, 'Mailer error: ' . $mail->ErrorInfo];
        }
        return [true, 'OK'];
    } catch (Exception $e) {
        return [false, 'Mailer exception: ' . $e->getMessage()];
    }
}
?>


