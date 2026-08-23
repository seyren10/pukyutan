<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Group;

/**
 * @property int $id
 * @property int $group_id
 * @property string $name
 * @property string|null $email
 * @property int $payout_order
 * @property string $dicebear_seed
 */
#[Fillable([
    "name",
    "email",
    "payout_order",
    "dicebear_seed"
])]
class Member extends Model
{
    use HasFactory, SoftDeletes;

    protected static function booted(): void
    {
        static::addGlobalScope('payoutOrder', function (Builder $builder) {
            $builder->orderBy('payout_order');
        });

        static::creating(function (Member $member) {
            $member->dicebear_seed = \Str::random(12);
        });
    }


    #region ============ RELATIONSHIPS ============
    /**
     * @return BelongsTo<Group, Member>
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    /**
     * @return HasMany<Contribution, Member>
     */
    public function contributions(): HasMany
    {
        return $this->hasmany(Contribution::class);
    }

    #endregion ============ RELATIONSHIPS ============
}
