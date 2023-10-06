<?php

namespace App\Repositories;

use Carbon\Carbon;
use App\User;
use App\Photo;
use App\UserInfo;
use App\Activity;

class UserRepository
{
    public function firstByEmail(string $email)
    {
        return User::where('email', strtolower($email))->first();
    }

    public function create(array $data)
    {
        return User::create($data);
    }

    public function update(User $user, array $data)
    {
        $user->fill($data);
        $user->save();
        return $user;
    }

    public function getItemByHash(string $hash, string $type)
    {
        return User::whereHas('hashes', function($q) use($hash, $type){
            $q->where('hash', $hash);
            $q->where('type', $type);
        })->first();
    }

    public function addRandomHash(User $user, string $type, $expired_at = null)
    {
        $hash = $user->hashes()->firstOrNew([
            'type' => $type,
        ]);

        if ($hash->exists) {
            $user->hashes()->detach($hash->id);
        }

        $hash->hash = md5(str_random(10));
        $hash->expired_at = $expired_at;
        $user->hashes()->save($hash);

        return $hash->hash;
    }

    public function createUserPhoto(User $user, array $data)
    {
        return $user->photos()->create($data);
    }

    public function updatePhoto(Photo $photo, array $data)
    {
        $photo->fill($data)->save();
        return $photo;
    }

    public function setMainPhoto(User $user, Photo $photo)
    {
        $photo->update(['is_main' => true]);
        $user->photos()->where('id','<>',$photo->id)->update(['is_main' => false]);
        return $photo;
    }

    public function deletePhoto(Photo $photo)
    {
        return $photo->delete();
    }

    public function saveLocation(User $user, array $data)
    {
        $user->location ? $user->location()->update($data) : $user->location()->create($data);
        return $user;
    }

    public function saveInfo(User $user, array $data)
    {
        $user->info ? $user->info()->update($data) : $user->info()->create($data);
        return $user;
    }

    public function saveOff(User $user, array $data)
    {
        $user->off ? $user->off()->update($data) : $user->off()->create($data);
        return $user;
    }

    public function saveMatch(User $user, array $data)
    {
        $user->match ? $user->match()->update($data) : $user->match()->create($data);
        return $user;
    }

    public function saveInterest(User $user, array $data)
    {
        $user->interest ? $user->interest()->update($data) : $user->interest()->create($data);
        return $user;
    }

    public function savePersonality(User $user, array $data)
    {
        $user->personality ? $user->personality()->update($data) : $user->personality()->create($data);
        return $user;
    }

    public function saveNotifySetting(User $user, array $data)
    {
        $setting = $user->notifySettings()->firstOrCreate(array_only($data, ['type','name']));
        $setting->value = array_get($data, 'value');
        $setting->save();
        return $setting;
    }

    public function saveSearch(User $user, $name, array $data)
    {
        $search = $user->searches()->firstOrNew(['name' => $name]);
        $search->fill($data);
        $search->save();
        return $search;
    }

    public function saveAdminData(User $user, array $data)
    {
        $user->adminData ? $user->adminData()->update($data) : $user->adminData()->create($data);
        return $user;
    }

    public function getCountOnlineMembers()
    {
        return User::online()->count();
    }

    public function createStripeSubscriber(User $user, array $data)
    {
        return $user->stripeSubscriber()->create($data);
    }

    public function createStripeSubscription(User $user, array $data)
    {
        return $user->stripeSubscriptions()->create($data);
    }

    public function createStripePayment(User $user, array $data)
    {
        return $user->stripePayments()->create($data);
    }

    public function createPaypalSubscription(User $user, array $data)
    {
        return $user->paypalSubscriptions()->create($data);
    }

    public function getUserHasMessagesAfter(Carbon $date, array $wArray)
    {
        $users = User::whereHas('notifySettings', function($q) use($wArray){
            $q->where($wArray);
        })
        ->whereHas('inboxUnreadMessages', function($q) use ($date){
            $q->where('created_at', '>', $date);
        })
        ->with(['inboxUnreadMessages' => function($q) use($date){
            $q->where('created_at', '>', $date);
            $q->with('dialog.sender.location');
        }])
        ->get();
        return $users;
    }

    public function getUserHasActivitiesAfter(Carbon $date, array $wArray)
    {
        $users = User::whereHas('notifySettings', function($q) use($wArray){
            $q->where($wArray);
        })
        ->whereHas('inboxActivityUsers', function($q) use ($date){
            $q->whereNotIn('activities.type', [Activity::TYPE_ADD_TO_BLOCK, Activity::TYPE_REMOVE_FROM_BLOCK]);
            $q->where('activities.created_at', '>', $date);
        })
        ->with(['inboxActivityUsers' => function($q) use($date){
            $q->where('activities.created_at', '>', $date);
            $q->with('location');
        }])
        ->get();

        return $users;
    }
}
