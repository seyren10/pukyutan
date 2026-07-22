<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
    protected function casts(): array
    {
        return [
            "due_date" => "date",
            "disbursed_at" => "datetime",
            "disbursed_amount" => "decimal:2"
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(Member::class, "recipient_member_id");
    }
}
