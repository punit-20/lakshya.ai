<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateGrowthSuiteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_description' => 'required|string',
            'target_audience' => 'required|string',
            'campaign_goal' => 'required|string',
        ];
    }
}
