<?php

namespace App\Models;

use App\Enums\FrequencyUnitType;
use App\Enums\GroupStatus;
use App\Models\Member;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * @property int $id
 * @property string $invite_code
 * @property string $name
 * @property float $contribution_amount
 * @property FrequencyUnitType $frequency_unit
 * @property int $frequency_interval
 * @property \Illuminate\Support\Carbon $start_date
 * @property GroupStatus $status
 * @property int $user_id
 */

#[Fillable([
    "name",
    "contribution_amount",
    "frequency_unit",
    "frequency_interval",
    "start_date",
    "status",
])]
class Group extends Model
{
    use HasFactory;
    protected function casts(): array
    {
        return [
            "start_date" => "date",
            "contribution_amount" => "decimal:2",
            "frequency_unit" => FrequencyUnitType::class,
            "status" => GroupStatus::class
        ];
    }

    protected static function booted()
    {
        static::creating(function (Group $group) {
            $group->invite_code = Str::of(Str::random(6))->upper();
        });
    }


    #region RELATIONS 
    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cycles(): HasMany
    {
        return $this->hasMany(Cycle::class);
    }

    public function groupShares(): HasMany
    {
        return $this->hasmany(GroupShare::class);
    }

    #endregion



    #region HELPERS

    public function isDraft(): bool
    {
        return $this->status === GroupStatus::DRAFT;
    }

    /**
     * Determine if the current round is completed by looking at the cycle's
     * maximum round_number and see if it is already disbursed
     * @return bool
     */
    public function isRoundCompleted(): bool
    {
        $currentRound = $this->cycles()->max("round_number");

        if (!$currentRound)
            return false;

        return $this->cycles()
            ->where("round_number", $currentRound)
            ->whereNull('disbursed_at')
            ->doesntExist();
    }
    #endregion

}
