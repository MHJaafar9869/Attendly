<?php

namespace Modules\Core\Enums\Status;

enum StatusIDEnum: int
{
    // === User ===
    case STUDENT_OTP_PENDING = 1;
    case STUDENT_PENDING = 2;
    case STUDENT_ACTIVE = 3;
    case STUDENT_INACTIVE = 4;
    case STUDENT_SUSPENDED = 5;
    case STUDENT_BANNED = 6;

    // === Teacher ===
    case TEACHER_OTP_PENDING = 7;
    case TEACHER_PENDING = 8;
    case TEACHER_ACTIVE = 9;
    case TEACHER_INACTIVE = 10;
    case TEACHER_SUSPENDED = 11;
    case TEACHER_BANNED = 12;
}
