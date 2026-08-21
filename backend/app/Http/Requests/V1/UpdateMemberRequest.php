<?php

namespace App\Http\Requests\V1;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMemberRequest extends FormRequest
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
        $member = $this->route('member');
        return [
            "name" => ["sometimes", "string", "max:255"],
            "email" => [
                "sometimes",
                "email",
                "max:255",
                Rule::when($this->email !== $member->email, Rule::unique("members")->where(
                    fn($query) => $query->where("group_id", $member->group->id)
                ))
            ],
        ];
    }
}
