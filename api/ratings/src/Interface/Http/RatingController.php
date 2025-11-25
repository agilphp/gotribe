<?php
namespace Trekly\Ratings\Interface\Http;

use Trekly\Ratings\Application\GetCreatorAverageRatingUseCase;

class RatingController
{
    private GetCreatorAverageRatingUseCase $getAverageUseCase;

    public function __construct(GetCreatorAverageRatingUseCase $getAverageUseCase)
    {
        $this->getAverageUseCase = $getAverageUseCase;
    }

    public function getCreatorAverage(string $creatorId): void
    {
        $average = $this->getAverageUseCase->execute($creatorId);
        echo json_encode([
            'creator_id' => $creatorId,
            'average_rating' => $average
        ]);
    }
}
