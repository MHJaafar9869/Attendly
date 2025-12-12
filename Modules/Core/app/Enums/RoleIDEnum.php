<?php

namespace Modules\Core\Enums;

enum RoleIDEnum: int
{
    case SUPER_ADMIN = 1;
    case ADMIN = 2;
    case ACCOUNTANT = 3;
    case TEACHER = 4;
    case SUPERVISOR = 5;
    case STUDENT = 6;
}
