<?php

namespace App\Http\Requests\Client\Profile;

use App\Http\Requests\ApiRequest;

class AddPhoto extends ApiRequest
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
            'photo' => 'required|image|max:20128',
        ];
    }

    public function messages()
    {
        return [
            'photo.max' => 'File size should be :max kb max',
        ];
    }
}
