<?php

declare(strict_types=1);

namespace App\DTO;

final readonly class CurrentCycleStats implements \JsonSerializable
{
    public function __construct(
        public float $collectedTotal,
        public float $expectedTotal,
    ) {}

    public function jsonSerialize(): array
    {
        return [
            'collected_total' => $this->collectedTotal,
            'expected_total' => $this->expectedTotal,
        ];
    }
}