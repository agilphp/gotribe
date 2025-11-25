<?php
namespace Trekly\Ratings\Application;

use Trekly\Ratings\Domain\Rating\RatingRepository;

class GetCreatorAverageRatingUseCase
{
    private RatingRepository $repository;

    public function __construct(RatingRepository $repository)
    {
        $this->repository = $repository;
    }

    public function execute(string $creatorId): ?float
    {
        return $this->repository->getCreatorAverageRating($creatorId);
    }
}
