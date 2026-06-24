<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateSocialSuiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_description' => 'required|string',
            'tone' => 'required|string',
            'target_audience' => 'required|string',
            'cta' => 'nullable|string',
        ];
    }
}
