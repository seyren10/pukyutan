<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $cycle_id
 * @property int $member_id
 */
#[Fillable([
    "cycle_id",
    "member_id",
    "sent_at"
])]
class CycleReminder extends Model
{
    protected function casts(): array
    {
        return [
            "sent_at" => "datetime"
        ];
    }

    #region RELATIONSHIPS
    public function cycle(): BelongsTo
    {
        return $this->belongsTo(Cycle::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    #endregion
}
