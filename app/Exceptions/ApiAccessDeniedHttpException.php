<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Contracts\Validation\Validator;

class ApiAccessDeniedHttpException extends Exception
{
    protected $code = 403;

    protected $message = 'Access Denied';

    public function __construct()
    {
        parent::__construct();
    }

    public function render()
    {
        return response()->error($this->message, $this->code);
    }

    public function withCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function withMessage($message)
    {
        $this->message = __($message);
        return $this;
    }
}