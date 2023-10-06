<?php

namespace App\Http\Requests\Client\Auth;

use App\Coupon;
use App\Http\Requests\ApiRequest;
use App\Repositories\CouponRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class Register extends ApiRequest
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
            'first_name' => 'required|string|max:256',
            'last_name' => 'nullable|string|max:256',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string',
            'sex' => 'required|in:M,F',
            'age' => 'required|integer|gte:18|lte:80',
            'coupon' => [
                'nullable',
                'string',
                'max:256',
                Rule::exists('coupons', 'code'),
            ],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if($couponCode = $this->coupon) {
                $coupon = app()->make(CouponRepository::class)->firstByCode($couponCode);
                if( ! $coupon) {
                    $validator->errors()->add('coupon', 'Coupon not found');
                } else {
                    if( ! $coupon->isActive()) {
                        $validator->errors()->add('coupon', 'Coupon not active');
                    }
                    if( $coupon->isExpired()) {
                        $validator->errors()->add('coupon', 'Coupon expired');
                    }
                    if( $coupon->isFull()) {
                        $validator->errors()->add('coupon', 'Coupon limit reached');
                    }
                }
            }
        });
    }
}
