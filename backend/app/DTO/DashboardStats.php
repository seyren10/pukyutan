<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class DashboardStats implements \JsonSerializable
{
    public function __construct(
        public int $activeGroups,
        public int $draftGroups,
        public int $completedGroups,
        public int $membersTotal,
        public ?CurrentCycleStats $currentCycle,
        public ?NextPayout $nextPayout,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'groups' => [
                'active' => $this->activeGroups,
                'draft' => $this->draftGroups,
                'completed' => $this->completedGroups,
            ],
            'members_total' => $this->membersTotal,
            'current_cycle' => $this->currentCycle,
            'next_payout' => $this->nextPayout,
        ];
    }
}