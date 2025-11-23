<?php

namespace Trekly\User\Domain\Rating;

interface CreatorRatingRepository
{
    public function save(CreatorRating $rating): void;
    public function findByCreatorId(string $creatorId): array;
    public function findByMemberId(string $memberId): array;
    public function findByMemberAndProject(string $memberId, string $projectId): ?CreatorRating;

    public function getAverageRating(string $creatorId): float;
    public function countRatings(string $creatorId): int;
}
