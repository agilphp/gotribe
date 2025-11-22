<?php

namespace Trekly\Participation\Infrastructure\Persistence;

use Trekly\Participation\Domain\Participation\Participation;
use Trekly\Participation\Domain\Participation\ParticipationRepository;
use Trekly\Participation\Domain\Participation\ParticipationStatus;

class PDOParticipationRepository implements ParticipationRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(Participation $participation): void
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO participations (id, project_id, user_id, status, created_at)
            VALUES (:id, :project_id, :user_id, :status, :created_at)
            ON DUPLICATE KEY UPDATE
            status = VALUES(status)
        ");

        $stmt->execute([
            ':id' => $participation->getId(),
            ':project_id' => $participation->getProjectId(),
            ':user_id' => $participation->getUserId(),
            ':status' => $participation->getStatus()->value,
            ':created_at' => $participation->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
    }

    public function findById(string $id): ?Participation
    {
        $stmt = $this->pdo->prepare("SELECT * FROM participations WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapRowToParticipation($data);
    }

    public function findByProjectAndUser(string $projectId, string $userId): ?Participation
    {
        $stmt = $this->pdo->prepare("SELECT * FROM participations WHERE project_id = :project_id AND user_id = :user_id");
        $stmt->execute([':project_id' => $projectId, ':user_id' => $userId]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        return $this->mapRowToParticipation($data);
    }

    private function mapRowToParticipation(array $row): Participation
    {
        return new Participation(
            $row['id'],
            $row['project_id'],
            $row['user_id'],
            ParticipationStatus::from($row['status'])
        );
    }
}
