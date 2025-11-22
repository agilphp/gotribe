<?php

namespace Trekly\User\Interface\Http;

use Trekly\User\Application\CreateCreatorProfileUseCase;
use Trekly\User\Application\GetUserProfileUseCase;
use Trekly\User\Application\UpdateUserProfileUseCase;

class ProfileController
{
    private GetUserProfileUseCase $getUserProfileUseCase;
    private UpdateUserProfileUseCase $updateUserProfileUseCase;
    private CreateCreatorProfileUseCase $createCreatorProfileUseCase;

    public function __construct(
        GetUserProfileUseCase $getUserProfileUseCase,
        UpdateUserProfileUseCase $updateUserProfileUseCase,
        CreateCreatorProfileUseCase $createCreatorProfileUseCase
    ) {
        $this->getUserProfileUseCase = $getUserProfileUseCase;
        $this->updateUserProfileUseCase = $updateUserProfileUseCase;
        $this->createCreatorProfileUseCase = $createCreatorProfileUseCase;
    }

    public function getProfile(string $userId): void
    {
        $profile = $this->getUserProfileUseCase->execute($userId);
        if ($profile) {
            echo json_encode([
                'userId' => $profile->getUserId(),
                'fullName' => $profile->getFullName(),
                'bio' => $profile->getBio(),
                'avatarUrl' => $profile->getAvatarUrl(),
                'location' => $profile->getLocation(),
                'creatorProfile' => $profile->getCreatorProfile() ? [
                    'type' => $profile->getCreatorProfile()->getType(),
                    'documentInfo' => $profile->getCreatorProfile()->getDocumentInfo(),
                    'companyName' => $profile->getCreatorProfile()->getCompanyName(),
                    'verified' => $profile->getCreatorProfile()->isVerified()
                ] : null
            ]);
        } else {
            http_response_code(404);
            echo json_encode(['error' => 'Profile not found']);
        }
    }

    public function updateProfile(string $userId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        // Basic validation
        $this->updateUserProfileUseCase->execute(
            $userId,
            $data['fullName'] ?? '',
            $data['bio'] ?? '',
            $data['avatarUrl'] ?? '',
            $data['location'] ?? ''
        );
        echo json_encode(['message' => 'Profile updated']);
    }

    public function createCreatorProfile(string $userId): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        // Basic validation
        if (!isset($data['type'], $data['documentInfo'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        $this->createCreatorProfileUseCase->execute(
            $userId,
            $data['type'],
            $data['documentInfo'],
            $data['companyName'] ?? null
        );
        echo json_encode(['message' => 'Creator profile created']);
    }
}
