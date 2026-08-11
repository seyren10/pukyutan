<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

/**
 * @property int $id
 * @property int $round_number
 * @property int $cycle_number
 * @property \Illuminate\Support\Carbon $due_date
 * @property \Illuminate\Support\Carbon|null $disbursed_at
 * @property float|null $disbursed_amount
 * @property int $group_id
 * @property int $recipient_member_id
 * 
 */
#[Fillable([
    "round_number",
    "cycle_number",
    "due_date",
    "disbursed_at",
    "disbursed_amount",
    "recipient_member_id"
])]
class Cycle extends Model
{
    use HasFactory;
    protected function casts(): array
    {
        return [
            "due_date" => "date",
            "disbursed_at" => "datetime",
            "disbursed_amount" => "decimal:2"
        ];
    }

    #region ============== RELATIONS ===============
    /**
     * @return BelongsTo<Group, Cycle>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @return BelongsTo<Member, Cycle>
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Member::class, "recipient_member_id");
    }

    /**
     * @return HasMany<Contribution, Cycle>
     */
    public function contributions(): HasMany
    {
        return $this->hasMany(Contribution::class);
    }

    public function members(): HasManyThrough
    {
        return $this->hasManyThrough(
            Member::class,
            Group::class,
            'id',        // groups.id — matched against cycles.group_id
            'group_id',  // members.group_id — matched against groups.id
            'group_id',  // cycles.group_id (local key)
            'id',        // groups.id (local key on the through table)
        );
    }

    #endregion
}
