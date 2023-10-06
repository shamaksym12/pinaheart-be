<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Collections\MessageCollection;
use Illuminate\Support\Facades\DB;

class Message extends Model
{
    protected $fillable = [
        'dialog_id',
        'is_from',
        'is_paid',
        'text',
        'read_at',
    ];

    public function newCollection(array $models = [])
    {
        return new MessageCollection($models);
    }

    protected $casts = [
        'is_paid' => 'boolean',
    ];

    protected $touches = ['dialog'];

    /**Start Relations */
    public function dialog()
    {
        return $this->belongsTo(Dialog::class);
    }
    /**End Relations */

    /**Start Scopes*/
    public function scopeWithMy($query, bool $from)
    {
        return $from ? $query->select('*', DB::raw('is_from as my')) : $query->select('*', DB::raw(' ! is_from as my'));
    }

    public function scopeNotHavePaidUserInDialog($query)
    {
        return $query->where(function($q){
            $q->whereDoesntHave('dialog.recipient', function($q){
                $q->where('is_paid', true);
            });
            $q->whereDoesntHave('dialog.sender', function($q){
                $q->where('is_paid', true);
            });
        });
    }
    /**End Scopes */

    /**Start Mutators*/
    public function getIsReadAttribute()
    {
        return ! is_null($this->read_at);
    }

    public function getTextMaskedAttribute()
    {
        return preg_replace('/\S/', '*', $this->text);
    }
    /**End Mutators */

    /**Start Helper*/
    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }

    public function markAsPaid(bool $value)
    {
        $this->forceFill(['is_paid' => $value]);
    }
    /**End Helper*/
}
