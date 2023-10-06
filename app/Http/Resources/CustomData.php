<?php
namespace App\Http\Resources;

trait CustomData
{
    public $customData;

    public function withCustomData($data)
    {
        foreach($data as $key => $items) {
            $this->{$key} = $items;
        }

        return $this;
    }

    public function addData($data)
    {
        foreach($data as $key => $items) {
            $this->customData[$key] = $items;
        }

        return $this;
    }

    public function customMerge()
    {
        $this->merge($this->customData);
    }

    public function customGet(string $key, $default = null)
    {
        return array_get($this->customData, $key, $default);
    }
}