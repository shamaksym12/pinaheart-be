<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use App\Exceptions\ApiValidationException;
use App\Exceptions\ApiAccessDeniedHttpException;

abstract class ApiRequest extends FormRequest
{
    protected function failedValidation(Validator $validator)
    {
        throw (new ApiValidationException())->withValidator($validator);
    }

    protected function failedAuthorization()
    {
        throw new ApiAccessDeniedHttpException();
    }
}