<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateMarketingPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_description' => 'required|string',
            'platform' => 'required|string',
            'tone' => 'required|string',
            'target_audience' => 'required|string',
            'cta' => 'nullable|string',
        ];
    }
}
