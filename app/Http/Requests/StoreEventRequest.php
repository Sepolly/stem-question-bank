<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string'],
            'start_date' => ['required', Rule::date()->todayOrAfter()],
            'end_date' => ['required', 'after:start_date'],
        ];
    }
}
