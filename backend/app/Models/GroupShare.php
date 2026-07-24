<?php

namespace App\Models;

use App\Enums\GroupShareStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $group_id
 * @property int $user_id
 * @property Carbon $requested_at
 * @property Carbon|null $responded_at
 * @property GroupShareStatus $status
 */
#[Fillable([
    "status",
    "requested_at",
    "responded_at"
])]
class GroupShare extends Model
{
    /** @use HasFactory<\Database\Factories\GroupShareFactory> */
    use HasFactory;

    protected function casts(): array
    {
        return [
            "status" => GroupShareStatus::class,
            "requested_at" => "timestamp",
            "responded_at" => "timestamp"
        ];
    }

    #region RELATIONSHIPS
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    #endregion
}
