<?php

namespace Trekly\Project\Application;

class GuestLimitCalculator
{
    private const BASE_LIMIT = 10;
    private const EVENTS_THRESHOLD = 5;
    private const MULTIPLIER = 3;
    private const MIN_RATING = 3.0;

    /**
     * Calculate maximum guests based on creator's statistics
     * Formula: baseLimit * (3 ^ floor(eventsCreated / 5)) if avgRating >= 3.0
     * 
     * Examples:
     * - 0-4 events: 10 guests
     * - 5-9 events (avg >= 3): 30 guests
     * - 10-14 events (avg >= 3): 90 guests
     * - 15-19 events (avg >= 3): 270 guests
     */
    public function calculate(int $eventsCreated, float $avgRating): int
    {
        if ($avgRating < self::MIN_RATING) {
            return self::BASE_LIMIT;
        }

        $bonusMultiplier = floor($eventsCreated / self::EVENTS_THRESHOLD);
        return self::BASE_LIMIT * pow(self::MULTIPLIER, $bonusMultiplier);
    }
}
