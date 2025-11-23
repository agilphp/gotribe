<?php

namespace Trekly\Auth\Domain\User;

enum Role: string
{
    case MEMBER = 'MEMBER';
    case CREATOR = 'CREATOR';
    case ADMIN = 'ADMIN';
}
