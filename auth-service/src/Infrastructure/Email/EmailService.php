<?php

namespace Trekly\Auth\Infrastructure\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private string $smtpHost;
    private int $smtpPort;
    private string $smtpUsername;
    private string $smtpPassword;
    private string $fromEmail;
    private string $fromName;

    public function __construct()
    {
        $this->smtpHost = $_ENV['SMTP_HOST'] ?? 'mail.webcol.net';
        $this->smtpPort = (int)($_ENV['SMTP_PORT'] ?? 465);
        $this->smtpUsername = $_ENV['SMTP_USERNAME'] ?? 'runpal@webcol.net';
        $this->smtpPassword = $_ENV['SMTP_PASSWORD'] ?? 'fc$2=wK#26F8uX3I';
        $this->fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? 'runpal@webcol.net';
        $this->fromName = $_ENV['SMTP_FROM_NAME'] ?? 'TREKLY';
    }

    public function sendWelcomeEmail(string $email, string $password): bool
    {
        $subject = "Welcome to TREKLY!";
        $body = $this->getWelcomeEmailTemplate($email, $password);
        
        return $this->send($email, $subject, $body);
    }

    private function send(string $toEmail, string $subject, string $body): bool
    {
        error_log("EmailService - Attempting to send email to: {$toEmail}");
        
        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $this->smtpPort;

            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail);

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            error_log("EmailService - Email sent successfully to {$toEmail}");
            return true;
        } catch (Exception $e) {
            error_log("EmailService - Email sending failed: {$mail->ErrorInfo}");
            return false;
        }
    }

    private function getWelcomeEmailTemplate(string $email, string $password): string
    {
        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #4CAF50; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .details { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #4CAF50; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Welcome to TREKLY! 🚀</h1>
        </div>
        <div class="content">
            <p>Hi there,</p>
            <p>Welcome to TREKLY! Your account has been successfully created.</p>
            
            <div class="details">
                <h2>Your Login Credentials</h2>
                <p><strong>Email:</strong> {$email}</p>
                <p><strong>Password:</strong> {$password}</p>
            </div>
            
            <p>Please keep your credentials safe. We recommend changing your password after your first login.</p>
            
            <p>Ready to start your adventure? <a href="http://localhost:4200/auth/login">Login here</a></p>
        </div>
        <div class="footer">
            <p>TREKLY - Adventure Awaits</p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
