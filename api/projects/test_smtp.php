<?php
/**
 * Test SMTP Configuration for Projects Service
 * 
 * This script tests if the SMTP credentials in .env are working correctly.
 * Run this file to verify email sending before creating projects.
 * 
 * Usage: php test_smtp.php
 */

require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load .env file
if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->load();
    echo "✅ .env file loaded\n\n";
} else {
    echo "❌ ERROR: .env file not found!\n";
    echo "Please create .env file in api/projects/ directory\n";
    exit(1);
}

// Display current configuration
echo "📧 SMTP Configuration:\n";
echo "-----------------------------------\n";
echo "Host: " . ($_ENV['SMTP_HOST'] ?? 'NOT SET') . "\n";
echo "Port: " . ($_ENV['SMTP_PORT'] ?? 'NOT SET') . "\n";
echo "Username: " . ($_ENV['SMTP_USERNAME'] ?? 'NOT SET') . "\n";
echo "Password: " . (isset($_ENV['SMTP_PASSWORD']) ? '***' . substr($_ENV['SMTP_PASSWORD'], -4) : 'NOT SET') . "\n";
echo "From Email: " . ($_ENV['SMTP_FROM_EMAIL'] ?? 'NOT SET') . "\n";
echo "From Name: " . ($_ENV['SMTP_FROM_NAME'] ?? 'NOT SET') . "\n";
echo "-----------------------------------\n\n";

// Prompt for test email address
echo "Enter your email address to receive test email: ";
$testEmail = trim(fgets(STDIN));

if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
    echo "❌ Invalid email address\n";
    exit(1);
}

echo "\n🚀 Sending test email to: {$testEmail}\n\n";

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->SMTPDebug = 2;  // Enable verbose debug output
    $mail->Debugoutput = function($str, $level) {
        echo "DEBUG: {$str}\n";
    };
    
    $mail->isSMTP();
    $mail->Host = $_ENV['SMTP_HOST'] ?? 'mail.gotribe.co';
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['SMTP_USERNAME'] ?? 'tribe@gotribe.co';
    $mail->Password = $_ENV['SMTP_PASSWORD'] ?? '';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
    $mail->Port = (int)($_ENV['SMTP_PORT'] ?? 465);

    // Recipients
    $mail->setFrom(
        $_ENV['SMTP_FROM_EMAIL'] ?? 'tribe@gotribe.co',
        $_ENV['SMTP_FROM_NAME'] ?? 'GoTribe'
    );
    $mail->addAddress($testEmail, 'Test User');

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'GoTribe SMTP Test - ' . date('Y-m-d H:i:s');
    $mail->Body = '
        <html>
        <body style="font-family: Arial, sans-serif; padding: 20px;">
            <h2 style="color: #2196F3;">✅ SMTP Configuration Test</h2>
            <p>If you are reading this email, your SMTP configuration is working correctly!</p>
            <hr>
            <p><strong>Configuration Details:</strong></p>
            <ul>
                <li>SMTP Host: ' . htmlspecialchars($_ENV['SMTP_HOST'] ?? 'N/A') . '</li>
                <li>SMTP Port: ' . htmlspecialchars($_ENV['SMTP_PORT'] ?? 'N/A') . '</li>
                <li>From: ' . htmlspecialchars($_ENV['SMTP_FROM_EMAIL'] ?? 'N/A') . '</li>
            </ul>
            <hr>
            <p style="color: #666; font-size: 12px;">
                GoTribe - Projects Service<br>
                Test executed at: ' . date('Y-m-d H:i:s') . '
            </p>
        </body>
        </html>
    ';
    $mail->AltBody = 'SMTP Test - If you receive this, SMTP is working correctly!';

    $mail->send();
    
    echo "\n\n";
    echo "========================================\n";
    echo "✅ SUCCESS! Email sent successfully!\n";
    echo "========================================\n";
    echo "Check your inbox at: {$testEmail}\n";
    echo "If you received the email, SMTP is configured correctly.\n";
    echo "\n";
    
} catch (Exception $e) {
    echo "\n\n";
    echo "========================================\n";
    echo "❌ ERROR: Email could not be sent\n";
    echo "========================================\n";
    echo "Error: {$mail->ErrorInfo}\n";
    echo "\n";
    echo "Common issues:\n";
    echo "1. Wrong SMTP credentials in .env\n";
    echo "2. SMTP server blocking connections\n";
    echo "3. Firewall blocking SMTP port\n";
    echo "4. Email account doesn't exist\n";
    echo "\n";
    exit(1);
}
