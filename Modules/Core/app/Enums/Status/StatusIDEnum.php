<?php

namespace Modules\Core\Enums\Status;

enum StatusIDEnum: int
{
    // === User ===
    case USER_PENDING = 1;
    case USER_ACTIVE = 2;
    case USER_INACTIVE = 3;
    case USER_SUSPENDED = 4;
    case USER_BANNED = 5;
}
