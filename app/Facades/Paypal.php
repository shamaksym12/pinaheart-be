<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

class Paypal extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \App\Interactions\Paypal::class;
    }
}