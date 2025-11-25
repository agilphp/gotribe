<?php
namespace Trekly\Ratings\Infrastructure\Persistence;

use Trekly\Ratings\Domain\Rating\RatingRepository;
use PDO;

class PdoRatingRepository implements RatingRepository
{
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getCreatorAverageRating(string $creatorId): ?float
    {
        $stmt = $this->pdo->prepare('SELECT AVG(rating) as average FROM creator_ratings WHERE creator_id = ?');
        $stmt->execute([$creatorId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['average'] !== null ? round($result['average'], 2) : null;
    }
}
