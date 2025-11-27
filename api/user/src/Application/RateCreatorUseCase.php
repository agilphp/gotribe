<?php

namespace Trekly\User\Application;

use Trekly\User\Domain\Rating\CreatorRating;
use Trekly\User\Domain\Rating\CreatorRatingRepository;
use Trekly\User\Domain\Profile\UserProfileRepository;

class RateCreatorUseCase
{
    // Agrega los repositorios necesarios para la validación
    public function __construct(
        private CreatorRatingRepository $ratingRepository,
        private UserProfileRepository $profileRepository,
        private \Trekly\User\Domain\Project\ProjectRepository $projectRepository,
        private \Trekly\User\Domain\Participation\ParticipationRepository $participationRepository
    ) {}

    public function execute(string $memberId, string $creatorId, string $projectId, int $ratingValue, ?string $comment): void
    {
        // Debug logging
        error_log("RateCreatorUseCase - memberId: {$memberId}");
        error_log("RateCreatorUseCase - creatorId: {$creatorId}");
        error_log("RateCreatorUseCase - projectId: {$projectId}");
        error_log("RateCreatorUseCase - ratingValue: {$ratingValue}");

        // 0. Validación de reglas de rating
        $project = $this->projectRepository->findById($projectId);
        if (!$project) {
            throw new \Exception('Project not found.');
        }

        $participation = $this->participationRepository->findByProjectAndUser($projectId, $memberId);
        if (!$participation) {
            throw new \Exception('You are not a participant in this project.');
        }

        if ($project->getPrice() == 0) {
            // Gratis: solo debe estar unido
            if (!in_array($participation->getStatus()->value, ['REQUESTED', 'CONFIRMED', 'ATTENDED'])) {
                throw new \Exception('You must join the event before rating.');
            }
        } else {
            // De pago: debe estar marcado como ATTENDED
            if ($participation->getStatus()->value !== 'ATTENDED') {
                throw new \Exception('Your attendance must be confirmed before rating.');
            }
        }

        // 1. Check if rating already exists
        $existingRating = $this->ratingRepository->findByMemberAndProject($memberId, $projectId);

        error_log("RateCreatorUseCase - existingRating: " . ($existingRating ? json_encode([
            'id' => $existingRating->getId(),
            'creatorId' => $existingRating->getCreatorId(),
            'memberId' => $existingRating->getMemberId(),
            'projectId' => $existingRating->getProjectId()
        ]) : 'null'));

        if ($existingRating) {
            throw new \Exception('You have already rated this creator for this project.');
        }

        // 2. Create and save new rating
        $rating = new CreatorRating(
            uniqid('', true),
            $creatorId,
            $memberId,
            $projectId,
            $ratingValue,
            $comment
        );
        $this->ratingRepository->save($rating);

        // 3. Update creator profile stats
        $average = $this->ratingRepository->getAverageRating($creatorId);
        $count = $this->ratingRepository->countRatings($creatorId);

        $profile = $this->profileRepository->findByUserId($creatorId);
        if ($profile) {
            $profile->updateRatingStats($average, $count);
            $this->profileRepository->save($profile);
        }
    }
}
