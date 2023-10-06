<?php

namespace App\Http\Requests\Client\Profile;

use App\Http\Requests\ApiRequest;

class SetOff extends ApiRequest
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
            'reason' => 'nullable|string|max:256',
            'password' => 'required|string',
        ];
    }

}
