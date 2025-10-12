<?php

namespace Modules\Core\Enums\Status;

enum StatusEnum: string
{
    // === User ===
    case USER_PENDING = 'pending_verification';
    case USER_ACTIVE = 'active';
    case USER_INACTIVE = 'inactive';
    case USER_SUSPENDED = 'suspended';
    case USER_BANNED = 'banned';

    public function label(): string
    {
        return match ($this) {
            // === USER ===
            self::USER_PENDING => 'Pending Verification',
            self::USER_ACTIVE => 'Active',
            self::USER_INACTIVE => 'Inactive',
            self::USER_SUSPENDED => 'Suspended',
            self::USER_BANNED => 'Banned',
        };
    }
}
