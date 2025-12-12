<?php

declare(strict_types=1);

namespace Modules\Core\DTO;

use Illuminate\Support\Carbon;

class UserFilterDto
{
    public function __construct(
        public readonly ?string $search,
        public readonly ?bool $loggedIn,
        public readonly string $orderBy,
        public readonly string $orderDir,
        public readonly ?string $dateRangeColumn,
        public readonly ?Carbon $dateRangeStart,
        public readonly ?Carbon $dateRangeEnd,
        public readonly ?bool $emailVerified,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            search: $data['search'],
            loggedIn: $data['logged_in'] ?? null,
            orderBy: $data['order_by'] ?? 'created_at',
            orderDir: $data['order_dir'] ?? 'desc',
            dateRangeColumn: $data['date_range_column'] ?? null,
            dateRangeStart: $data['date_range_start'] ?? null,
            dateRangeEnd: $data['date_range_end'] ?? null,
            emailVerified: $data['email_verified'] ?? null
        );
    }

    public function toArray(): array
    {
        return [
            'search' => $this->search,
            'loggedIn' => $this->loggedIn,
            'orderBy' => $this->orderBy,
            'orderDir' => $this->orderDir,
            'dateRangeColumn' => $this->dateRangeColumn,
            'dateRangeStart' => $this->dateRangeStart,
            'dateRangeEnd' => $this->dateRangeEnd,
            'emailVerified' => $this->emailVerified,
        ];
    }
}
