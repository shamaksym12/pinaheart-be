<?php
namespace App\Collections;

use Illuminate\Database\Eloquent\Collection;

class MessageCollection extends Collection
{
    public function markAsRead()
    {
        $this->each(function($item){
            $item->markAsRead();
        });
    }

    public function markAsPaid(bool $value)
    {
        $this->each(function($item) use($value){
            $item->markAsPaid($value);
        });
    }
}