<?php

namespace Trekly\User\Application;

use Trekly\User\Domain\Rating\CreatorRatingRepository;

class GetCreatorRatingsUseCase
{
    public function __construct(
        private CreatorRatingRepository $ratingRepository
    ) {}

    public function execute(string $creatorId): array
    {
        return $this->ratingRepository->findByCreatorId($creatorId);
    }
}
