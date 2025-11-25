<?php

namespace Trekly\User\Application;

use Trekly\User\Domain\Rating\CreatorRating;
use Trekly\User\Domain\Rating\CreatorRatingRepository;
use Trekly\User\Domain\Profile\UserProfileRepository;

class RateCreatorUseCase
{
    public function __construct(
        private CreatorRatingRepository $ratingRepository,
        private UserProfileRepository $profileRepository
    ) {}

    public function execute(string $memberId, string $creatorId, string $projectId, int $ratingValue, ?string $comment): void
    {
        // 1. Check if rating already exists
        $existingRating = $this->ratingRepository->findByMemberAndProject($memberId, $projectId);
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
