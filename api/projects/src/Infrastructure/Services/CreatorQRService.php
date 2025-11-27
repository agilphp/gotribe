<?php

namespace Trekly\Project\Infrastructure\Services;

use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;
use Dompdf\Dompdf;
use Dompdf\Options;

class CreatorQRService
{
    /**
     * Generates a QR code for the creator to validate member payments
     */
    public function generateCreatorQR(string $projectId, string $creatorId): string
    {
        $qrContent = json_encode([
            'projectId' => $projectId,
            'creatorId' => $creatorId,
            'action' => 'validate_member_payment'
        ]);

        $result = Builder::create()
            ->writer(new PngWriter())
            ->data($qrContent)
            ->encoding(new Encoding('UTF-8'))
            ->errorCorrectionLevel(new ErrorCorrectionLevelHigh())
            ->size(400)
            ->margin(20)
            ->roundBlockSizeMode(RoundBlockSizeMode::Margin)
            ->build();

        return $result->getDataUri();
    }

    /**
     * Generates a PDF with the creator's QR code
     */
    public function generateCreatorQRPdf(array $project, string $qrDataUri): string
    {
        $html = $this->getCreatorQRHtml($project, $qrDataUri);

        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    private function getCreatorQRHtml(array $project, string $qrDataUri): string
    {
        $title = htmlspecialchars($project['title']);
        $projectId = htmlspecialchars($project['id']);
        $price = number_format($project['price'], 0);
        $currency = htmlspecialchars($project['currency']);

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 0 auto; text-align: center; }
        .header { background: #2196F3; color: white; padding: 30px; border-radius: 10px; margin-bottom: 30px; }
        .title { font-size: 28px; font-weight: bold; margin-bottom: 10px; }
        .subtitle { font-size: 16px; opacity: 0.9; }
        .qr-section { background: #f5f5f5; padding: 40px; border-radius: 10px; margin: 20px 0; }
        .qr-code { margin: 20px 0; }
        .instructions { background: white; padding: 30px; border-radius: 10px; text-align: left; margin-top: 30px; }
        .instruction-item { margin: 15px 0; padding-left: 30px; position: relative; }
        .instruction-item:before { content: "✓"; position: absolute; left: 0; color: #4CAF50; font-weight: bold; font-size: 20px; }
        .price-info { background: #fff3cd; padding: 20px; border-radius: 5px; margin: 20px 0; border-left: 4px solid #ffc107; }
        .footer { margin-top: 30px; padding-top: 20px; border-top: 2px solid #eee; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="title">🎯 Creator Validation QR</div>
            <div class="subtitle">GoTribe Payment Validator</div>
        </div>

        <div class="qr-section">
            <h2>{$title}</h2>
            <p>Project ID: <code>{$projectId}</code></p>
            
            <div class="price-info">
                <strong>Price per member:</strong> {$price} {$currency}
            </div>

            <div class="qr-code">
                <img src="{$qrDataUri}" alt="Creator QR Code" style="width: 400px; height: 400px;">
            </div>
            
            <p style="font-size: 14px; color: #666;">
                Members scan this QR to validate their payment
            </p>
        </div>

        <div class="instructions">
            <h3>📋 How to Use This QR Code</h3>
            
            <div class="instruction-item">
                <strong>Print this page</strong> or save the QR on your phone
            </div>
            
            <div class="instruction-item">
                <strong>At the meeting point:</strong> Show this QR to members who have paid
            </div>
            
            <div class="instruction-item">
                <strong>Members scan your QR</strong> with their phone camera or GoTribe app
            </div>
            
            <div class="instruction-item">
                <strong>System validates</strong> the payment automatically
            </div>
            
            <div class="instruction-item">
                <strong>Your commission</strong> (10%) is calculated and registered
            </div>
        </div>

        <div class="footer">
            <p><strong>Important:</strong> Keep this QR code safe. Anyone with access can validate payments for your project.</p>
            <p>GoTribe - Adventure Management Platform</p>
        </div>
    </div>
</body>
</html>
HTML;
    }
}
