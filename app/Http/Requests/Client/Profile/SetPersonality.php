<?php

namespace App\Http\Requests\Client\Profile;

use App\Http\Requests\ApiRequest;

class SetPersonality extends ApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'desc' => 'nullable|string',
            'travel' => 'nullable|string',
            'weekend' => 'nullable|string',
            'humor' => 'nullable|string',
            'person' => 'nullable|string',
            'dress' => 'nullable|string',
        ];
    }
}
