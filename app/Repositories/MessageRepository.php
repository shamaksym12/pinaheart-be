<?php

namespace App\Repositories;

use App\User;
use App\Dialog;
use Illuminate\Database\Eloquent\Collection;

class MessageRepository
{
    public function findDialogForUsers(User $me, User $user)
    {
        return Dialog::forUsers($me, $user)->first();
    }

    public function getUserDialogs(User $user)
    {
        return Dialog::active()
            ->where(function($q) use($user){
                $q->whereNull('deleted_for')->orWhere('deleted_for', '<>', $user->id);
            })
            ->where(function($q) use($user){
                $q->where('from', $user->id)->orWhere('to', $user->id);
            })
            ->with([
                'sender' => function($q) use($user){
                    $q->forDialog($user);
                },
                'recipient' => function($q) use($user){
                    $q->forDialog($user);
                },
                'lastMessage'
            ])
            ->orderBy('updated_at','desc')
            ->get();
    }

    public function updateDialog(Dialog $dialog, array $data)
    {
        $dialog->fill($data)->save();
        return $dialog;
    }

    public function createDialog(array $data)
    {
        return Dialog::create($data);
    }

    public function createDialogMesage(Dialog $dialog, array $data)
    {
        return $dialog->messages()->create($data);
    }

    public function deleteDialogs(Collection $dialogs, User $user)
    {
        $dialogs->each(function($item) use($user){
            $item->messages()->delete();
            if($item->is_deleted) {
                $item->delete();
            } else {
                $item->update(['deleted_for' => $user->id]);
            }
        });
    }

    public function forceDeleteDialogs(Collection $dialogs)
    {
        $dialogs->each(function($item) {
            $item->messages()->delete();
            $item->delete();
        });
    }

}
