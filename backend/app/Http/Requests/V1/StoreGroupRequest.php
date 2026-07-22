<?php

namespace App\Http\Requests\V1;

use App\Enums\FrequencyUnitType;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreGroupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            "name" => ["required", "string", "max:255"],
            "contribution_amount" => ["required", "numeric", "min:0.01"],
            "frequency_unit" => ["required", Rule::enum(FrequencyUnitType::class)],
            "frequency_interval" => ["sometimes", "integer", "min:1"],
            "start_date" => ['required', 'date', 'after_or_equal:today'],
        ];
    }
}
