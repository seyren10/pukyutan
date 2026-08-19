<?php

namespace App\Models;

use App\Enums\NotificationType;
use Illuminate\Database\Eloquent\MassPrunable;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use MassPrunable;

    public function casts(): array
    {
        return [
            'type' => NotificationType::class
        ];
    }

    public function prunable()
    {
        return $this->whereNotNull('read_at')
            ->where('read_at', "<=", now()->subMonth());
    }
}
