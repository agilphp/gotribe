<?php
namespace Trekly\Ratings\Domain\Rating;

interface RatingRepository
{
    public function getCreatorAverageRating(string $creatorId): ?float;
}
