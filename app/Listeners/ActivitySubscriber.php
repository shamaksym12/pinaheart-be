<?php

namespace App\Listeners;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use App\Services\Client\ProfileService;
use App\Notifications\Auth\Recovery as AuthRecoveryNotification;
use App\Activity;

class ActivitySubscriber implements ShouldQueue
{
    public function __construct(ProfileService $profileService)
    {
        $this->profileService = $profileService;
    }

    public function onViewedShortProfile($event)
    {
        if ($event->who->isAdmin()) {
            return false;
        }
        
        if($event->who->id != $event->whom->id) {
            $activity = Activity::latest()->firstOrNew([
                'who_id' => $event->who->id,
                'whom_id' => $event->whom->id,
                'type' => Activity::TYPE_VIEW,
            ]);
            if( ! $activity->exists) {
                $activity->save();
            } else {
                if(now()->diffInMinutes($activity->created_at) > 60) {
                    $activity->replicate()->save();
                } else {
                    $activity->touch();
                }
            }
        }
    }

    public function onChangeFavorites($event)
    {
        if($event->who->id != $event->whom->id) {
            $activity = new Activity([
                'who_id' => $event->who->id,
                'whom_id' => $event->whom->id,
            ]);
            if( $event->added) {
                $activity->type = Activity::TYPE_ADD_TO_FAVORITE;
            } else {
                $activity->type = Activity::TYPE_REMOVE_FROM_FAVORITE;
            }
            $activity->save();
        }
    }

    public function onChangeInterest($event)
    {
        if($event->who->id != $event->whom->id) {
            $activity = new Activity([
                'who_id' => $event->who->id,
                'whom_id' => $event->whom->id,
            ]);
            if( $event->added) {
                $activity->type = Activity::TYPE_ADD_TO_INTEREST;
            } else {
                $activity->type = Activity::TYPE_REMOVE_FROM_INTEREST;
            }
            $activity->save();
        }
    }

    public function onChangeBlock($event)
    {
        if($event->who->id != $event->whom->id) {
            $activity = new Activity([
                'who_id' => $event->who->id,
                'whom_id' => $event->whom->id,
            ]);
            if( $event->added) {
                $activity->type = Activity::TYPE_ADD_TO_BLOCK;
            } else {
                $activity->type = Activity::TYPE_REMOVE_FROM_BLOCK;
            }
            $activity->save();
        }
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  Illuminate\Events\Dispatcher  $events
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\Person\ViewedShortProfile',
            'App\Listeners\ActivitySubscriber@onViewedShortProfile'
        );
        $events->listen(
            'App\Events\Person\ChangeFavorites',
            'App\Listeners\ActivitySubscriber@onChangeFavorites'
        );
        $events->listen(
            'App\Events\Person\ChangeInterest',
            'App\Listeners\ActivitySubscriber@onChangeInterest'
        );
        $events->listen(
            'App\Events\Person\ChangeBlock',
            'App\Listeners\ActivitySubscriber@onChangeBlock'
        );
    }
}
