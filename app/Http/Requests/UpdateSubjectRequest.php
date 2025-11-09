<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSubjectRequest extends FormRequest
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
            'name' => ['bail', 'required', 'string', 'min:3', 'max:255'],
            'description' => ['nullable', 'string', 'min:5', 'max:1000'],
            'topics' => ['required', 'array'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter the name of the subject',
            'topics.required' => 'Please provide the topics',
        ];
    }
}
