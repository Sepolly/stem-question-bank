<?php

namespace App\Http\Requests;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateQuestionRequest extends FormRequest
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
            'round' => ['sometimes', 'nullable', 'integer'],
            'type' => ['sometimes', 'nullable', Rule::in(QuestionType::cases())],
            'difficulty' => ['sometimes', 'nullable', Rule::in(QuestionDifficulty::cases())],
            'question_text' => ['sometimes', 'nullable', 'string'],
            'options' => ['sometimes', 'nullable', 'array'],
            'bool_answer' => ['sometimes', 'nullable', 'boolean'],
            'answer_text' => ['sometimes', 'nullable', 'string'],
            'subject_id' => ['sometimes', 'nullable', 'integer', 'exists:subjects,id'],
            'topic_id' => ['sometimes', 'nullable', 'integer', 'exists:topics,id'],
            'has_been_asked' => ['sometimes', 'nullable', 'boolean'],
        ];
    }
}
