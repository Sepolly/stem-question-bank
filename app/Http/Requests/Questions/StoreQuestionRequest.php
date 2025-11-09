<?php

namespace App\Http\Requests\Questions;

use App\Enums\QuestionDifficulty;
use App\Enums\QuestionType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreQuestionRequest extends FormRequest
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
            'round' => ['required', 'integer'],
            'type' => ['required', Rule::in(QuestionType::cases())],
            'difficulty' => ['required', Rule::in(QuestionDifficulty::cases())],
            'question_text' => ['required', 'string'],
            'options' => ['nullable', 'array'],
            'bool_answer' => ['nullable', 'boolean'],
            'answer_text' => ['nullable', 'string'],
            'subject_id' => ['required', 'integer', 'exists:subjects,id'],
            'topic_id' => ['required', 'integer', 'exists:topics,id'],
        ];
    }
}
