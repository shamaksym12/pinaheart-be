<?php
if (! function_exists('customThrow')) {

    function customThrow($message = '', $code = 422)
    {
        throw (new \App\Exceptions\ApiCustomException())->withMessage(__($message))->withCode($code);
    }
}

if (! function_exists('customThrowIf')) {

    function customThrowIf($bolean, $message = '', $code = 422)
    {
        if($bolean) {
            customThrow($message, $code);
        }
    }
}

if ( ! function_exists('storageUrl')) {

    function storageUrl($shortPath)
    {
        return ! empty($shortPath) ? \Illuminate\Support\Facades\Storage::url($shortPath) : null;
    }
}
if ( ! function_exists('randomInteger')) {

    function randomInteger($digit = 4)
    {
        $max = str_repeat('9', $digit);
        return str_pad(mt_rand(1,$max),$digit,mt_rand(1,9),STR_PAD_LEFT);
    }
}

if ( ! function_exists('toCarbon')) {

    function toCarbon($string)
    {
        if ( ! $string) {
            return null;
        }

        try {
            return new \Carbon\Carbon($string);
        } catch (\Exception $e) {
            return null;
        }
    }
}

if ( ! function_exists('frontUrl')) {

    function frontUrl($uri = '')
    {
        return str_finish(config('app.front_url'),'/').$uri;
    }
}
