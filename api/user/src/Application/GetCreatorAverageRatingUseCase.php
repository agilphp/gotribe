<?php

namespace Trekly\User\Application;

use Trekly\User\Domain\Rating\CreatorRatingRepository;

class GetCreatorAverageRatingUseCase
{
    public function __construct(
        private CreatorRatingRepository $ratingRepository
    ) {}

    public function execute(string $creatorId): array
    {
        $average = $this->ratingRepository->getAverageRating($creatorId);
        $count = $this->ratingRepository->countRatings($creatorId);

        return [
            'averageRating' => $average,
            'totalRatings' => $count
        ];
    }
}
