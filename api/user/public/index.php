<?php

require_once __DIR__ . '/../bootstrap.php';

use Trekly\User\Infrastructure\Persistence\DoctrineUserProfileRepository;
use Trekly\User\Application\GetUserProfileUseCase;
use Trekly\User\Application\UpdateUserProfileUseCase;
use Trekly\User\Application\CreateCreatorProfileUseCase;
use Trekly\User\Application\RateCreatorUseCase;
use Trekly\User\Interface\Http\ProfileController;
use Trekly\User\Interface\Http\RatingController;

use Trekly\User\Infrastructure\Persistence\DoctrineCreatorRatingRepository;
use Trekly\User\Application\GetCreatorAverageRatingUseCase;

use Trekly\User\Application\GetCreatorRatingsUseCase;
use Trekly\User\Application\GetMemberRatingsUseCase;
use Trekly\User\Infrastructure\Http\HttpProjectRepository;
use Trekly\User\Infrastructure\Http\HttpParticipationRepository;

// CORS headers are handled by root bootstrap.php

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

try {
    error_log("[DEBUG] Starting controller instantiation");
    $entityManager = require __DIR__ . '/../bootstrap.php';
    error_log("[DEBUG] EntityManager instantiated");
    $repository = new DoctrineUserProfileRepository($entityManager);
    error_log("[DEBUG] DoctrineUserProfileRepository instantiated");
    $getUserProfileUseCase = new GetUserProfileUseCase($repository);
    $updateUserProfileUseCase = new UpdateUserProfileUseCase($repository);
    $createCreatorProfileUseCase = new CreateCreatorProfileUseCase($repository);
    $controller = new ProfileController(
        $getUserProfileUseCase,
        $updateUserProfileUseCase,
        $createCreatorProfileUseCase
    );
    error_log("[DEBUG] ProfileController instantiated");
    $ratingRepository = new DoctrineCreatorRatingRepository($entityManager);
    error_log("[DEBUG] DoctrineCreatorRatingRepository instantiated");
    
    // Crear clientes HTTP para los servicios externos
    $projectRepository = new HttpProjectRepository();
    $participationRepository = new HttpParticipationRepository();
    
    $rateCreatorUseCase = new RateCreatorUseCase(
        $ratingRepository, 
        $repository,
        $projectRepository,
        $participationRepository
    );
    $getCreatorAverageRatingUseCase = new GetCreatorAverageRatingUseCase($ratingRepository);
    $getCreatorRatingsUseCase = new GetCreatorRatingsUseCase($ratingRepository);
    $getMemberRatingsUseCase = new GetMemberRatingsUseCase($ratingRepository);
    $ratingController = new RatingController(
        $rateCreatorUseCase,
        $getCreatorAverageRatingUseCase,
        $getCreatorRatingsUseCase,
        $getMemberRatingsUseCase
    );
    error_log("[DEBUG] RatingController instantiated");
    $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $method = $_SERVER['REQUEST_METHOD'];
    
    if (preg_match('#/api/users/([^/]+)$#', $uri, $matches)) {
        $userId = $matches[1];
        if ($method === 'GET') {
            $controller->getProfile($userId);
        } elseif ($method === 'PUT') {
            $controller->updateProfile($userId);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } elseif (preg_match('#/api/users/([^/]+)/creator-profile$#', $uri, $matches)) {
        $userId = $matches[1];
        if ($method === 'POST') {
            $controller->createCreatorProfile($userId);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } elseif (preg_match('#/api/ratings$#', $uri)) {
        if ($method === 'POST') {
            $ratingController->rate();
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } elseif (preg_match('#/api/ratings/creator/([^/]+)/average$#', $uri, $matches)) {
        $creatorId = $matches[1];
        if ($method === 'GET') {
            $ratingController->getAverage($creatorId);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } elseif (preg_match('#/api/ratings/creator/([^/]+)$#', $uri, $matches)) {
        $creatorId = $matches[1];
        if ($method === 'GET') {
            $ratingController->getCreatorRatings($creatorId);
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } elseif (preg_match('#/api/ratings/member/([^/]+)$#', $uri, $matches)) {
        $memberId = $matches[1];
        error_log("[DEBUG] Calling getMemberRatings for memberId: $memberId");
        if ($method === 'GET') {
            try {
                $ratingController->getMemberRatings($memberId);
                error_log("[DEBUG] getMemberRatings completed for memberId: $memberId");
            } catch (\Throwable $e) {
                error_log("[ERROR] Exception in getMemberRatings: " . $e->getMessage());
                http_response_code(200);
                echo json_encode([]);
            }
        } else {
            http_response_code(405);
            echo json_encode(['error' => 'Method Not Allowed']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Not Found', 'uri' => $uri]);
    }
} catch (\Exception $e) {
    error_log("[ERROR] Uncaught exception in index.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
