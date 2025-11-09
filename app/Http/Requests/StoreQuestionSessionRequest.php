<?php

namespace App\Http\Requests;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionSessionRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:5'],
            'number_of_questions' => ['required', 'integer'],
            'type' => ['required', Rule::in(QuestionType::cases())],
            'difficulty' => ['required', Rule::in(QuestionDifficulty::cases())],
            'round' => ['required', 'integer'],
            'starts_at' => ['nullable'],
            'ends_at' => ['nullable', 'after:starts_at'],
        ];
    }
}
