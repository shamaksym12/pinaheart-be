<?php

namespace App\Http\Requests\Client\Profile;

use App\Http\Requests\ApiRequest;
use Illuminate\Validation\Rule;

class SetEmail extends ApiRequest
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
        $me = auth()->user();
        return [
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($me->id),
            ]
        ];
    }

    public function messages()
    {
        return [
            'email.unique' => 'This email already exists',
        ];
    }
}
