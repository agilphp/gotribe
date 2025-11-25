<?php

namespace Trekly\User\Application;

use Trekly\User\Domain\Rating\CreatorRatingRepository;

class GetMemberRatingsUseCase
{
    public function __construct(
        private CreatorRatingRepository $ratingRepository
    ) {}

    public function execute(string $memberId): array
    {
        return $this->ratingRepository->findByMemberId($memberId);
    }
}
