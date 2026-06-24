<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GenerateAdCampaignRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product' => 'required|string',
            'target_audience' => 'required|string',
            'budget' => 'required|string',
            'campaign_goal' => 'required|string',
        ];
    }
}
