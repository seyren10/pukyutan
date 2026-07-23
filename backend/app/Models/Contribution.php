<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * @property int $id
 * @property float $amount
 * @property Carbon $paid_at
 * @property string|null $notes
 * @property int $member_id
 * @property int $cycle_id
 */
#[Fillable(["amount", "paid_at", "member_id", "notes"])]
class Contribution extends Model
{
    use HasFactory;
    protected function casts(): array
    {
        return [
            "amount" => "decimal:2",
            "paid_at" => "date"
        ];
    }

    #region ============ RELATIONSHIPS ============

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(Cycle::class);
    }
    #endregion ============ RELATIONSHIPS ============
}
