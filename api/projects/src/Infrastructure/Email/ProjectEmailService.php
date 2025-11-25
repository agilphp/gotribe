<?php

namespace Trekly\Project\Infrastructure\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class ProjectEmailService
{
    private string $smtpHost;
    private int $smtpPort;
    private string $smtpUsername;
    private string $smtpPassword;
    private string $fromEmail;
    private string $fromName;

    public function __construct()
    {
        $this->smtpHost = $_ENV['SMTP_HOST'] ?? 'mail.gotribe.co';
        $this->smtpPort = (int)($_ENV['SMTP_PORT'] ?? 465);
        $this->smtpUsername = $_ENV['SMTP_USERNAME'] ?? 'tribe@gotribe.co';
        $this->smtpPassword = $_ENV['SMTP_PASSWORD'] ?? '7tak5K1rWyacJ42O';
        $this->fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? 'tribe@gotribe.co';
        $this->fromName = $_ENV['SMTP_FROM_NAME'] ?? 'GoTribe';
    }

    public function sendCreatorQR(
        string $creatorEmail,
        string $creatorName,
        array $project,
        string $pdfContent
    ): bool {
        $subject = "Your Validation QR Code - {$project['title']}";
        $body = $this->getCreatorQREmailTemplate($creatorName, $project);
        
        return $this->send(
            $creatorEmail, 
            $creatorName, 
            $subject, 
            $body,
            $pdfContent,
            'GoTribe_Creator_QR.pdf'
        );
    }

    private function send(
        string $toEmail, 
        string $toName, 
        string $subject, 
        string $body,
        ?string $attachmentContent = null,
        ?string $attachmentName = null
    ): bool
    {
        error_log("ProjectEmailService - Sending to: {$toEmail}");
        
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $this->smtpPort;

            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);

            if ($attachmentContent && $attachmentName) {
                $mail->addStringAttachment($attachmentContent, $attachmentName);
            }

            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);

            $mail->send();
            error_log("ProjectEmailService - Email sent successfully");
            return true;
        } catch (Exception $e) {
            error_log("ProjectEmailService - Failed: {$mail->ErrorInfo}");
            return false;
        }
    }

    private function getCreatorQREmailTemplate(string $creatorName, array $project): string
    {
        $title = htmlspecialchars($project['title']);
        $price = number_format($project['price'], 0);
        $currency = htmlspecialchars($project['currency']);
        $maxGuests = $project['maxGuests'] ?? 10;

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2196F3; color: white; padding: 20px; text-align: center; border-radius: 5px; }
        .content { background: #f9f9f9; padding: 20px; margin-top: 20px; border-radius: 5px; }
        .project-info { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #2196F3; }
        .instructions { background: #e3f2fd; padding: 15px; margin: 15px 0; border-radius: 5px; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .highlight { color: #2196F3; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎉 Your Project is Published!</h1>
        </div>
        <div class="content">
            <p>Hi {$creatorName},</p>
            <p>Great news! Your adventure is now live on GoTribe:</p>
            
            <div class="project-info">
                <h2>{$title}</h2>
                <p><strong>Price:</strong> {$price} {$currency} per person</p>
                <p><strong>Capacity:</strong> {$maxGuests} participants</p>
            </div>
            
            <div class="instructions">
                <h3>📱 Your Validation QR Code</h3>
                <p>Attached is your <strong>Creator QR Code</strong>. Here's how to use it:</p>
                <ol>
                    <li><strong>Print the PDF</strong> or save it on your phone</li>
                    <li><strong>At the meeting point:</strong> Show your QR to members who have paid</li>
                    <li><strong>Members scan your QR</strong> to validate their payment</li>
                    <li><strong>System automatically:</strong>
                        <ul>
                            <li>Confirms their attendance</li>
                            <li>Calculates your 10% commission to GoTribe</li>
                            <li>Tracks all payments</li>
                        </ul>
                    </li>
                </ol>
            </div>
            
            <p><strong>Remember:</strong> Members pay you directly at the meeting point. The system tracks these payments when you validate their QR codes.</p>
            
            <p class="highlight">You'll owe GoTribe 10% of the total collected, payable within 30 days after the event.</p>
        </div>
        <div class="footer">
            <p>GoTribe - Adventure Management Platform</p>
            <p>This is an automated message, please do not reply.</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
