<?php

namespace Trekly\Participation\Domain\Participation;

enum ParticipationStatus: string
{
    case REQUESTED = 'REQUESTED';
    case CONFIRMED = 'CONFIRMED';
    case WAITLIST = 'WAITLIST';
    case CANCELLED = 'CANCELLED';
    case ATTENDED = 'ATTENDED';
}
