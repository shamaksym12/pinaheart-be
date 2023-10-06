<?php

namespace App\Http\Requests\Client\Profile;

use App\Http\Requests\ApiRequest;

class SetMatches extends ApiRequest
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
        $rules = [
            'sex' => 'nullable|in:M,F',
            'country_id' => 'nullable|integer',
            'formatted_address' => 'nullable|string',
            'distance' => 'nullable|integer',
            'distance_unit' => 'nullable|in:kms,miles',
            'match_params' => 'nullable|array',
            'age_from' => [
                'nullable',
                'integer',
            ],
            'age_to' => [
                'nullable',
                'integer',
            ],
        ];
        if($this->age_from && $this->age_to) {
            $rules['age_from'][] = 'gte:18';
            $rules['age_from'][] = 'lte:80';
            $rules['age_to'][] = 'lte:80';
            $rules['age_to'][] = 'gt:age_from';
        }
        return $rules;
    }
}
