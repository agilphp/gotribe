<?php

namespace Trekly\Project\Infrastructure\Persistence;

use Trekly\Project\Domain\Project\ActivityType;
use Trekly\Project\Domain\Project\Project;
use Trekly\Project\Domain\Project\ProjectRepository;

class PDOProjectRepository implements ProjectRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Project $project): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO projects (id, title, description, activity_type, creator_id, start_date_time, meeting_point, price, currency, image_url, is_published, created_at)
            VALUES (:id, :title, :description, :activity_type, :creator_id, :start_date_time, :meeting_point, :price, :currency, :image_url, :is_published, :created_at)
            ON DUPLICATE KEY UPDATE
            title = VALUES(title), description = VALUES(description), activity_type = VALUES(activity_type),
            start_date_time = VALUES(start_date_time), meeting_point = VALUES(meeting_point),
            price = VALUES(price), currency = VALUES(currency), image_url = VALUES(image_url), is_published = VALUES(is_published)
        ");

        $stmt->execute([
            ':id' => $project->getId(),
            ':title' => $project->getTitle(),
            ':description' => $project->getDescription(),
            ':activity_type' => $project->getActivityType()->value,
            ':creator_id' => $project->getCreatorId(),
            ':start_date_time' => $project->getStartDateTime()->format('Y-m-d H:i:s'),
            ':meeting_point' => $project->getMeetingPoint(),
            ':price' => $project->getPrice(),
            ':currency' => $project->getCurrency(),
            ':image_url' => $project->getImageUrl(),
            ':is_published' => $project->isPublished() ? 1 : 0,
            ':created_at' => $project->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    public function findById(string $id): ?Project
    {
        $stmt = $this->pdo->prepare("SELECT * FROM projects WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapRowToProject($data);
    }

    public function findAll(array $filters = []): array
    {
        $sql = "SELECT * FROM projects WHERE 1=1";
        $params = [];

        if (isset($filters['activityType'])) {
            $sql .= " AND activity_type = :activityType";
            $params[':activityType'] = $filters['activityType'];
        }

        if (isset($filters['isPublished'])) {
            $sql .= " AND is_published = :isPublished";
            $params[':isPublished'] = (int) $filters['isPublished'];
        }

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map([$this, 'mapRowToProject'], $rows);
    }

    private function mapRowToProject(array $row): Project
    {
        $project = new Project(
            $row['id'],
            $row['title'],
            $row['description'],
            ActivityType::from($row['activity_type']),
            $row['creator_id'],
            new \DateTimeImmutable($row['start_date_time']),
            $row['meeting_point'],
            (float) $row['price'],
            $row['currency'],
            $row['image_url'] ?? null
        );

        if ($row['is_published']) {
            $project->publish();
        }

        return $project;
    }
}
