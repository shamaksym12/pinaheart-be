<?php

namespace App\Http\Requests\Client\Profile;

use App\Http\Requests\ApiRequest;
use App\UserNotifySetting;

class SetNotifySettings extends ApiRequest
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
        $types = (new UserNotifySetting())->getTypes();
        $names = (new UserNotifySetting())->getNames();
        $values = (new UserNotifySetting())->getValues();
        $rules = [
            'settings' => 'required|array',
            'settings.*.type' => 'required|string|in:'.implode(',', $types),
            'settings.*.name' => 'required|string|in:'.implode(',', $names),
            'settings.*.value' => 'required|string|in:'.implode(',', $values),
        ];
        return $rules;
    }

}
