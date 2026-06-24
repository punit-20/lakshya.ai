<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LaunchMarketingCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string',
            'description' => 'required|string',
            'platform' => 'required|string',
            'image_prompt' => 'required|string',
        ];
    }
}
