<?php

namespace App\Http\Resources\Client\Person;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ListPaginateCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public $collects = ListItem::class;


    public function toArray($request)
    {
        return $this->resource;
    }
}
