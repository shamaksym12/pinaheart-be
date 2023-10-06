<?php

namespace App\Http\Requests\Admin\Coupon;

use App\Http\Requests\ApiRequest;

class Create extends ApiRequest
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
            'code' => 'required|string|max:256|unique:coupons,code',
            'count_days' => 'required|integer|gt:0',
            'max_uses' => 'required|integer|gt:0',
            'expired_at' => 'required|date',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if (now()->gt(toCarbon($this->expired_at))) {
                $validator->errors()->add('expired_at', 'Too old expired_at date.');
            }
        });
    }
}
