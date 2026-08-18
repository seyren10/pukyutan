<?php

namespace App\Http\Requests\V1;

use App\Enums\FrequencyUnitType;
use App\Enums\GroupStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isDraft = $this->route('group')->status === GroupStatus::DRAFT;

        $rules = [
            'name' => ['sometimes', 'string', 'max:255'],
        ];

        if ($isDraft) {
            $rules += [
                'contribution_amount' => ['sometimes', 'numeric', 'min:0.01'],
                'frequency_unit' => ['sometimes', Rule::enum(FrequencyUnitType::class)],
                'frequency_interval' => ['sometimes', 'integer', 'min:1'],
                'start_date' => ['sometimes', 'date'],
            ];
        } else {
            // Explicit "prohibited" rather than silently ignoring these
            // fields if sent — a clear 422 is more honest than quietly
            // dropping data the client thought it changed.
            $rules += [
                'contribution_amount' => ['prohibited'],
                'frequency_unit' => ['prohibited'],
                'frequency_interval' => ['prohibited'],
                'start_date' => ['prohibited'],
            ];
        }

        return $rules;
    }

    public function messages(): array
    {
        $lockedMessage = 'This can only be changed while the group is still in draft.';

        return [
            'contribution_amount.prohibited' => $lockedMessage,
            'frequency_unit.prohibited' => $lockedMessage,
            'frequency_interval.prohibited' => $lockedMessage,
            'start_date.prohibited' => $lockedMessage,
        ];
    }
}