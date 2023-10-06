<?php

namespace App\Http\Requests\Client\Payment;

use App\Http\Requests\ApiRequest;

class PaypallCreate extends ApiRequest
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
            'orderID' => 'required|string',
            'subscriptionID' => 'required|string',
        ];
    }
}
