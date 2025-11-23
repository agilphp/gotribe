<?php

namespace Trekly\Participation\Infrastructure\Services;

use Dompdf\Dompdf;
use Dompdf\Options;
use Endroid\QrCode\Builder\Builder;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\Label\LabelAlignment;
use Endroid\QrCode\Label\Font\NotoSans;
use Endroid\QrCode\RoundBlockSizeMode;
use Endroid\QrCode\Writer\PngWriter;

class TicketService
{
    public function generateTicketPdf(array $participation, array $project, array $participant): string
    {
        // 1. Generate QR Code
        $qrContent = json_encode([
            'participationId' => $participation['id'],
            'userId' => $participation['userId'],
            'projectId' => $participation['projectId'],
            'action' => 'validate_attendance'
        ]);

        $result = (new Builder(
            writer: new PngWriter(),
            writerOptions: [],
            data: $qrContent,
            encoding: new Encoding('UTF-8'),
            errorCorrectionLevel: ErrorCorrectionLevel::High,
            size: 300,
            margin: 10,
            roundBlockSizeMode: RoundBlockSizeMode::Margin
        ))->build();

        $qrDataUri = $result->getDataUri();

        // 2. Generate HTML
        $html = $this->getTicketHtml($participation, $project, $participant, $qrDataUri);

        // 3. Generate PDF
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }

    private function getTicketHtml(array $participation, array $project, array $participant, string $qrDataUri): string
    {
        $title = htmlspecialchars($project['title']);
        $date = date('F j, Y \a\t g:i A', strtotime($project['startDateTime']));
        $location = htmlspecialchars($project['meetingPoint'] ?? 'TBD');
        $participantName = htmlspecialchars($participant['email']); // Using email as name for now
        $ticketId = $participation['id'];

        return <<<HTML
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: 'Helvetica', sans-serif; color: #333; }
        .ticket { border: 2px solid #4CAF50; padding: 20px; max-width: 700px; margin: 0 auto; }
        .header { text-align: center; border-bottom: 2px solid #4CAF50; padding-bottom: 20px; margin-bottom: 20px; }
        .logo { font-size: 24px; font-weight: bold; color: #4CAF50; }
        .content { display: table; width: 100%; }
        .info { display: table-cell; width: 60%; vertical-align: top; }
        .qr { display: table-cell; width: 40%; text-align: center; vertical-align: top; }
        .label { font-weight: bold; color: #666; font-size: 12px; text-transform: uppercase; margin-top: 10px; }
        .value { font-size: 16px; margin-bottom: 10px; }
        .footer { margin-top: 20px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <div class="logo">TREKLY ADVENTURE TICKET</div>
            <div>Admit One</div>
        </div>
        
        <div class="content">
            <div class="info">
                <div class="label">Adventure</div>
                <div class="value">{$title}</div>
                
                <div class="label">Date & Time</div>
                <div class="value">{$date}</div>
                
                <div class="label">Meeting Point</div>
                <div class="value">{$location}</div>
                
                <div class="label">Participant</div>
                <div class="value">{$participantName}</div>
                
                <div class="label">Ticket ID</div>
                <div class="value" style="font-family: monospace;">{$ticketId}</div>
            </div>
            
            <div class="qr">
                <img src="{$qrDataUri}" alt="QR Code" style="width: 200px;">
                <div style="margin-top: 10px; font-size: 10px;">Scan to Validate</div>
            </div>
        </div>
        
        <div class="footer">
            Present this ticket to the creator upon arrival.
        </div>
    </div>
</body>
</html>
HTML;
    }
}
