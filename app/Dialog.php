<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dialog extends Model
{
    protected $fillable = [
        'active',
        'from',
        'to',
        'deleted_for',
    ];

    /**Start Relations */
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class)->orderBy('created_at', 'desc');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'from');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'to');
    }
    /**End Relations */

    /**Start Scopes*/
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeForUsers($query, User $user1, User $user2)
    {
        return $query->where(function($q) use($user1, $user2){
            $q->where('from', $user1->id)->where('to', $user2->id);
        })->orWhere(function($q) use($user1, $user2){
            $q->where('to', $user1->id)->where('from', $user2->id);
        });
    }

    public function scopeUser($query, User $user)
    {
        return $query->where(function($q) use($user){
            $q->where('from', $user->id);
            $q->orWhere('to', $user->id);
        });
    }
    /**End Scopes */

    /**Start Mutators*/
    public function getIsDeletedAttribute()
    {
        return ! is_null($this->deleted_for);
    }
    /**End Mutators */

    /**Start Helper*/
    public function isActive()
    {
        return (bool) $this->active;
    }

    public function isUser(User $user)
    {
        return $this->from == $user->id || $this->to == $user->id;
    }

    public function isFromUser(User $user)
    {
        return $this->from == $user->id;
    }

    public function isToUser(User $user)
    {
        return $this->to == $user->id;
    }

    public function hasPaidUser()
    {
        if($this->relationLoaded('sender') && $this->relationLoaded('recipient')) {
            return (bool) (optional($this->sender)->is_paid || optional($this->recipient)->is_paid);
        }
        return false;
    }

    public function setHasPaidUser()
    {
        $this->attributes['has_paid_user'] = $this->hasPaidUser();
    }

    public function setUserFor(User $user)
    {
        if($this->relationLoaded('sender') && $this->relationLoaded('recipient')) {
            if($this->to == $user->id) {
                $this->setRelation('user', $this->sender);
            } else {
                $this->setRelation('user', $this->recipient);
            }
            $this->setRelation('user', ($this->to == $user->id) ? $this->sender : $this->recipient);
        } else {
            $this->setRelation('user', new User());
        }
        $this->unsetRelation('sender');
        $this->unsetRelation('recipient');
    }
    /**End Helper*/
}
