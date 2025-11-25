<?php

namespace Trekly\User\Infrastructure\Persistence;

use Trekly\User\Domain\Profile\CreatorProfile;
use Trekly\User\Domain\Profile\UserProfile;
use Trekly\User\Domain\Profile\UserProfileRepository;

class PDOUserProfileRepository implements UserProfileRepository
{
    private \PDO $pdo;

    public function __construct(\PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function save(UserProfile $profile): void
    {
        // Check if exists to decide INSERT or UPDATE
        if ($this->findByUserId($profile->getUserId())) {
            $stmt = $this->pdo->prepare("
                UPDATE user_profiles 
                SET full_name = :full_name, bio = :bio, avatar_url = :avatar_url, location = :location
                WHERE user_id = :user_id
            ");
        } else {
            $stmt = $this->pdo->prepare("
                INSERT INTO user_profiles (user_id, full_name, bio, avatar_url, location)
                VALUES (:user_id, :full_name, :bio, :avatar_url, :location)
            ");
        }

        $stmt->execute([
            ':user_id' => $profile->getUserId(),
            ':full_name' => $profile->getFullName(),
            ':bio' => $profile->getBio(),
            ':avatar_url' => $profile->getAvatarUrl(),
            ':location' => $profile->getLocation()
        ]);

        if ($profile->getCreatorProfile()) {
            $this->saveCreatorProfile($profile->getUserId(), $profile->getCreatorProfile());
        }
    }

    private function saveCreatorProfile(string $userId, CreatorProfile $creatorProfile): void
    {
        // Upsert creator profile
        $stmt = $this->pdo->prepare("
            INSERT INTO creator_profiles (user_id, type, document_info, company_name, verified)
            VALUES (:user_id, :type, :document_info, :company_name, :verified)
            ON DUPLICATE KEY UPDATE
            type = VALUES(type), document_info = VALUES(document_info), company_name = VALUES(company_name), verified = VALUES(verified)
        ");

        $stmt->execute([
            ':user_id' => $userId,
            ':type' => $creatorProfile->getType(),
            ':document_info' => $creatorProfile->getDocumentInfo(),
            ':company_name' => $creatorProfile->getCompanyName(),
            ':verified' => (int) $creatorProfile->isVerified()
        ]);
    }

    public function findByUserId(string $userId): ?UserProfile
    {
        $stmt = $this->pdo->prepare("SELECT * FROM user_profiles WHERE user_id = :user_id");
        $stmt->execute([':user_id' => $userId]);
        $data = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$data) {
            return null;
        }

        $profile = new UserProfile(
            $data['user_id'],
            $data['full_name'],
            $data['bio'] ?? '',
            $data['avatar_url'] ?? '',
            $data['location'] ?? ''
        );

        // Fetch creator profile if exists
        $stmtCreator = $this->pdo->prepare("SELECT * FROM creator_profiles WHERE user_id = :user_id");
        $stmtCreator->execute([':user_id' => $userId]);
        $creatorData = $stmtCreator->fetch(\PDO::FETCH_ASSOC);

        if ($creatorData) {
            $profile->becomeCreator(new CreatorProfile(
                $creatorData['type'],
                $creatorData['document_info'],
                $creatorData['company_name'],
                (bool) $creatorData['verified']
            ));
        }

        return $profile;
    }
}
