<?php

namespace Trekly\User\Interface\Http;

use Trekly\User\Application\RateCreatorUseCase;
use Trekly\User\Application\GetCreatorAverageRatingUseCase;

use Trekly\User\Application\GetCreatorRatingsUseCase;
use Trekly\User\Application\GetMemberRatingsUseCase;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class RatingController
{
    public function __construct(
        private RateCreatorUseCase $rateUseCase,
        private GetCreatorAverageRatingUseCase $getAverageUseCase,
        private GetCreatorRatingsUseCase $getRatingsUseCase,
        private GetMemberRatingsUseCase $getMemberRatingsUseCase
    ) {}

    public function rate(): void
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $memberId = $_SERVER['HTTP_X_USER_ID'] ?? null;
        $role = $_SERVER['HTTP_X_USER_ROLE'] ?? null;

        error_log("RatingController - Initial memberId: " . ($memberId ?? 'NULL'));
        error_log("RatingController - Initial role: " . ($role ?? 'NULL'));

        // Fallback: Decode JWT if headers are missing
        if (!$memberId || !$role) {
            $authHeader = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
            if (empty($authHeader) && function_exists('apache_request_headers')) {
                $headers = apache_request_headers();
                $authHeader = $headers['Authorization'] ?? '';
            }

            error_log("RatingController - Auth Header: " . $authHeader);

            if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
                try {
                    $jwt = $matches[1];
                    $key = new Key($_ENV['JWT_SECRET'] ?? 'gotribe_jwt_secret_change_this_in_production_32chars_minimum', 'HS256');
                    $decoded = JWT::decode($jwt, $key);
                    $memberId = $decoded->sub ?? $memberId;
                    $role = $decoded->role ?? $role;
                    error_log("RatingController - Decoded memberId: " . ($memberId ?? 'NULL'));
                    error_log("RatingController - Decoded role: " . ($role ?? 'NULL'));
                } catch (\Exception $e) {
                    error_log("RatingController - JWT Decode Error: " . $e->getMessage());
                    http_response_code(401);
                    echo json_encode(['error' => 'Invalid token: ' . $e->getMessage()]);
                    return;
                }
            } else {
                error_log("RatingController - No Bearer token found in Authorization header");
            }
        }

        $memberId = $memberId ?? 'unknown';
        $role = $role ?? 'MEMBER';

        error_log("RatingController - Final memberId: " . $memberId);
        error_log("RatingController - Final role: " . $role);

        if ($role !== 'MEMBER') {
            http_response_code(403);
            echo json_encode(['error' => 'Only MEMBER users can rate creators']);
            return;
        }

        if (!isset($data['creatorId'], $data['projectId'], $data['rating'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing required fields']);
            return;
        }

        try {
            error_log("RatingController - About to call rateUseCase with:");
            error_log("  memberId: {$memberId}");
            error_log("  creatorId: {$data['creatorId']}");
            error_log("  projectId: {$data['projectId']}");
            error_log("  rating: {$data['rating']}");
            
            $this->rateUseCase->execute(
                $memberId,
                $data['creatorId'],
                $data['projectId'],
                (int) $data['rating'],
                $data['comment'] ?? null
            );
            http_response_code(201);
            echo json_encode(['message' => 'Rating submitted successfully']);
        } catch (\Exception $e) {
            error_log("RatingController - Exception caught: " . $e->getMessage());
            error_log("RatingController - Exception trace: " . $e->getTraceAsString());
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }


    public function getAverage(string $creatorId): void
    {
        try {
            $result = $this->getAverageUseCase->execute($creatorId);
            echo json_encode($result);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getCreatorRatings(string $creatorId): void
    {
        try {
            $ratings = $this->getRatingsUseCase->execute($creatorId);
            $response = array_map(function ($r) {
                return [
                    'id' => $r->getId(),
                    'rating' => $r->getRating(),
                    'comment' => $r->getComment(),
                    'createdAt' => $r->getCreatedAt()->format('Y-m-d H:i:s')
                ];
            }, $ratings);
            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(400);
            echo json_encode(['error' => $e->getMessage()]);
        }
    }

    public function getMemberRatings(string $memberId): void
    {
        try {
            $ratings = $this->getMemberRatingsUseCase->execute($memberId);
            if (!is_array($ratings) || empty($ratings)) {
                echo json_encode([]);
                return;
            }
            $response = array();
            foreach ($ratings as $r) {
                if (is_object($r) && method_exists($r, 'getId')) {
                    $response[] = [
                        'id' => $r->getId(),
                        'projectId' => $r->getProjectId(),
                        'rating' => $r->getRating(),
                        'comment' => $r->getComment(),
                        'createdAt' => $r->getCreatedAt()->format('Y-m-d H:i:s')
                    ];
                }
            }
            echo json_encode($response);
        } catch (\Exception $e) {
            http_response_code(200);
            echo json_encode([]);
        }
    }
}
