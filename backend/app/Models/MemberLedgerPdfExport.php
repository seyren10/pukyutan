<?php

namespace App\Models;

use App\Enums\PdfExportStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property int $member_id
 * @property PdfExportStatus $status
 * @property string|null $file_path
 * @property string|null $error_message
 */
#[Fillable(['member_id', 'status', 'file_path', 'error_message'])]
class MemberLedgerPdfExport extends Model
{
    use Prunable;
    protected function casts(): array
    {
        return [
            'status' => PdfExportStatus::class,
        ];
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function prunable(): Builder
    {
        return static::where('created_at', '<', now()->subHours(24));
    }

    protected function pruning(): void
    {
        if ($this->file_path) {
            Storage::disk('local')->delete($this->file_path);
        }
    }
}
