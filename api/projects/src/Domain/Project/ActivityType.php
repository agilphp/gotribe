<?php

namespace Trekly\Project\Domain\Project;

enum ActivityType: string
{
    case HIKING = 'HIKING';
    case RUNNING = 'RUNNING';
    case MTB = 'MTB';
    case TRIATHLON = 'TRIATHLON';
    case MIXED = 'MIXED';
}
