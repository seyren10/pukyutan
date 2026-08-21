<?php

declare(strict_types=1);

namespace App\DTO;

use Carbon\Carbon;

final readonly class NextPayout implements \JsonSerializable
{
    public function __construct(
        public int $groupId,
        public string $groupName,
        public string $recipientName,
        public float $amount,
        public Carbon $dueDate,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'group_id' => $this->groupId,
            'group_name' => $this->groupName,
            'recipient_name' => $this->recipientName,
            'amount' => $this->amount,
            'due_date' => $this->dueDate,
        ];
    }
}