<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SaveAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'platform' => 'required|in:reddit,twitter,linkedin',
            'username' => 'required|string',
            'session_cookies' => 'nullable|string',
        ];
    }
}
