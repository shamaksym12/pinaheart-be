<?php

namespace App\Http\Requests\Client\Profile;

use App\Http\Requests\ApiRequest;

class SetInfo extends ApiRequest
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
            'looking' => 'required|string',
            'about' => 'required|string',
            'heading' => 'required|string|max:256',
        ];
    }

    public function messages()
    {
        return [
            'looking.required' => 'Please fill in who you\'re looking for',
            'about.required' => 'Please fill in something about yourself',
            'heading.required' => 'Please fill in profile heading',
        ];
    }

}
