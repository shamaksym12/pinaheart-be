<?php

namespace App\Http\Requests\Client\Profile;

use App\Http\Requests\ApiRequest;

class SetProfileParams extends ApiRequest
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
        $rules = [];
        foreach ($this->keys() as $key) {
            $rules[$key] = ['required'];
            switch ($key) {
                case 'first_name':
                    $rules[$key] = 'required|string|max:256';
                    break;
                case 'country_id':
                    $rules[$key] = 'required|integer';
                    break;
                case 'place':
                    $rules[$key] = 'nullable|array';
                    break;
                case 'dob_day':
                    $rules[$key] = 'required|date_format:d';
                    break;
                case 'dob_month':
                    $rules[$key] = 'required|date_format:m';
                    break;
                case 'dob_year':
                    $rules[$key] = 'required|date_format:Y';
                    break;
                case 'sex':
                    $rules[$key] = 'required|in:M,F';
                    break;
                case 'heading':
                    $rules[$key] = 'required|string|max:256';
                    break;
                case 'about':
                    $rules[$key] = 'required|string';
                    break;
                case 'looking':
                    $rules[$key] = 'required|string';
                    break;
                default:
                    $rules[$key] = 'required';
                    break;
            }
        }
        return $rules;
    }

    public function messages()
    {
        return [
            '*' => '',
        ];
    }
}
