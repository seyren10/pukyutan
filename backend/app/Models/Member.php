<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Group;

/**
 * @property int $id
 * @property string $name
 * @property string|null $email
 * @property int $payout_order
 */
#[Fillable([
    "name",
    "email",
    "payout_order",
])]
class Member extends Model
{
    use HasFactory, SoftDeletes;

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
