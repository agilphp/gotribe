<?php

namespace Trekly\Project\Application;

use Trekly\Project\Domain\Project\ActivityType;
use Trekly\Project\Domain\Project\Project;
use Trekly\Project\Domain\Project\ProjectRepository;
use Trekly\Project\Infrastructure\Services\CreatorQRService;
use Trekly\Project\Infrastructure\Email\ProjectEmailService;
use Trekly\Project\Infrastructure\Http\AuthServiceClient;

class CreateProjectUseCase
{
    private ProjectRepository $repository;
    private CreatorQRService $qrService;
    private ProjectEmailService $emailService;
    private AuthServiceClient $authClient;

    public function __construct(
        ProjectRepository $repository,
        ?CreatorQRService $qrService = null,
        ?ProjectEmailService $emailService = null,
        ?AuthServiceClient $authClient = null
    ) {
        $this->repository = $repository;
        $this->qrService = $qrService ?? new CreatorQRService();
        $this->emailService = $emailService ?? new ProjectEmailService();
        $this->authClient = $authClient ?? new AuthServiceClient();
    }

    public function execute(
        string $title,
        string $description,
        string $activityType,
        string $creatorId,
        string $startDateTime,
        string $meetingPoint,
        float $price,
        string $currency,
        ?string $imageUrl = null
    ): Project {
        $project = Project::create(
            $title,
            $description,
            ActivityType::from($activityType),
            $creatorId,
            new \DateTimeImmutable($startDateTime),
            $meetingPoint,
            $price,
            $currency,
            10, // maxGuests - default value
            $imageUrl
        );
        
        // Auto-publish for MVP so it appears in the list immediately
        $project->publish();

        $this->repository->save($project);

        // Generate Creator QR Code (non-blocking)
        try {
            // Check if QR library exists before trying to use it
            if (!class_exists('Endroid\QrCode\Builder\Builder')) {
                error_log("WARNING: QR Library not found. Skipping Creator QR generation.");
                return $project;
            }

            // Only generate QR if project has a price > 0
            if ($project->getPrice() > 0) {
                // Get creator details
                $creator = $this->authClient->getUser($creatorId);
                
                if ($creator && isset($creator['email'])) {
                    // Generate QR code
                    $qrDataUri = $this->qrService->generateCreatorQR(
                        $project->getId(),
                        $creatorId
                    );

                    // Generate PDF with QR
                    $pdfContent = $this->qrService->generateCreatorQRPdf(
                        [
                            'id' => $project->getId(),
                            'title' => $project->getTitle(),
                            'price' => $project->getPrice(),
                            'currency' => $project->getCurrency(),
                            'maxGuests' => $project->getMaxGuests()
                        ],
                        $qrDataUri
                    );

                    // Send email with QR PDF
                    $this->emailService->sendCreatorQR(
                        $creator['email'],
                        $creator['name'] ?? 'Creator',
                        [
                            'title' => $project->getTitle(),
                            'price' => $project->getPrice(),
                            'currency' => $project->getCurrency(),
                            'maxGuests' => $project->getMaxGuests()
                        ],
                        $pdfContent
                    );

                    error_log("Creator QR sent successfully for project: {$project->getId()}");
                }
            }
        } catch (\Throwable $e) {
            // Log error but don't fail the creation
            error_log("Failed to send creator QR: " . $e->getMessage());
        }

        return $project;
    }
}
