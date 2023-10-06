<?php

namespace App\Http\Requests\Client\Profile;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class SetPassword extends ApiRequest
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
            'old_password' => 'required|string',
            'password' => 'required|confirmed',
        ];
    }

    public function messages()
    {
        return [
            'password.confirmed' => 'Check your new password, please',
        ];
    }
}
