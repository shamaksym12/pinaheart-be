<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    protected $fillable = [
        'staff_id',
        'action',
        'target_id',
    ];

    const ACTION_BLOCKED = 'blocked';
    const ACTION_UNBLOCKED = 'unblocked';
    const ACTION_COMMENTED = 'commented';
    const ACTION_CHANGE_EMAIL = 'change_email';
    const ACTION_CHANGE_PASS = 'change_password';

    public function staff() {
        return $this->hasOne(User::class, 'id', 'staff_id');
    }

    public function target() {
        return $this->hasOne(User::class, 'id', 'target_id');
    }
}
