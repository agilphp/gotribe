<?php

namespace Trekly\Participation\Infrastructure\Email;

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
        $this->smtpHost = $_ENV['SMTP_HOST'] ?? 'mail.gotribe.co';
        $this->smtpPort = (int)($_ENV['SMTP_PORT'] ?? 465);
        $this->smtpUsername = $_ENV['SMTP_USERNAME'] ?? 'tribe@gotribe.co';
        $this->smtpPassword = $_ENV['SMTP_PASSWORD'] ?? '7tak5K1rWyacJ42O';
        $this->fromEmail = $_ENV['SMTP_FROM_EMAIL'] ?? 'tribe@gotribe.co';
        $this->fromName = $_ENV['SMTP_FROM_NAME'] ?? 'GoTribe';
    }

    public function sendParticipationConfirmation(
        string $participantEmail,
        string $participantName,
        array $project,
        ?string $pdfContent = null,
        ?string $pdfFilename = null
    ): bool {
        $subject = "You've joined: {$project['title']}";
        $body = $this->getParticipantEmailTemplate($participantName, $project);
        
        return $this->send($participantEmail, $participantName, $subject, $body, $pdfContent, $pdfFilename);
    }

    public function sendNewParticipantNotification(
        string $creatorEmail,
        string $creatorName,
        string $participantName,
        array $project
    ): bool {
        $subject = "New participant joined your adventure: {$project['title']}";
        $body = $this->getCreatorEmailTemplate($creatorName, $participantName, $project);
        
        return $this->send($creatorEmail, $creatorName, $subject, $body);
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
        error_log("EmailService - Attempting to send email to: {$toEmail}");
        error_log("EmailService - Subject: {$subject}");
        
        $mail = new PHPMailer(true);

        try {
            // Server settings
            error_log("EmailService - Configuring SMTP...");
            $mail->isSMTP();
            $mail->Host = $this->smtpHost;
            $mail->SMTPAuth = true;
            $mail->Username = $this->smtpUsername;
            $mail->Password = $this->smtpPassword;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port = $this->smtpPort;

            // Recipients
            $mail->setFrom($this->fromEmail, $this->fromName);
            $mail->addAddress($toEmail, $toName);

            // Attachments
            if ($attachmentContent && $attachmentName) {
                $mail->addStringAttachment($attachmentContent, $attachmentName);
            }

            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->AltBody = strip_tags($body);

            error_log("EmailService - Sending email...");
            $mail->send();
            error_log("EmailService - Email sent successfully to {$toEmail}");
            return true;
        } catch (Exception $e) {
            error_log("EmailService - Email sending failed: {$mail->ErrorInfo}");
            error_log("EmailService - Exception: " . $e->getMessage());
            return false;
        }
    }

    private function getParticipantEmailTemplate(string $participantName, array $project): string
    {
        $title = htmlspecialchars($project['title']);
        $activityType = htmlspecialchars($project['activityType']);
        $startDate = date('F j, Y \a\t g:i A', strtotime($project['startDateTime']));
        $meetingPoint = htmlspecialchars($project['meetingPoint'] ?? 'TBD');
        $price = number_format($project['price'], 2);
        $currency = htmlspecialchars($project['currency']);

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
            <h1>🎉 You're In!</h1>
        </div>
        <div class="content">
            <p>Hi {$participantName},</p>
            <p>Great news! You've successfully joined the adventure:</p>
            
            <div class="details">
                <h2>{$title}</h2>
                <p><strong>Activity:</strong> {$activityType}</p>
                <p><strong>Date & Time:</strong> {$startDate}</p>
                <p><strong>Meeting Point:</strong> {$meetingPoint}</p>
                <p><strong>Price:</strong> {$price} {$currency}</p>
            </div>
            
            <p>We're excited to have you join us! The creator will be in touch with more details soon.</p>
            <p><strong>Important:</strong> Please arrive on time at the meeting point.</p>
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

    private function getCreatorEmailTemplate(string $creatorName, string $participantName, array $project): string
    {
        $title = htmlspecialchars($project['title']);
        $activityType = htmlspecialchars($project['activityType']);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #2196F3; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .participant { background: white; padding: 15px; margin: 15px 0; border-left: 4px solid #2196F3; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎊 New Participant!</h1>
        </div>
        <div class="content">
            <p>Hi {$creatorName},</p>
            <p>Good news! Someone just joined your adventure:</p>
            
            <div class="participant">
                <h2>{$title}</h2>
                <p><strong>Activity:</strong> {$activityType}</p>
                <p><strong>New Participant:</strong> {$participantName}</p>
            </div>
            
            <p>Your adventure is getting more exciting! Make sure to prepare everything for a great experience.</p>
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
